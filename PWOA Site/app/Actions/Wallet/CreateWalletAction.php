<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Services\PrivatePaymentService;
use Illuminate\Support\Facades\Log;

class CreateWalletAction
{
    public function __construct(
        protected PrivatePaymentService $service
    ) {}

    public function execute(User $user): ?Wallet
    {
        if ($user->wallet) {
            return $user->wallet;
        }

        try {
            $result = $this->service->createWallet(
                $user->id,
                $user->email,
                $user->name
            );

            if ($result && isset($result['address'])) {
                return Wallet::create([
                    'user_id' => $user->id,
                    'address' => $result['address'],
                    'public_key' => $result['public_key'] ?? null,
                    'status' => 'active',
                ]);
            }

            Log::error("Wallet creation failed for user {$user->id}: No address returned.");
        } catch (\Exception $e) {
            Log::error("Wallet creation failed for user {$user->id}: " . $e->getMessage());
        }

        return null;
    }
}
