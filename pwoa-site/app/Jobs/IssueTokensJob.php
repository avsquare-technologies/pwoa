<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\PrivatePaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IssueTokensJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $userId,
        protected float $amount,
        protected string $currency = 'WASH'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PrivatePaymentService $service): void
    {
        $user = User::find($this->userId);

        if (!$user || !$user->wallet) {
            Log::error("IssueTokensJob Failed: User {$this->userId} or wallet not found.");
            return;
        }

        $transaction = $service->issueTokens(
            $user->id,
            $user->wallet->address,
            $this->amount,
            $this->currency
        );

        if ($transaction->status === 'failed') {
            // If it failed due to a transient error (e.g. timeout), we might want to retry.
            // But if it's a "tesSUCCESS" mismatch or something permanent, we shouldn't.
            // For now, we'll let the service handle the status, and manually retry if needed.
            
            if (str_contains($transaction->error_message, 'Exception')) {
                throw new \Exception("Transient error during token issuance: " . $transaction->error_message);
            }
        }
    }
}
