<?php

namespace App\Console\Commands;

use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSecureWallet extends Command
{
    protected $signature = 'xrpl:generate-wallet {--json : Output raw JSON in console}';

    protected $description = 'Generate XRPL wallet and store plain JSON securely (not public)';

    public function handle()
    {
        try {

            // 1️⃣ Generate Wallet
            $wallet = XRPLWallet::generate();

            $data = [
                'address' => $wallet->getAddress(),
                'seed' => $wallet->getSeed(),
                'public_key' => $wallet->getPublicKey(),
                'private_key' => $wallet->getPrivateKey(),
                'created_at' => now()->toDateTimeString(),
            ];

            // 2️⃣ Convert to JSON
            $json = json_encode($data, JSON_PRETTY_PRINT);

            // 3️⃣ Ensure private folder exists
            Storage::makeDirectory('private');

            // 4️⃣ Save plain JSON file
            $filename = 'private/wallet_'.time().'.json';
            Storage::put($filename, $json);

            // 5️⃣ Console Output
            $this->info('Wallet generated successfully.');
            $this->info("JSON file stored at: storage/app/{$filename}");

            if ($this->option('json')) {
                $this->newLine();
                $this->line($json);
            }

        } catch (\Exception $e) {

            $this->error('Wallet generation failed: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
