<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class TrustlineService
{
    protected $workflow;

    protected $xrplWalletService;

    public function __construct(TokenWorkflowService $workflow, XRPLWalletService $xrplWalletService)
    {
        $this->workflow = $workflow;
        $this->xrplWalletService = $xrplWalletService;
    }

    public function ensureTrustlineForAddress(string $address, string $currency): void
    {
        $wallet = Wallet::where('address', $address)->first();

        if (! $wallet) {
            Log::warning("External address detected: {$address}. Manual trustline required if not in DB.");

            return;
        }

        Log::info("Ensuring trustline for: {$address}");
        $this->xrplWalletService->ensureTrustLineForUser($wallet->seed, $currency);
    }
}
