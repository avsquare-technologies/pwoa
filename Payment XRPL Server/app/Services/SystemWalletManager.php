<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use function Hardcastle\XRPL_PHP\Sugar\xrpToDrops;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use App\Traits\NormalizesXRPLCurrency;

class SystemWalletManager
{
    use NormalizesXRPLCurrency;

    protected $connection;

    public function __construct(XrplConnection $connection)
    {
        $this->connection = $connection;
    }
    public function sendSystemPayment(string $walletType, string $destinationAddress, float $amount, ?string $currencyCode = null, bool $isCustomerPayment = false, ?string $customerAddress = null, bool $IsWithdrawal = false, ?array $memos = null)
    {
        if ($isCustomerPayment) {
            if (empty($customerAddress)) {
                throw new Exception("Source address is required for customer payments.");
            }

            $wallet = Wallet::where('address', $customerAddress)->firstOrFail();

            // Assuming getWalletCipher returns ['address' => '...', 'seed' => '...']
            ['address' => $sourceAddress, 'seed' => $sourceSeed] = ['address' => $wallet->address, 'seed' => $wallet->seed];
        } elseif ($IsWithdrawal) {
            $wallet = Wallet::where('address', $customerAddress)->firstOrFail();
            ['address' => $sourceAddress, 'seed' => $sourceSeed] = ['address' => $wallet->address, 'seed' => $wallet->seed];
        } else {
            // System Wallet (Hot/Cold)
            ['address' => $sourceAddress, 'seed' => $sourceSeed] = $this->getCredentials($walletType);
        }

        ['address' => $officialIssuerAddress] = $this->getCredentials('cold');

        $currencyCode = $currencyCode ?? config('xrpl.native_currency', 'XRP');
        $currencyCode = $this->normalizeCurrency($currencyCode);

        Log::info("Sending {$amount} {$currencyCode} FROM {$sourceAddress} TO {$destinationAddress}");


        return $this->submitTransaction($sourceSeed, function ($sequence, $signerAddress) use ($destinationAddress, $amount, $currencyCode, $officialIssuerAddress, $memos) {

            $tx = [
                'TransactionType' => 'Payment',
                'Account'         => $signerAddress,
                'Destination'     => $destinationAddress,
                'Sequence'        => $sequence,
                'Fee'             => $this->connection->getFee(),
                // 'NetworkID'       => $this->connection->getNetworkId(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = $this->connection->getNetworkId();
            }

            if (!empty($memos)) {
                $tx['Memos'] = $memos;
            }

            if ($currencyCode && $currencyCode !== config('xrpl.native_currency', 'XRP')) {

                Log::info("Sending {$amount} {$currencyCode} issuer {$officialIssuerAddress} FROM {$signerAddress} TO {$destinationAddress}");

                $isIssuer = ($signerAddress === $officialIssuerAddress);

                $tx['Amount'] = [
                    'currency' => $currencyCode,
                    'issuer'   => $officialIssuerAddress,
                    'value'    => (string)$amount
                ];

                if (!$isIssuer) {

                    $transferRate = (string) config('xrpl.issuer_settings.transfer_fee_percent', '0');

                    $amountStr = sprintf('%.8f', (float) $amount);

                    $ratePart = bcdiv($transferRate, '100', 8);
                    $multiplier = bcadd('1', $ratePart, 8);
                    $multiplier = bcadd($multiplier, '0.01', 8);

                    // 4. Final multiplication
                    $value = bcmul($amountStr, $multiplier, 8);

                    $tx['SendMax'] = [
                        'currency' => $currencyCode,
                        'issuer'   => $officialIssuerAddress,
                        'value'    => $value,
                    ];
                }
            } else {
                $tx['Amount'] = (string) xrpToDrops($amount);
            }

            return $tx;
        });
    }

