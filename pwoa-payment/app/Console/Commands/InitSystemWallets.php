<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SystemWallet;
use App\Services\XrplConnection;
use App\Services\SystemWalletManager;
use Illuminate\Support\Facades\Http;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Exception;

class InitSystemWallets extends Command
{
    protected $signature = 'xrpl:init-wallet
                            {type? : The category (hot, cold, escrow, custom)}
                            {name? : A unique name for this wallet}';


    protected $description = 'Initialize, fund, and optionally create trustlines for system wallets';

    protected XrplConnection $connection;
    protected SystemWalletManager $manager;

    public function __construct(XrplConnection $connection, SystemWalletManager $manager)
    {
        parent::__construct();
        $this->connection = $connection;
        $this->manager = $manager;
    }

    public function handle()
    {
        $this->info("--- XRPL Wallet Initialization Wizard ---");

        // 1. Selection
        $type = $this->argument('type');
        if (!$type) {
            $type = $this->choice('Which type of wallet?', ['hot', 'cold', 'escrow', 'custom'], 0);
        }

        $type = strtolower($type);

        $name = $this->argument('name') ?: $this->ask("Enter a unique name for this {$type} wallet");
        $name = strtolower($name);

        // 2. Mapping & Validation
        $dbType = match ($type) {
            'cold' => 'cold_issuer',
            'hot' => 'hot_operational',
            'escrow' => 'escrow_vault',
            'custom' => 'custom_wallet',
        };


        if (SystemWallet::where('name', $name)->exists()) {
            $this->error("Error: Wallet name '{$name}' already exists.");
            return Command::FAILURE;
        }

        // 3. Execution
        $this->info('Generating XRPL Keys...');
        $xrplWallet = XRPLWallet::generate();
        $address = $xrplWallet->getAddress();
        $seed = $xrplWallet->getSeed();

        try {
            $this->fundUntil($address, 100);

            SystemWallet::create([
                'name' => $name,
                'address' => $address,
                'seed' => $seed,
                'public_key' => $xrplWallet->getPublicKey(),
                'type' => $dbType,
                'default_currency' => 'XRP',
            ]);

            $this->info("✓ Successfully initialized '{$name}'!");

            // 4. Custom Flow: Create Trustline
            if ($type === 'custom') {
                $this->handleCustomTrustline($address, $seed);
            }
        } catch (Exception $e) {
            $this->error("Failed: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Logic for creating a Trustline to the Cold Issuer
     */
    protected function handleCustomTrustline(string $address, string $seed)
    {
        if (!$this->confirm("Would you like to create a Trustline for this custom wallet to the Cold Issuer?", true)) {
            return;
        }

        $currencyCode = $this->ask("Enter the Currency Code (e.g., USD, FEE)", "FEE");

        try {
            $this->info("Creating Trustline for {$currencyCode}...");

            $coldCreds = $this->manager->getCredentials('cold');
            $client = $this->connection->getClient();

            // Get Sequence for the new Custom Wallet
            $res = $client->request(['command' => 'account_info', 'account' => $address, 'ledger_index' => 'current']);
            $sequence = $res['result']['account_data']['Sequence'];

            $tx = [
                'TransactionType' => 'TrustSet',
                'Account' => $address,
                'LimitAmount' => [
                    'currency' => $currencyCode,
                    'issuer' => $coldCreds['address'],
                    'value' => '1000000000', // Default large limit
                ],
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = (int) config('xrpl.network.testnet.id', 21338);
            }

            $wallet = XRPLWallet::fromSeed($seed);

            if (!isset($tx['SourceTag']) && $this->connection) {
                $tx['SourceTag'] = $this->connection->getSourceTag();
            }

            $signed = $wallet->sign($tx);

            $submit = $client->request([
                'command' => 'submit',
                'tx_blob' => $signed['tx_blob']
            ]);

            if (($submit['result']['engine_result'] ?? '') === 'tesSUCCESS') {
                $this->info("✓ Trustline created successfully!");
            } else {
                $this->warn("Trustline failed: " . ($submit['result']['engine_result_message'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            $this->error("Trustline Error: " . $e->getMessage());
        }
    }

    private function fundUntil(string $address, int $targetXrp): void
    {
        $this->info("  💧 Requesting 100 XRP from faucet...");
        for ($i = 0; $i < 3; $i++) {
            $balance = $this->getBalance($address);
            if ($balance >= $targetXrp) return;
            $this->triggerFaucet($address);
            sleep(10);
        }
    }

    private function triggerFaucet(string $address): void
    {
        $faucetUrl = config('xrpl.faucet_url', 'https://faucet.altnet.rippletest.net/accounts');
        Http::post($faucetUrl, ['destination' => $address]);
    }

    private function getBalance(string $address): float
    {
        $ws = $this->connection->getClient();
        try {
            $res = $ws->request(['command' => 'account_info', 'account' => $address, 'ledger_index' => 'validated']);
            return isset($res['result']['account_data']['Balance'])
                ? (float) $res['result']['account_data']['Balance'] / 1_000_000
                : 0.0;
        } catch (Exception $e) {
            return 0.0;
        } finally {
            $ws->close();
        }
    }
}
