<?php

namespace App\Services;

use App\Client\WsClient;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PublicWalletService
{
    use \App\Traits\HandlesXrplCurrency;

    public function __construct(
        protected PrivatePaymentService $privatePaymentService
    ) {
    }

    /**
     * Get the balance of the custom token ($WASH) for a wallet.
     * Returns a float for backward compatibility with controllers.
     */
    public function getBalance(Wallet $wallet): float
    {
        $balances = $this->getLedgerBalances($wallet);
        $currencyCode = config('services.xrpl.currency', 'WASH');
        $issuerAddress = $this->getSystemWalletAddress('cold');

        foreach ($balances as $balance) {
            if ($this->isCurrencyMatch($balance['currency'], $currencyCode)) {
                if ($balance['currency'] !== 'XRP' && ($balance['issuer'] ?? '') !== $issuerAddress) {
                    continue;
                }
                return (float) $balance['balance'];
            }
        }

        return 0.00;
    }

    /**
     * Get all balances (XRP + Tokens) from the ledger.
     */
    public function getLedgerBalances(Wallet $wallet): array
    {
        try {
            $network = config('services.xrpl.network', 'wss://s.altnet.rippletest.net:51233');
            $ws = new WsClient($network, true);

            $balances = [];

            // 1. Get XRP Balance (account_info)
            $info = $ws->request([
                'command' => 'account_info',
                'account' => $wallet->address,
                'ledger_index' => 'validated',
            ]);

            if (isset($info['result']['account_data'])) {
                $balances[] = [
                    'balance' => (float) $info['result']['account_data']['Balance'] / 1000000,
                    'currency' => 'XRP',
                    'issuer' => '',
                ];
            }

            // 2. Get Trustline Balances (account_lines)
            $lines = $ws->request([
                'command' => 'account_lines',
                'account' => $wallet->address,
                'ledger_index' => 'validated',
            ]);

            if (isset($lines['result']['lines'])) {
                foreach ($lines['result']['lines'] as $line) {
                    $balances[] = [
                        'balance' => (float) $line['balance'],
                        'currency' => $this->decodeCurrency($line['currency']),
                        'issuer' => $line['account'],
                    ];
                }
            }

            $ws->close();
            return $balances;
        } catch (\Exception $e) {
            Log::error('Failed to fetch ledger balances: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch transactions for a wallet and filter for the custom token.
     */
    public function fetchTransactions(Wallet $wallet, int $limit = 20)
    {
        try {
            $currencyCode = config('services.xrpl.currency', 'WASH');
            $issuer = $this->getSystemWalletAddress('cold');
            $network = config('services.xrpl.network', 'wss://s.altnet.rippletest.net:51233');

            $ws = new WsClient($network, true);

            $payload = [
                'id' => 1,
                'command' => 'account_tx',
                'account' => $wallet->address,
                'ledger_index_min' => -1,
                'ledger_index_max' => -1,
                'limit' => 50,
                'binary' => false,
                'forward' => false,
            ];

            $res = $ws->request($payload);
            $ws->close();

            // Log::debug('XRPL Account Transactions Response for ' . $wallet->address, ['res' => $res]);

            $allRawTransactions = $res['result']['transactions'] ?? [];

            $filteredTransactions = [];

            foreach ($allRawTransactions as $tx) {
                $txn = $tx['tx'] ?? [];
                $type = $txn['TransactionType'] ?? '';

                if ($type === 'Payment') {
                    $amount = $txn['Amount'] ?? null;

                    // Check if it's our custom token
                    if (is_array($amount)) {
                        if ($this->isCurrencyMatch($amount['currency'] ?? '', $currencyCode) && ($amount['issuer'] ?? '') === $issuer) {
                            $isIncoming = ($txn['Destination'] === $wallet->address);

                            $filteredTransactions[] = [
                                'hash' => $txn['hash'] ?? ($tx['hash'] ?? ''),
                                'type' => $isIncoming ? 'credit' : 'debit',
                                'amount' => (float) $amount['value'],
                                'description' => $isIncoming ? 'Received Tokens' : 'Sent Tokens',
                                'counterparty' => $isIncoming ? $txn['Account'] : $txn['Destination'],
                                'timestamp' => $this->rippleTimeToUtc($txn['date'] ?? ($txn['Timestamp'] ?? null)),
                                'status' => 'completed'
                            ];
                        }
                    }
                }
            }

            return array_slice($filteredTransactions, 0, $limit);

        } catch (\Throwable $e) {
            Log::error('XRPL transaction fetch error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get the system wallet address (issuer) from the private service.
     */
    public function getSystemWalletAddress(string $type = 'cold'): string
    {
        try {
            return Cache::remember("system_wallet_address_{$type}", now()->addHours(2), function () use ($type) {
                // We use the existing PrivatePaymentService to get the address
                // Assuming it has a way to get the system wallet or we call the API directly
                // Based on user's reference: $privateApi->post("/wallet/system-wallet/{$type}")

                $response = $this->privatePaymentService->getSystemWallet($type);

                $address = $response['data']['data']['address'] ?? $response['data']['address'] ?? $response['address'] ?? null;

                if (empty($address)) {
                    throw new RuntimeException("Private API missing wallet address. Response: " . json_encode($response));
                }

                return $address;
            });
        } catch (\Throwable $e) {
            Log::error("GetSystemWalletAddress Error [{$type}]: " . $e->getMessage());
            return ''; // Return empty or handle as needed
        }
    }

    /**
     * Convert Ripple Epoch time to UTC string.
     */
    private function rippleTimeToUtc(int $rippleTime): string
    {
        if (!$rippleTime)
            return now()->toDateTimeString();
        // Ripple epoch starts at 2000-01-01 00:00:00 UTC (946684800 Unix)
        return date('Y-m-d H:i:s', $rippleTime + 946684800);
    }

    public function mintBatchNft(int $userId, string $uri, int $count, int $taxon, float $price = 0)
    {
        $wallet = Wallet::where('user_id', $userId)->firstOrFail();

        return $this->privatePaymentService->mintBatchNft(
            $wallet->address,
            $uri,
            $count,
            $taxon,
            $price
        );
    }
    public function hasSufficientBalance(Wallet $wallet, float $amount): bool
    {
        return $this->getBalance($wallet) >= $amount;
    }

    public function buyTicket(string $buyerAddress, string $sellerAddress, string $tokenId, float $amount)
    {
        try {
            $buyerWallet = Wallet::where('address', $buyerAddress)->firstOrFail();

            if (!$this->hasSufficientBalance($buyerWallet, $amount)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient $WASH balance to purchase this ticket.',
                ];
            }

            return $this->privatePaymentService->buyNftTicket(
                $buyerAddress,
                $sellerAddress,
                $tokenId,
                $amount
            );
        } catch (\Exception $e) {
            Log::error('Buy Ticket Service Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