    public function submitTransaction(string $seed, callable $txBuilder)
    {
        $wallet = XRPLWallet::fromSeed($seed);
        $address = $wallet->getAddress();

        try {
            return Cache::lock('wallet_tx_' . $address, 20)->block(10, function () use ($wallet, $address, $txBuilder) {

                $ws = $this->connection->getClient();
                $attempts = 0;
                $maxRetries = 3;

                while ($attempts < $maxRetries) {

                    $accountInfo = $this->getAccountInfo($address);
                    $sequence = $accountInfo['account_data']['Sequence'];

                    $tx = $txBuilder($sequence, $address);

                    if (!isset($tx['SourceTag']) && $this->connection) {
                        $tx['SourceTag'] = $this->connection->getSourceTag();
                    }

                    // Log::info("Submitting Transaction", $tx);
                    // dd($tx);
                    $signed = $wallet->sign($tx);

                    $result = $ws->request([
                        'command' => 'submit',
                        'tx_blob' => $signed['tx_blob']
                    ]);

                    $engineResult = $result['result']['engine_result'] ?? 'failed';

                    if ($engineResult === 'tefPAST_SEQ') {
                        $attempts++;
                        sleep(1);
                        continue;
                    }

                    if ($engineResult !== 'tesSUCCESS' && $engineResult !== 'terQUEUED') {
                        Log::error($result);
                        Log::error("Tx Failed: " . ($result['result']['engine_result_message'] ?? $engineResult));
                        throw new Exception("Tx Failed: " . ($result['result']['engine_result_message'] ?? $engineResult));
                    }

                    $hash = $result['result']['tx_json']['hash'];
                    $this->waitForBalance($ws, $address);

                    // return $result;
                    return $this->waitForValidation($ws, $hash);
                }

                throw new Exception("Tx Failed: Max retries reached for Sequence sync.");
            });
        } catch (LockTimeoutException $e) {
            throw new Exception("System Busy: Could not lock wallet $address.");
        }
    }

    public function getCredentials(string $type): array
    {
        // Try to fetch from database first (flexible/dynamic)
        $dbType = match ($type) {
            'cold' => 'cold_issuer',
            'hot' => 'hot_operational',
            default => null,
        };

        if ($dbType) {
            $wallet = \App\Models\SystemWallet::where('type', $dbType)->first();
            if ($wallet) {
                return ['address' => $wallet->address, 'seed' => $wallet->seed];
            }
        }

        // Fallback to static config (.env)
        $key = match ($type) {
            'cold'   => 'xrpl.cold_wallet',
            'hot'    => 'xrpl.hot_wallet',
            'escrow' => 'xrpl.escrow_wallet',
            default  => throw new Exception("Invalid wallet type requested: $type"),
        };

        $address = config("$key.address");
        $seed = config("$key.seed");

        if (empty($address) || empty($seed)) {
            throw new Exception("Critical: Missing config for $type wallet ($key) and no database record found.");
        }

        return ['address' => $address, 'seed' => $seed];
    }


    public function getAccountInfo($address)
    {
        $ws = $this->connection->getClient();
        $res = $ws->request(['command' => 'account_info', 'account' => $address, 'ledger_index' => 'current']);
        if (!isset($res['result']['account_data'])) {
            throw new Exception("Wallet $address not found / inactive.");
        }
        return $res['result'];
    }

    private function waitForBalance($ws, $address)
    {
        $attempts = 0;
        while ($attempts < 8) {
            sleep(1);
            try {
                $res = $ws->request(['command' => 'account_info', 'account' => $address, 'ledger_index' => 'validated']);
                if (isset($res['result']['account_data']['Balance'])) return;
            } catch (Exception $e) {
            }
            $attempts++;
        }
    }

    public function ensureAccountActive(string $address): void
    {
        $ws = $this->connection->getClient();
        $attempts = 0;
        while ($attempts < 15) { // Wait up to 15 seconds
            try {
                $ws->request(['command' => 'account_info', 'account' => $address]);
                return; // Found it!
            } catch (\Throwable $e) {
                sleep(1);
                $attempts++;
            }
        }
        throw new Exception("Account $address was not activated in time.");
    }

