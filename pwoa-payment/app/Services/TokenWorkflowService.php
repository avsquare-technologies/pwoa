<?php

namespace App\Services;
use Exception;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Traits\NormalizesXRPLCurrency;

class TokenWorkflowService
{
    use NormalizesXRPLCurrency;

    protected $connection;

    protected $manager;

    public function __construct(XrplConnection $connection, SystemWalletManager $manager)
    {
        $this->connection = $connection;
        $this->manager = $manager;
    }

    public function configureIssuer()
    {
        Log::info('Step 1: Configuring Issuer Account...');

        $creds = $this->manager->getCredentials('cold');
        $address = $creds['address'];

        return $this->submitWithLock($creds, 'Configure Issuer', function ($sequence) use ($address) {

            $feePercent = (float) config('xrpl.issuer_settings.transfer_fee_percent', 0);
            $domainStr = config('xrpl.issuer_settings.domain', '');
            $tickSize = (int) config('xrpl.issuer_settings.tick_size', 5);

            $transferRate = 0;
            if ($feePercent > 0) {
                $transferRate = (int) (1000000000 * (1 + ($feePercent / 100)));
            }

            $domainHex = '';
            if (! empty($domainStr)) {
                $domainHex = bin2hex($domainStr);
            }

            $tx = [
                'TransactionType' => 'AccountSet',
                'Account' => $address,
                'SetFlag' => 8,
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
                // 'NetworkID' => $this->connection->getNetworkId(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = (int) config('xrpl.network.testnet.id', 21338);
            }

            if ($transferRate > 0) {
                $tx['TransferRate'] = $transferRate;
            }
            if (! empty($domainHex)) {
                $tx['Domain'] = $domainHex;
            }
            if ($tickSize > 0) {
                $tx['TickSize'] = $tickSize;
            }

            return $tx;
        });
    }

