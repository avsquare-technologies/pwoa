<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category')
            ->where('status', 'published');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $events = $query->orderBy('starts_at')->get();

        return view('frontend.events.index', [
            'upcomingEvents' => $events->filter(fn($e) => $e->starts_at->isFuture()),
            'pastEvents' => $events->filter(fn($e) => $e->starts_at->isPast())
                ->sortByDesc('starts_at'),
        ]);
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        return view('frontend.events.show', compact('event'));
    }

    public function purchase(string $slug, Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $event = Event::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        $qty = (int) $request->input('quantity', 1);

        if (!$user->wallet) {
            return back()->with('error', 'You must have a wallet to purchase tickets. Please visit your Wallet dashboard first.');
        }

        // 1. Check Capacity & Availability dynamically
        $soldOrMintingCount = \App\Models\EventTicket::where('event_id', $event->id)
            ->where(function ($q) {
                $q->whereIn('status', ['sold', 'minting'])
                  ->orWhere(function ($sq) {
                      $sq->where('status', 'minted')
                         ->whereNotNull('user_id');
                  });
            })
            ->count();

        $availableSeats = $event->capacity !== null ? max(0, $event->capacity - $soldOrMintingCount) : 9999;

        if ($availableSeats < $qty) {
            return back()->with('error', "Sorry, only {$availableSeats} seats are currently available.");
        }

        // 2. Fetch or prepare metadata / TicketBatch on the fly
        $batch = \App\Models\TicketBatch::where('event_id', $event->id)->first();
        if (!$batch) {
            try {
                if (!$event->image_path) {
                    throw new \Exception("Event must have an image to prepare metadata for ticket minting.");
                }

                $pinata = app(\App\Services\PinataService::class);
                $imagePath = $event->image_path;
                $isUrl = str_starts_with($imagePath, 'http');
                $tempFile = null;

                if ($isUrl) {
                    $tempFile = tempnam(sys_get_temp_dir(), 'nft_');
                    $imageContent = file_get_contents($imagePath);
                    if (!$imageContent) {
                        throw new \Exception("Failed to download image from URL: {$imagePath}");
                    }
                    file_put_contents($tempFile, $imageContent);
                    $uploadPath = $tempFile;
                } else {
                    $disk = \Storage::disk('public')->exists($imagePath) ? 'public' : config('filesystems.default');
                    $uploadPath = \Storage::disk($disk)->path($imagePath);
                }

                if (!file_exists($uploadPath)) {
                    throw new \Exception("Event image file not found on disk at: {$uploadPath}");
                }

                try {
                    $imageIpfsHash = $pinata->uploadFile($uploadPath);
                } finally {
                    if ($tempFile && file_exists($tempFile)) {
                        unlink($tempFile);
                    }
                }

                $ticketPriceUsd = (float) ($event->price ?? 0);

                $metadata = [
                    'name' => "{$event->title} NFT Ticket",
                    'description' => trim(strip_tags(html_entity_decode($event->description))),
                    'image' => $imageIpfsHash,
                    'external_url' => config('app.url') . '/events/' . $event->slug,
                    'attributes' => [
                        ['trait_type' => 'Event Name', 'value' => $event->title],
                        ['trait_type' => 'Venue', 'value' => $event->location ?? 'Virtual'],
                        ['trait_type' => 'Category', 'value' => $event->category?->name ?? 'Uncategorized'],
                        ['trait_type' => 'Start Date', 'value' => $event->starts_at->toDateTimeString()],
                        ['trait_type' => 'Price (USD)', 'value' => $ticketPriceUsd],
                        ['trait_type' => 'Ticket Type', 'value' => 'NFT Access Pass'],
                    ]
                ];
                $metadataIpfsHash = $pinata->uploadJson($metadata);

                $washToUsd = config('services.xrpl.wash_to_usd', 0.05);
                $priceWash = $ticketPriceUsd / ($washToUsd > 0 ? $washToUsd : 0.05);
                $batchId = Str::uuid()->toString();

                $batch = \App\Models\TicketBatch::create([
                    "event_id" => $event->id,
                    "creator_id" => config('services.xrpl.admin_user_id', 1),
                    "batch_id" => $batchId,
                    "total" => $event->capacity ?? 100,
                    "minted" => 0,
                    "failed" => 0,
                    "next_index" => 1,
                    "metadata_uri" => $metadataIpfsHash,
                    "price" => $priceWash,
                    "status" => "active"
                ]);
            } catch (\Throwable $e) {
                Log::error('On-Demand Metadata Prep Error: ' . $e->getMessage());
                return back()->with('error', 'Failed to prepare ticket metadata: ' . $e->getMessage());
            }
        }

        $washToUsd = (float) config('services.xrpl.wash_to_usd', 0.05);
        $priceWash = $event->isFreeFor($user) ? 0.0 : ((float) $event->price / ($washToUsd > 0 ? $washToUsd : 0.05));
        $totalAmount = $priceWash * $qty;

        // Defensive guard: prevent free purchase if the event has a non-zero price and is not free for the user
        if ($totalAmount <= 0 && $event->price > 0 && !$event->isFreeFor($user)) {
            return back()->with('error', "Invalid price calculation: Non-free event cannot have a zero payment amount.");
        }

        // Check user balance
        $walletService = app(\App\Services\PublicWalletService::class);
        if (!$walletService->hasSufficientBalance($user->wallet, $totalAmount)) {
            return back()->with('error', "Insufficient \$WASH balance. You need " . number_format($totalAmount, 2) . " \$WASH for {$qty} tickets.");
        }

        // Keep track of reserved tickets and transfers for failure handling
        $reservedTickets = [];
        $reservedTransfers = [];
        $order = null;

        try {
            // 3. Create Ticket Order
            $order = \App\Models\TicketOrder::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'quantity' => $qty,
                'total_amount' => $totalAmount,
                'status' => \App\Enums\OrderStatus::PROCESSING,
            ]);

            // 4. Reserve Tickets in DB Transaction (Locking Batch to ensure transaction safety)
            DB::beginTransaction();
            try {
                $lockedBatch = \App\Models\TicketBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

                // Re-evaluate seats remaining with lock
                $soldOrMintingCount = \App\Models\EventTicket::where('event_id', $event->id)
                    ->where(function ($q) {
                        $q->whereIn('status', ['sold', 'minting'])
                          ->orWhere(function ($sq) {
                              $sq->where('status', 'minted')
                                 ->whereNotNull('user_id');
                          });
                    })
                    ->count();
                $availableSeats = $event->capacity !== null ? max(0, $event->capacity - $soldOrMintingCount) : 9999;

                if ($availableSeats < $qty) {
                    throw new \Exception('Sorry, the event has just sold out.');
                }

                // Create $qty tickets with 'minting' status
                for ($i = 0; $i < $qty; $i++) {
                    $ticketNumber = \App\Models\EventTicket::where('event_id', $event->id)->count() + 1;
                    $ticket = \App\Models\EventTicket::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'batch_id' => $batch->batch_id,
                        'order_id' => $order->id,
                        'ticket_number' => $ticketNumber,
                        'status' => 'minting',
                    ]);
                    $reservedTickets[] = $ticket;

                    // Create Transfer Record
                    $transfer = \App\Models\TicketTransfer::create([
                        'order_id' => $order->id,
                        'ticket_id' => $ticket->id,
                        'status' => \App\Enums\TransferStatus::PENDING,
                    ]);
                    $reservedTransfers[] = $transfer;
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

            // 5. Fetch admin wallet for minting & payment
            $adminUserId = config('services.xrpl.admin_user_id', 1);
            $adminWallet = \App\Models\Wallet::where('user_id', $adminUserId)->first();
            if (!$adminWallet) {
                throw new \Exception("Admin wallet not configured.");
            }

            // 6. Execute NFT Minting and Buy/Transfer loop (1 mint per ticket)
            $successCount = 0;
            $firstAttendee = null;

            foreach ($reservedTickets as $index => $ticket) {
                $transfer = $reservedTransfers[$index];
                $nftTokenId = null;
                $ticketSeq = null;

                try {
                    // Mint the NFT to the Admin wallet with price 0
                    $mintResponse = app(\App\Services\PrivatePaymentService::class)->mintBatchNft(
                        $adminWallet->address,
                        $batch->metadata_uri,
                        1,
                        $event->id,
                        0
                    );

                    if (empty($mintResponse['success'])) {
                        throw new \Exception($mintResponse['error'] ?? 'NFT Minting failed.');
                    }

                    $ticketsData = $mintResponse["tickets"] ?? $mintResponse["data"]["tickets"] ?? [];
                    $ticketData = $ticketsData[0] ?? null;
                    if (!$ticketData || empty($ticketData['nft_token_id'])) {
                        throw new \Exception("Failed to retrieve minted NFT token ID from payment service.");
                    }

                    $nftTokenId = $ticketData["nft_token_id"];
                    $mintTxHash = $ticketData["tx_hash"];
                    $ticketSeq = $ticketData["ticket_sequence"] ?? $ticketData["sequence_used"] ?? null;

                    // Execute purchase / transfer (buyTicket) on XRPL
                    $buyResponse = app(\App\Services\PublicWalletService::class)->buyTicket(
                        $user->wallet->address,
                        $adminWallet->address,
                        $nftTokenId,
                        $priceWash
                    );

                    if (empty($buyResponse['success'])) {
                        throw new \Exception("XRPL Purchase failed: " . ($buyResponse['message'] ?? $buyResponse['error'] ?? 'Unknown purchase error'));
                    }

                    $purchaseTxHash = $buyResponse['tx_hash'] ?? ($buyResponse['data']['tx_hash'] ?? null);

                    // 7. Update ticket & transfer on success
                    $ticket->update([
                        'owner_wallet_address' => $user->wallet->address,
                        'nft_token_id' => $nftTokenId,
                        'tx_hash' => $purchaseTxHash,
                        'ticket_seq' => $ticketSeq,
                        'status' => 'sold',
                    ]);

                    $transfer->update([
                        'status' => \App\Enums\TransferStatus::SUCCESS,
                        'tx_hash' => $purchaseTxHash,
                    ]);

                    // Create Attendee Record
                    $attendeeTicketId = "PWOA-EVT-{$event->id}-" . str_pad($ticket->ticket_number, 5, '0', STR_PAD_LEFT);

                    $attendee = \App\Models\EventAttendee::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'ticket_id' => $attendeeTicketId,
                        'token' => Str::random(32),
                        'status' => 'valid',
                        'expires_at' => $event->ends_at ?? $event->starts_at->addHours(4),
                    ]);

                    if (!$firstAttendee) {
                        $firstAttendee = $attendee;
                    }

                    if ($batch->minted < $batch->total) {
                        $batch->increment('minted');
                    }
                    $successCount++;

                } catch (\Throwable $mintEx) {
                    Log::error("NFT Minting/Transfer loop failed at ticket #{$ticket->id}: " . $mintEx->getMessage());
                    
                    // Rollback/recovery: Update the failed ticket with the nft_token_id if it was successfully generated
                    // So we do not lose track of the minted NFT in the admin wallet.
                    if (!empty($nftTokenId)) {
                        $ticket->update([
                            'nft_token_id' => $nftTokenId,
                            'status' => 'failed',
                        ]);
                        $transfer->update([
                            'status' => \App\Enums\TransferStatus::FAILED,
                            'error_message' => $mintEx->getMessage()
                        ]);
                    }

                    // Mark this and all subsequent tickets as failed
                    for ($k = $index; $k < count($reservedTickets); $k++) {
                        if ($k === $index && !empty($nftTokenId)) {
                            continue;
                        }
                        $reservedTickets[$k]->update(['status' => 'failed']);
                        $reservedTransfers[$k]->update([
                            'status' => \App\Enums\TransferStatus::FAILED,
                            'error_message' => $mintEx->getMessage()
                        ]);
                    }
                    break; // stop minting loop
                }
            }

            // Update order status based on success count
            if ($successCount === $qty) {
                $order->update(['status' => \App\Enums\OrderStatus::COMPLETED]);
                $redirectRoute = $qty === 1 
                    ? redirect()->route('events.ticket', [$event->slug, $firstAttendee->ticket_id])
                    : redirect()->route('events.order', [$event->slug, $order->id]);
                return $redirectRoute->with('success', "Successfully purchased ticket" . ($qty > 1 ? "s!" : "!"));
            } elseif ($successCount > 0) {
                $order->update(['status' => \App\Enums\OrderStatus::PARTIAL]);
                $redirectRoute = $successCount === 1
                    ? redirect()->route('events.ticket', [$event->slug, $firstAttendee->ticket_id])
                    : redirect()->route('events.order', [$event->slug, $order->id]);
                return $redirectRoute->with('warning', "Purchased {$successCount} of {$qty} tickets (some mints failed).");
            } else {
                throw new \Exception("All ticket mints failed.");
            }

        } catch (\Throwable $e) {
            Log::error('On-Demand Ticket Purchase Exception: ' . $e->getMessage());

            // Rollback all reserved items if nothing was successful (or during payment failure)
            foreach ($reservedTickets as $t) {
                if ($t->status === 'minting') {
                    $t->update(['status' => 'failed']);
                }
            }
            foreach ($reservedTransfers as $tr) {
                if ($tr->status === \App\Enums\TransferStatus::PENDING) {
                    $tr->update(['status' => \App\Enums\TransferStatus::FAILED, 'error_message' => $e->getMessage()]);
                }
            }
            if ($order) {
                $order->update(['status' => \App\Enums\OrderStatus::FAILED, 'error_message' => $e->getMessage()]);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function ticket(string $slug, string $ticketId)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $attendee = EventAttendee::where('ticket_id', $ticketId)
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('frontend.events.ticket', compact('event', 'attendee'));
    }

    public function ticketPdf(string $slug, string $ticketId)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $attendee = EventAttendee::where('ticket_id', $ticketId)
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('frontend.events.ticket_pdf', compact('event', 'attendee'));
        return $pdf->download("PWOA-Ticket-{$attendee->ticket_id}.pdf");
    }

    public function order(string $slug, string $orderId)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $order = \App\Models\TicketOrder::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['attendees', 'transfers'])
            ->firstOrFail();

        return view('frontend.events.order', compact('event', 'order'));
    }

    public function verifyTicket(Request $request)
    {
        $ticketId = $request->query('ticket_id');
        $token = $request->query('token');

        if (!$ticketId || !$token) {
            return response()->json(['error' => 'Missing ticket ID or token'], 400);
        }

        $attendee = EventAttendee::where('ticket_id', $ticketId)->first();

        if (!$attendee) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        if ($attendee->token !== $token) {
            return response()->json(['error' => 'Invalid token'], 403);
        }

        if ($attendee->status === 'used') {
            return response()->json(['error' => 'Ticket already used at ' . $attendee->checked_in_at->format('M d, Y h:i A')], 400);
        }

        if ($attendee->status === 'expired' || ($attendee->expires_at && $attendee->expires_at->isPast())) {
            $attendee->update(['status' => 'expired']);
            return response()->json(['error' => 'Ticket has expired'], 400);
        }

        $attendee->update([
            'status' => 'used',
            'checked_in_at' => now(),
        ]);

        return response()->json([
            'success' => 'Ticket verified successfully!',
            'attendee' => [
                'name' => $attendee->user->name,
                'event' => $attendee->event->title,
                'checked_in_at' => $attendee->checked_in_at->format('h:i A'),
            ]
        ]);
    }
}
