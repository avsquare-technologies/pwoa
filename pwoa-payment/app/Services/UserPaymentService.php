<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Log;
use Exception;

class UserPaymentService
{
    protected $walletManager;
    protected $trustlineService;

    public function __construct(
        SystemWalletManager $walletManager,
        TrustlineService $trustlineService
    ) {
        $this->walletManager = $walletManager;
        $this->trustlineService = $trustlineService;
    }

    public function sendFromUser(string $sourceAddress, string $destination, float $amount): array
{
    $wallet = Wallet::where('address', $sourceAddress)->firstOrFail();
    $currency = config('xrpl.currency', 'FEE');

    // STEP 1: Ensure SENDER has trustline
    $this->trustlineService->ensureTrustlineForAddress($sourceAddress, $currency);

    // STEP 2: Ensure DESTINATION has trustline (if managed by us)
    $this->trustlineService->ensureTrustlineForAddress($destination, $currency);

    try {
        Log::info("User transfer {$amount} {$currency} to {$destination}");

        $result = $this->walletManager->sendSystemPayment(
            walletType: 'hot',
            destinationAddress: $destination,
            amount: $amount,
            currencyCode: $currency,
            isCustomerPayment: true,
            customerAddress: $wallet->address
        );

        return [
            'success' => true,
            'result'  => $result,
            'message' => 'User external payment successful'
        ];

    } catch (\Throwable $e) {
        Log::error("User external payment failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
}
