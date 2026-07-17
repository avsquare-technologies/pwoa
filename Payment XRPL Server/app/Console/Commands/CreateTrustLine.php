<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wallet;
use App\Services\XRPLWalletService;
use App\Services\SystemWalletManager;
use Illuminate\Support\Facades\Log;

class CreateTrustLine extends Command
{
    /**
     * The name and signature of the console command.
     * Everything is optional (?) so we can ask interactively.
     */
    protected $signature = 'xrpl:trust
                            {address? : The User Wallet Address}
                            {--issuer= : Who to trust? (hot, cold, custom)}
                            {--currency= : Currency Code (FEE, USD, etc)}';

    protected $description = 'Interactive command to create a TrustLine for a user';

    protected $walletService;

    public function __construct(XRPLWalletService $walletService)
    {
        parent::__construct();
        $this->walletService = $walletService;
    }

    public function handle()
    {
        $this->info("🚀 XRPL TrustLine Manager");

        // 1. Get User Address (Ask if not provided)
        $userAddress = $this->argument('address');
        if (!$userAddress) {
            $userAddress = $this->ask('Enter the User Wallet Address (Destination)');
        }

        // Validate User exists in DB
        $userWallet = Wallet::where('address', $userAddress)->first();
        if (!$userWallet) {
            $this->error("❌ Wallet not found in database: $userAddress");
            return 1;
        }
        if (empty($userWallet->seed)) {
            $this->error("❌ Wallet found but has no SEED. Cannot sign transaction.");
            return 1;
        }

        // 2. Select Issuer (Menu selection)
        $issuerInput = $this->option('issuer');
        $issuerAddress = null;

        if (!$issuerInput) {
            // Interactive Menu
            $issuerType = $this->choice(
                'Which wallet should the user trust?',
                ['hot', 'cold', 'custom'],
                0 // Default to hot
            );
        } else {
            // Command line argument logic
            $issuerType = in_array($issuerInput, ['hot', 'cold']) ? $issuerInput : 'custom';
            if ($issuerType === 'custom') $issuerAddress = $issuerInput;
        }

        // Resolve Address based on type
        if ($issuerType === 'hot') {
            $issuerAddress = config('xrpl.hot_wallet.address');
            $this->info("👉 Selected Issuer: HOT Wallet ($issuerAddress)");
        } elseif ($issuerType === 'cold') {
            $issuerAddress = config('xrpl.cold_wallet.address');
            $this->info("👉 Selected Issuer: COLD Wallet ($issuerAddress)");
        } elseif ($issuerType === 'custom' && !$issuerAddress) {
            $issuerAddress = $this->ask('Enter the Custom Issuer Address');
        }

        // 3. Select Currency
        $currency = $this->option('currency');
        if (!$currency) {
            $defaultCurrency = config('xrpl.currency', 'FEE');
            $currency = $this->ask("Enter Currency Code", $defaultCurrency);
        }

        // 4. Confirmation
        $this->table(
            ['Setting', 'Value'],
            [
                ['User (Signer)', $userAddress],
                ['Trusting (Issuer)', $issuerAddress],
                ['Currency', $currency],
            ]
        );

        if (!$this->confirm('Do you want to submit this transaction?', true)) {
            $this->warn("Cancelled.");
            return 0;
        }

        // 5. Execute
        $this->info("⏳ Submitting to XRPL...");

        try {
            $hash = $this->walletService->ensureTrustLineForUser(
                $userWallet->seed,
                $currency,
                $issuerAddress
            );

            $this->info("✅ Transaction Successful!");
            if (is_array($hash) && isset($hash['result']['tx_json']['hash'])) {
                $this->line("Hash: " . $hash['result']['tx_json']['hash']);
            }
        } catch (\Exception $e) {
            $this->error("❌ Failed: " . $e->getMessage());
        }

        return 0;
    }
}
