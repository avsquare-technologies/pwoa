<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\TicketBatch;
use App\Models\EventTicket;
use App\Services\PublicWalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MintChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 10;
    public int $timeout = 600;

    public function __construct(
        public string $batchId,
        public int $chunkSize = 2
    ) {}

    public function handle(PublicWalletService $walletService)
    {
        $batch = DB::transaction(function () {
            $batch = TicketBatch::where("batch_id", $this->batchId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($batch->status === "completed") {
                return null;
            }

            $remaining = $batch->total - $batch->minted;

            if ($remaining <= 0) {
                $batch->update(["status" => "completed", "last_heartbeat" => now()]);
                return null;
            }

            $batch->update(["last_heartbeat" => now()]);

            return $batch;
        });

        if (!$batch) {
            return;
        }

        $remaining = $batch->total - $batch->minted;
        $mintCount = min($this->chunkSize, $remaining);

        Log::info("🧩 Mint Chunk Started for Event", [
            "batch"     => $batch->batch_id,
            "mintCount" => $mintCount,
            "progress"  => "{$batch->minted}/{$batch->total}"
        ]);

        try {
            // Use admin user ID for minting
            $creatorId = config('services.xrpl.admin_user_id', 1);

            $response = $walletService->mintBatchNft(
                $creatorId,
                $batch->metadata_uri,
                $mintCount,
                $batch->event_id,
                $batch->price
            );

            if (empty($response["success"])) {
                $error = $response["error"] ?? "Mint failed";

                Log::error("❌ Chunk Mint Request Failed", [
                    "batch" => $batch->batch_id,
                    "error" => $error,
                    "response" => $response
                ]);

                if (
                    str_contains($error, "meta not found") ||
                    str_contains($error, "504") ||
                    str_contains($error, "timeout")
                ) {
                    Log::warning("⏳ Temporary Mint Error, retrying...", [
                        "batch" => $batch->batch_id,
                        "error" => $error
                    ]);

                    $this->release(10);
                    return;
                }

                if ($error === "insufficient_funds") {
                    Log::error("💰 Wallet has no funds", [
                        "batch" => $batch->batch_id
                    ]);
                    return;
                }

                throw new \Exception($error);
            }

            // Correctly extract tickets from the top-level response
            $tickets = $response["tickets"] ?? $response["data"]["tickets"] ?? [];

            Log::info("✅ (v2) Received Tickets from Private API", [
                "batch" => $batch->batch_id,
                "count" => count($tickets),
                "processed_count" => $response["processed"] ?? 0
            ]);

            if (count($tickets) === 0) {
                throw new \Exception("Chunk returned 0 tickets despite success flag");
            }

        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            // If it's a timeout, retry instead of failing immediately
            if (str_contains($errorMessage, 'timed out') || str_contains($errorMessage, 'cURL error 28')) {
                Log::warning("⏳ Network/Server Timeout, retrying chunk...", [
                    "batch" => $batch->batch_id,
                    "error" => $errorMessage
                ]);

                $this->release(30); // Wait 30 seconds before retry
                return;
            }

            Log::error("❌ Mint API Failed", [
                "batch" => $batch->batch_id,
                "error" => $errorMessage,
            ]);

            DB::transaction(function () use ($batch, $e) {
                TicketBatch::where("batch_id", $batch->batch_id)
                    ->lockForUpdate()
                    ->update([
                        "status" => "failed",
                        "error" => $e->getMessage(),
                        "updated_at" => now()
                    ]);
            });

            throw $e;
        }

        /**
         * STEP 3: Save minted tickets safely
         */
        DB::transaction(function () use ($tickets) {
            $batch = TicketBatch::where("batch_id", $this->batchId)
                ->lockForUpdate()
                ->firstOrFail();

            $insert = [];

            foreach ($tickets as $ticket) {
                $insert[] = [
                    "event_id"             => $batch->event_id,
                    "batch_id"             => $batch->batch_id,
                    "ticket_number"        => $batch->next_index++,
                    "ticket_seq"           => $ticket["ticket_sequence"] ?? $ticket["sequence_used"] ?? null,
                    "nft_token_id"         => $ticket["nft_token_id"],
                    "tx_hash"              => $ticket["tx_hash"],
                    "owner_wallet_address" => $ticket["owner_wallet_address"] ?? null,
                    "status"               => "minted",
                    "created_at"           => now(),
                    "updated_at"           => now(),
                ];

                $batch->minted++;
            }

            DB::table("event_tickets")->insert($insert);

            if ($batch->minted >= $batch->total) {
                $batch->status = "completed";
                $batch->save();

                Log::info("🎉 Event Batch Completed Fully", [
                    "batch"  => $batch->batch_id,
                    "minted" => $batch->minted
                ]);

                return;
            }

            $batch->save();

            Log::info("✅ Event Chunk Saved Successfully", [
                "batch"     => $batch->batch_id,
                "minted"    => $batch->minted,
                "remaining" => $batch->total - $batch->minted
            ]);
        });

        /**
         * STEP 4: Dispatch NEXT chunk
         */
        MintChunkJob::dispatch(
            batchId: $this->batchId,
            chunkSize: $this->chunkSize
        )->delay(now()->addSeconds(2));
    }
}
