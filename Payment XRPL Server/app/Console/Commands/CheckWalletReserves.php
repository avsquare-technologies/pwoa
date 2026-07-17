<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wallet;
use App\Services\SystemWalletManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class CheckWalletReserves extends Command
{
    protected $signature = 'xrpl:check-reserves';
    protected $description = 'Memory-optimized refill using chunks for large wallet sets';

    protected SystemWalletManager $manager;

    public function __construct(SystemWalletManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle()
    {
        $threshold = (float) config('xrpl.min_reserve_threshold', 2.2);
        $refillAmt = (float) config('xrpl.refill_amount', 2);
        $chunkSize = 50;

        $this->info("🚀 Starting Optimized Reserve Check...");

        try {
            $reserves = $this->manager->getLiveReserves();
            $baseRes = $reserves['base'];
            $ownerRes = $reserves['owner'];
            $this->comment("Network Rules -> Base: {$baseRes} | Owner: {$ownerRes}");
        } catch (Exception $e) {
            $this->error("Fallback to 1.0/0.2 reserves");
            $baseRes = 1.0;
            $ownerRes = 0.2;
        }

        Wallet::where('status', 'active')
            ->chunkById($chunkSize, function ($wallets) use ($baseRes, $ownerRes, $threshold, $refillAmt, $chunkSize) {

                foreach ($wallets as $wallet) {
                    $address = $wallet->address;

                    if (Cache::has("pending_refill_{$address}")) {
                        continue;
                    }

                    try {
                        $res = $this->manager->getAccountInfo($address);

                        if (!isset($res['account_data'])) {
                            throw new Exception("Account data missing");
                        }

                        $account = $res['account_data'];
                        $totalBalance = (float) ($account['Balance'] / 1000000);
                        $ownerCount   = (int) ($account['OwnerCount'] ?? 0);

                        $requiredReserve = $baseRes + ($ownerCount * $ownerRes);
                        $spendableXRP = $totalBalance - $requiredReserve;

                        if ($spendableXRP < $threshold) {
                            $this->warn(" ⚠️ Low Spendable ({$spendableXRP}) for {$address}. Refilling...");

                            Cache::put("pending_refill_{$address}", true, now()->addMinutes(10));

                            $this->manager->sendSystemPayment('hot', $address, $refillAmt, null);

                            $this->info(" ✅ Refill sent to {$address}");
                        }
                    } catch (Exception $e) {
                        if ($this->isInactiveError($e->getMessage())) {
                            $this->error(" 🚨 Activating {$address}...");

                            try {
                                Cache::put("pending_refill_{$address}", true, now()->addMinutes(10));
                                // $this->manager->sendSystemPayment('hot', $address, 2.0, null);
                            } catch (Exception $inner) {
                                $this->error(" ❌ Activation failed for {$address}");
                            }
                        } else {
                            $this->error(" ❌ Failed {$address}: " . $e->getMessage());
                            Cache::forget("pending_refill_{$address}");
                        }
                    }
                }

                $this->line("--- Finished a chunk of {$chunkSize} wallets ---");
            });

        $this->info("🏁 All chunks complete.");
        return self::SUCCESS;
    }

    private function isInactiveError(string $message): bool
    {
        return str_contains($message, 'not found') ||
            str_contains($message, 'inactive') ||
            str_contains($message, 'actNotFound');
    }
}
