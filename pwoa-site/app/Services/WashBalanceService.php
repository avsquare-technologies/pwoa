<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class WashBalanceService
{
    /** Minimum balance required for access */
    public const MIN_BALANCE = 2000;

    protected PublicWalletService $publicWalletService;

    public function __construct(PublicWalletService $publicWalletService)
    {
        $this->publicWalletService = $publicWalletService;
    }

    /**
     * Retrieve the real-time balance for a user. Returns null on failure.
     */
    public function getBalance(User $user): ?float
    {
        $wallet = $user->wallet;
        if (!$wallet) {
            return 0.00;
        }
        return $this->publicWalletService->getBalance($wallet);
    }

    /**
     * Quick check whether the user meets the required balance.
     */
    public function hasRequiredBalance(User $user): bool
    {
        $balance = $this->getBalance($user);
        
        // Allow the required balance to be configurable, falling back to MIN_BALANCE constant
        $requiredBalance = config('wallet.min_wash_balance', self::MIN_BALANCE);
        
        return $balance !== null && $balance >= $requiredBalance;
    }

    /**
     * Clear the cached balance for a user.
     * Deprecated: Caching has been removed in favor of real-time queries.
     * Keeping method to prevent breaking changes if called elsewhere.
     */
    public function clearCache(User $user): void
    {
        // No-op
    }
}