    public function makeTrustLine(string $currencyCode)
    {
        Log::info('Step 2: Creating TrustLine...');

        $currencyCode = $this->normalizeCurrency($currencyCode);

        $hotCreds = $this->manager->getCredentials('hot');

        $coldCreds = $this->manager->getCredentials('cold');

        return $this->submitWithLock($hotCreds, 'Set TrustLine', function ($sequence) use ($hotCreds, $coldCreds, $currencyCode) {
            $tx = [
                'TransactionType' => 'TrustSet',
                'Account' => $hotCreds['address'],
                'LimitAmount' => [
                    'currency' => $currencyCode,
                    'issuer' => $coldCreds['address'],
                    'value' => '10000000000',
                ],
                'Sequence' => $sequence,
                'Fee' => $this->connection->getFee(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = config('xrpl.network.testnet.id', 21338);
            }

            return $tx;
        });
    }

    public function sendToken(string $currencyCode, string $amount)
    {
        Log::info('Step 3: Minting Token...');

        $currencyCode = $this->normalizeCurrency($currencyCode);

        $hotCreds = $this->manager->getCredentials('hot');

        $txHash = $this->manager->sendSystemPayment(
            'cold',
            $hotCreds['address'],
            (float) $amount,
            $currencyCode
        );

        return [
            'status' => 'success',
            'tx_hash' => $txHash,
            'message' => 'Minting successful',
        ];
    }

    public function confirmBalances(string $currencyCode)
    {
        Log::info('Step 4: Confirming Balances...');

        $currencyCode = $this->normalizeCurrency($currencyCode);

        $hotCreds = $this->manager->getCredentials('hot');

        $coldCreds = $this->manager->getCredentials('cold');

        $client = $this->connection->getClient();

        sleep(2);

        $lines = $client->request([
            'command' => 'account_lines',
            'account' => $hotCreds['address'],
            'peer' => $coldCreds['address'],
        ]);

        $balance = '0';
        if (isset($lines['result']['lines'])) {
            foreach ($lines['result']['lines'] as $line) {
                if ($line['currency'] === $currencyCode) {
                    $balance = $line['balance'];
                    break;
                }
            }
        }

        return [
            'hot_wallet' => $hotCreds['address'],
            'issuer' => $coldCreds['address'],
            'currency' => $currencyCode,
            'balance' => $balance,
        ];
    }

    protected function submitWithLock(array $creds, string $name, callable $txBuilder)
    {
        try {
            return Cache::lock('wallet_tx_'.$creds['address'], 10)->block(5, function () use ($creds, $name, $txBuilder) {

                $client = $this->connection->getClient();

                $info = $this->getAccountInfo($creds['address']);
                $sequence = $info['account_data']['Sequence'];

                $tx = $txBuilder($sequence);
                Log::info('Submitting Transaction', $tx);

                if ($tx === null) {
                    return ['status' => 'skipped', 'message' => "$name already done."];
                }

                $wallet = XRPLWallet::fromSeed($creds['seed']);
                if (!isset($tx['SourceTag']) && $this->connection) {
                    $tx['SourceTag'] = $this->connection->getSourceTag();
                }

                $signed = $wallet->sign($tx);

                $result = $client->request([
                    'command' => 'submit',
                    'tx_blob' => $signed['tx_blob'],
                ]);

                $engineResult = $result['result']['engine_result'] ?? 'failed';
                if ($engineResult !== 'tesSUCCESS' && $engineResult !== 'terQUEUED') {
                    throw new Exception("$name Failed: ".($result['result']['engine_result_message'] ?? $engineResult));
                }

                $this->waitForValidation($result['result']['tx_json']['hash']);

                return [
                    'status' => 'success',
                    'tx_hash' => $result['result']['tx_json']['hash'],
                ];
            });
        } catch (LockTimeoutException $e) {
            throw new Exception("System Busy: Could not acquire lock for $name.");
        }
    }

    protected function getAccountInfo($address)
    {
        $client = $this->connection->getClient();
        $res = $client->request(['command' => 'account_info', 'account' => $address, 'ledger_index' => 'current']);

        if (! isset($res['result']['account_data'])) {
            throw new Exception("Account $address not found. Please fund it first.");
        }

        return $res['result'];
    }

    protected function waitForValidation($hash)
    {
        $client = $this->connection->getClient();
        $attempts = 0;
        while ($attempts < 8) {
            sleep(1);
            $res = $client->request(['command' => 'tx', 'transaction' => $hash]);
            if (isset($res['result']['validated']) && $res['result']['validated'] === true) {
                return;
            }
            $attempts++;
        }
    }

    public function getIssuerFullSettings()
    {
        $creds = $this->manager->getCredentials('cold');
        $address = $creds['address'];

        $info = $this->getAccountInfo($address);
        $data = $info['account_data'];

        return [
            'address' => $address,

            'Domain' => isset($data['Domain'])
                ? hex2bin($data['Domain'])
                : null,

            'TransferRateRaw' => $data['TransferRate'] ?? null,

            'TransferRatePercent' => isset($data['TransferRate'])
                ? $this->transferRateToPercent($data['TransferRate'])
                : null,

            'TickSize' => $data['TickSize'] ?? null,

            'EmailHash' => $data['EmailHash'] ?? null,
            'MessageKey' => $data['MessageKey'] ?? null,
            'NFTokenMinter' => $data['NFTokenMinter'] ?? null,

            'FlagsRaw' => $data['Flags'] ?? 0,
        ];
    }

    public function updateIssuerAccountSet(string $type, string $key, $value)
    {
        $creds = $this->manager->getCredentials('cold');
        $address = $creds['address'];

        return $this->submitWithLock($creds, "Update AccountSet", function ($sequence) use ($address, $type, $key, $value) {

            $tx = [
                'TransactionType' => 'AccountSet',
                'Account'         => $address,
                'Sequence'        => $sequence,
                'Fee'             => $this->connection->getFee(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = (int) config('xrpl.network.testnet.id', 21338);
            }

            /*
            |--------------------------------------------------------------------------
            | FIELD Updates
            |--------------------------------------------------------------------------
            */
            if ($type === "field") {

                if ($key === "Domain") {
                    $tx['Domain'] = bin2hex($value);
                }

                elseif ($key === "TransferRate") {
                    $tx['TransferRate'] = (int) $value;
                }

                elseif ($key === "TickSize") {
                    $tx['TickSize'] = (int) $value;
                }

                elseif ($key === "EmailHash") {
                    $tx['EmailHash'] = $value;
                }

                elseif ($key === "MessageKey") {
                    $tx['MessageKey'] = $value;
                }

                elseif ($key === "NFTokenMinter") {
                    $tx['NFTokenMinter'] = $value;
                }
            }
            if ($type === "flag") {

                $flagMap = [
                    "RequireDestTag"   => 1,
                    "RequireAuth"      => 2,
                    "DisallowXRP"      => 3,
                    "DisableMasterKey" => 4,
                    "DefaultRipple"    => 8,
                    "DepositAuth"      => 9,
                    "GlobalFreeze"     => 7,
                    "NoFreeze"         => 6,
                ];

                if (!isset($flagMap[$key])) {
                    throw new Exception("Unknown flag: {$key}");
                }

                if ($value === "set") {
                    $tx['SetFlag'] = $flagMap[$key];
                }

                if ($value === "clear") {
                    $tx['ClearFlag'] = $flagMap[$key];
                }
            }

            return $tx;
        });
    }


    public function transferRateToPercent($rate): ?float
    {
        if (! $rate) {
            return null;
        }

        return round((($rate / 1000000000) - 1) * 100, 4);
    }

    public function percentToTransferRate(float $percent): int
    {
        return (int) round(1000000000 * (1 + ($percent / 100)));
    }
}


