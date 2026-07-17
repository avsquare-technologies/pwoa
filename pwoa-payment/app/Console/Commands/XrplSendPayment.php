<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SystemWalletManager;
use Exception;
use Illuminate\Support\Facades\Log;

class XrplSendPayment extends Command
{
    protected $signature = 'xrpl:send';
    protected $description = 'Send a test XRPL payment from system or customer wallets';

    protected SystemWalletManager $walletManager;

    public function __construct(SystemWalletManager $walletManager)
    {
        parent::__construct();
        $this->walletManager = $walletManager;
    }

    public function handle(): int
    {
        // ---------------- SOURCE ----------------
        $sourceType = $this->choice(
            'Select SOURCE wallet',
            $this->walletChoice()
        );

        $sourceAddress = null;

        if ($sourceType === 'customer') {
            $sourceAddress = $this->ask('Enter SOURCE customer XRPL address');
            if (!str_starts_with($sourceAddress, 'r')) {
                $this->error('Invalid source address');
                return 1;
            }
        } else {
            // Get system wallet address for display purposes
            $sourceAddress = $this->walletManager->getCredentials($sourceType)['address'];
        }

        // ---------------- DESTINATION ----------------
        $destType = $this->choice(
            'Select DESTINATION wallet',
            $this->walletChoice()
        );

        $destAddress = null;

        if ($destType === 'customer') {
            $destAddress = $this->ask('Enter DESTINATION customer XRPL address');
            if (!str_starts_with($destAddress, 'r')) {
                $this->error('Invalid destination address');
                return 1;
            }
        } else {
            $destAddress = $this->walletManager->getCredentials($destType)['address'];
        }

        // ---------------- AMOUNT ----------------
        $amount = (float) $this->ask('Enter amount to send');
        if ($amount <= 0) {
            $this->error('Amount must be greater than zero');
            return 1;
        }

        // ---------------- CURRENCY ----------------
        $inputCurrency = $this->ask('Enter currency code (press ENTER for XRP)', 'WASH');
        $currency = strtoupper($inputCurrency);


        // ---------------- CONFIRM ----------------
        $this->newLine();
        $this->info('Transaction summary');
        $this->line("From     : {$sourceType} ({$sourceAddress})");
        $this->line("To       : {$destType} ({$destAddress})");
        $this->line("Amount   : {$amount}");
        $this->line("Currency : {$currency}");
        $this->newLine();

        if (!$this->confirm('Proceed with this transaction?', true)) {
            $this->warn('Transaction cancelled');
            return 0;
        }

        // ---------------- SEND ----------------
        try {
            // Determine flags for the service method
            $isCustomer = ($sourceType === 'customer');

            // If source is customer, the first arg (walletType) is ignored by logic,
            // but we pass 'hot' or similar as a placeholder.
            // If source is system, we pass $sourceType ('hot' or 'cold').
            $walletTypeArg = $isCustomer ? 'hot' : $sourceType;

            $response = $this->walletManager->sendSystemPayment(
                $walletTypeArg,
                $destAddress,
                $amount,
                $currency,
                $isCustomer,
                $sourceAddress
            );

            Log::info('XRP Payment sent', $response);

            // Extract the hash from the result array safely
            $hash = $response['result']['tx_json']['hash'] ?? 'Unknown Hash';

            $this->newLine();
            $this->info('✅ Transaction submitted');
            $this->line("TX Hash: {$hash}");
            return 0;
        } catch (Exception $e) {
            $this->newLine();
            $this->error('❌ Transaction failed');
            $this->line($e->getMessage());
            return 1;
        }
    }
    private function walletChoice()
    {
        return [
            'hot',
            'cold',
            'escrow',
            'customer'
        ];
    }
}
