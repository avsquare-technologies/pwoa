<?php

namespace App\Jobs;

use App\Models\TicketBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DispatchTicketChunksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $batchId,
        public int $chunkSize = 25
    ) {}

    public function handle()
    {
        $batch = TicketBatch::where("batch_id", $this->batchId)->firstOrFail();

        // ❌ Prevent double-starting if already minting or completed
        if ($batch->status === 'minting' || $batch->status === 'completed') {
            Log::warning("⚠️ Dispatch aborted: Batch {$this->batchId} is already in {$batch->status} state.");
            return;
        }

        $batch->update([
            "status" => "minting",
            "error" => null // Clear any old errors
        ]);

        Log::info("🚀 Starting Chunk Mint System for Event", [
            "batch" => $batch->batch_id,
            "total" => $batch->total
        ]);

        // ✅ Dispatch ONLY FIRST chunk
        MintChunkJob::dispatch(
            batchId: $batch->batch_id,
            chunkSize: $this->chunkSize
        );
    }
}