    private function waitForValidation($ws, string $hash)
    {
        $attempts = 0;
        while ($attempts < 10) {
            sleep(1);
            $res = $ws->request([
                'command' => 'tx',
                'transaction' => $hash
            ]);

            if (isset($res['result']['validated']) && $res['result']['validated'] === true) {
                return $res;
            }
            $attempts++;
        }
        throw new Exception("Transaction validation timed out for hash: $hash");
    }


    public function sendToHotWallet(
        XRPLWallet $wallet,
        string $fromAddress,
        float $amount,
        ?string $currency = null,
        ?string $issuer = null,
        ?string $orderId = null,
        $rate = 0
    ): void {

        $ws = $this->connection->getClient();

        try {

            $nativeCurrency = config('xrpl.native_currency', 'XRP');
            $currency = $currency ?? $nativeCurrency;
            $currency = $this->normalizeCurrency($currency);

            $destination = config('xrpl.hot_wallet.address');


            $accountInfo = $ws->request([
                'command' => 'account_info',
                'account' => $fromAddress,
                'ledger_index' => 'current',
            ]);

            if (!isset($accountInfo['result']['account_data']['Sequence'])) {
                throw new Exception("Sender account not funded.");
            }

            $sequence = $accountInfo['result']['account_data']['Sequence'];

            if ($currency === $nativeCurrency) {

                $amountField = (string) xrpToDrops($amount);
                $sendMaxField = null;

            } else {

                $amountField = [
                    'currency' => $currency,
                    'issuer' => $issuer,
                    'value' => (string)$amount
                ];

                $sendMaxField = [
                    'currency' => $currency,
                    'issuer' => $issuer,
                    'value' => (string)$amount
                ];
            }

            $memos = [];

            if ($orderId) {
                $memos = xahau()->buildMemos([
                    'OrderId' => $orderId,
                    'Type' => 'Transfer Fee',
                    'TransferPercentage' => $rate
                ]);
            }

            $tx = [
                'TransactionType' => 'Payment',
                'Account' => $fromAddress,
                'Destination' => $destination,
                'Amount' => $amountField,
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            if ($sendMaxField) {
                $tx['SendMax'] = $sendMaxField;
            }

            if (!empty($memos)) {
                $tx['Memos'] = $memos;
            }

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = $this->connection->getNetworkId();
            }
            
            if (!isset($tx['SourceTag']) && $this->connection) {
                $tx['SourceTag'] = $this->connection->getSourceTag();
            }
            
            $signed = $wallet->sign($tx);

            $result = $ws->request([
                'command' => 'submit',
                'tx_blob' => $signed['tx_blob'],
            ]);

        } finally {

            $ws->close();
        }
    }

    public function getNativeBalance(string $address): float
    {
        try {
            $ws = $this->connection->getClient();
            $res = $ws->request([
                'command' => 'account_info',
                'account' => $address,
                'ledger_index' => 'validated'
            ]);

            return isset($res['result']['account_data']['Balance'])
                ? (float) $res['result']['account_data']['Balance'] / 1_000_000
                : 0.0;
        } catch (Exception $e) {
            return 0.0;
        }
    }
    public function getLiveReserves()
    {
        try {
            $ws = $this->connection->getClient();

            $response = $ws->request([
                'command' => 'server_info',
            ]);

            // Access the validated ledger info
            $info = $response['result']['info']['validated_ledger'] ?? null;

            return [
                'base'  => isset($info['reserve_base_xrp']) ? (float)$info['reserve_base_xrp'] : 1.0,
                'owner' => isset($info['reserve_inc_xrp']) ? (float)$info['reserve_inc_xrp'] : 0.2,
            ];
        } catch (Exception $e) {
            
            return ['base' => 1.0, 'owner' => 0.2];
        }
    }
}
