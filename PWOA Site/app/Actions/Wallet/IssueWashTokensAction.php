<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Services\PrivatePaymentService;
use Illuminate\Support\Facades\Log;

class IssueWashTokensAction
{
    public function __construct(
        protected PrivatePaymentService $service,
        protected \App\Services\WashBalanceService $balanceService
    ) {
    }

    public function execute(User $user, float $amount): bool
    {
        $wallet = $user->wallet;

        if (!$wallet) {
            Log::error("Cannot issue tokens to user {$user->id}: No wallet found.");
            return false;
        }

        // Use the new robust issueTokens method which handles the XRPL response
        // parsing and creates a local TokenTransaction record.
        $transaction = $this->service->issueTokens($user->id, $wallet->address, $amount);

        if ($transaction->status === 'success') {
            $this->balanceService->clearCache($user);
            return true;
        }

        Log::error("Token issuance failed for user {$user->id} (Wallet: {$wallet->address})", [
            'amount' => $amount,
            'error_message' => $transaction->error_message,
        ]);

        return false;
    }
}
