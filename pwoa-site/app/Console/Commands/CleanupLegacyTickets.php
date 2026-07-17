<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\EventTicket;
use App\Models\TicketBatch;
use Illuminate\Support\Facades\Storage;

class CleanupLegacyTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:cleanup-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up unused legacy tickets for Event 26 and recalculate TicketBatch counters';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eventId = 26;
        $event = Event::find($eventId);

        if (!$event) {
            $this->error("Event 26 not found in database.");
            return 1;
        }

        $this->info("Starting legacy ticket cleanup for Event: {$event->title} (ID: {$event->id})");

        // 1. Locate affected tickets before deletion
        $legacyTickets = EventTicket::where('event_id', $eventId)
            ->where('status', 'minted')
            ->whereNull('user_id')
            ->get();

        $count = $legacyTickets->count();
        $this->info("Found {$count} legacy ticket records to remove.");

        // 2. Generate backup report
        $backup = [
            'timestamp' => now()->toDateTimeString(),
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'capacity' => $event->capacity,
            ],
            'tickets_count' => $count,
            'tickets' => $legacyTickets->map(fn($t) => [
                'id' => $t->id,
                'ticket_number' => $t->ticket_number,
                'nft_token_id' => $t->nft_token_id,
                'tx_hash' => $t->tx_hash,
                'status' => $t->status,
                'created_at' => $t->created_at?->toDateTimeString(),
            ])->toArray(),
        ];

        Storage::disk('local')->put('legacy_tickets_backup.json', json_encode($backup, JSON_PRETTY_PRINT));
        $this->info("Backup report saved to storage/app/legacy_tickets_backup.json");

        // 3. Delete records
        EventTicket::where('event_id', $eventId)
            ->where('status', 'minted')
            ->whereNull('user_id')
            ->delete();
        $this->info("Successfully deleted {$count} unused legacy records.");

        // 4. Recalculate TicketBatch statistics
        $batch = TicketBatch::where('event_id', $eventId)->first();
        if ($batch) {
            $this->info("Found TicketBatch with total capacity: {$batch->total}");
            $this->info("Current batch minted count in DB: {$batch->minted}");

            // Recalculate actual sold & minted tickets count
            $actualSold = EventTicket::where('event_id', $eventId)
                ->where(function($q) {
                    $q->whereIn('status', ['sold', 'minting'])
                      ->orWhere(function($sq) {
                          $sq->where('status', 'minted')
                             ->whereNotNull('user_id');
                      });
                })
                ->count();

            $batch->update(['minted' => $actualSold]);
            $this->info("Recalculated TicketBatch minted count to: {$actualSold}");
        } else {
            $this->warn("No TicketBatch found for Event 26 to update.");
        }

        $this->info("Legacy cleanup operation completed successfully.");
        return 0;
    }
}
