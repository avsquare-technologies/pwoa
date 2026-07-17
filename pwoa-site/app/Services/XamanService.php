<?php

namespace App\Services;

use App\Client\WsClient;
use Illuminate\Support\Facades\Log;
use Xrpl\XummSdkPhp\Payload\Options;
use Xrpl\XummSdkPhp\Payload\Payload;
use Xrpl\XummSdkPhp\XummSdk;

class XamanService
{
    use \App\Traits\HandlesXrplCurrency;

    protected XummSdk $sdk;

    public function __construct()
    {
        $this->sdk = new XummSdk(
            config('services.xumm.api_key'),
            config('services.xumm.api_secret')
        );
    }

    public function checkAndPrepareAccess(string $account)
    {
        $publicWallet = app(PublicWalletService::class);
        $issuer = $publicWallet->getSystemWalletAddress('cold');
        $currency = config('services.xrpl.currency', 'WASH');

        // Reuse existing logic to check the ledger
        $hasLine = $this->hasDestinationTrustline($account, $currency, $issuer);

        if ($hasLine) {
            return [
                'needs_trustline' => false,
                'account' => $account
            ];
        }

        // If no trustline, create the payload
        $payload = $this->createTrustlinePayload($issuer, $currency);

        return [
            'needs_trustline' => true,
            'uuid'   => $payload->uuid,
            'qr_png' => $payload->refs->qrPng,
            'account' => $account
        ];
    }

    public function createTrustlinePayload(string $issuer, string $currency)
    {
        $txBody = [
            'TransactionType' => 'TrustSet',
            'Flags' => 131072,
            'LimitAmount' => [
                'currency' => $this->encodeCurrency($currency),
                'issuer'   => $issuer,
                'value'    => '1000000000',
            ],
        ];

        $payload = new Payload($txBody, null, new Options(submit: true, expire: 240));
        return $this->sdk->createPayload($payload);
    }

    /**
     * Check if the trustline exists on the ledger.
     */
    private function hasDestinationTrustline(string $address, string $currency, string $issuer): bool
    {
        try {
            $network = config('services.xrpl.network', 'wss://xahau-test.net');
            $ws = new WsClient($network, true);

            $res = $ws->request([
                'command'      => 'account_lines',
                'account'      => $address,
                'peer'         => $issuer,
                'ledger_index' => 'validated',
            ]);
            $ws->close();

            if (isset($res['result']['lines'])) {
                foreach ($res['result']['lines'] as $line) {
                    if ($this->isCurrencyMatch($line['currency'], $currency)) {
                        return true;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Xaman Trustline Check Error: " . $e->getMessage());
            return false;
        }
    }

    public function createLoginPayload()
    {
        try {
            $payload = new Payload([
                'TransactionType' => 'SignIn',
            ]);

            return $this->sdk->createPayload($payload);
        } catch (\Throwable $e) {
            Log::error('Xaman Login Error', ['msg' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getPayload(string $uuid)
    {
        return $this->sdk->getPayload($uuid);
    }
}
