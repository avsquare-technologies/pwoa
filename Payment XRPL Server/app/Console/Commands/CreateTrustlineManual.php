<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XrplConnection;
use App\Services\SystemWalletManager;

class CreateTrustlineManual extends Command
{
    protected $signature = 'xrpl:create-trustline-manual';
    protected $description = 'Create XRPL TrustLine manually';

    protected $connection;
    protected $walletManager;

    public function __construct(XrplConnection $connection, SystemWalletManager $walletManager)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->walletManager = $walletManager;
    }

    public function handle()
    {
        $this->info("XRPL TrustLine Creator");

        // 1. wallet address
        $walletAddress = $this->ask('Enter Wallet Address');

        // 2. seed
        $seed = $this->secret('Enter Wallet Seed');

        // 3. issuer
        $issuerAddress = $this->ask('Enter Issuer Address');

        // 4. currency
        $currency = $this->ask('Enter Currency Code', 'FEE');

        $this->table(
            ['Field','Value'],
            [
                ['Wallet', $walletAddress],
                ['Issuer', $issuerAddress],
                ['Currency', $currency]
            ]
        );

        if (!$this->confirm('Submit TrustLine transaction?', true)) {
            $this->warn('Cancelled');
            return;
        }

        try {

            $hash = $this->walletManager->submitTransaction(
                $seed,
                function ($sequence, $signerAddress) use ($currency, $issuerAddress) {

                    $tx = [
                        'TransactionType' => 'TrustSet',
                        'Account' => $signerAddress,
                        'LimitAmount' => [
                            'currency' => $currency,
                            'issuer' => $issuerAddress,
                            'value' => '10000000000',
                        ],
                        'Sequence' => $sequence,
                        'Fee' => $this->connection->getFee(),
                    ];

                    if (config('xrpl.network_name') === 'xahau') {
                        $tx['NetworkID'] = $this->connection->getNetworkId();
                    }

                    return $tx;
                }
            );

            $this->info("TrustLine Created");

            if (is_array($hash) && isset($hash['result']['tx_json']['hash'])) {
                $this->line("Hash: " . $hash['result']['tx_json']['hash']);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

    }
}
