<?php

namespace App\Services;

use App\Models\CollabathonTransaction;
use App\Models\Wallet;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Traits\NormalizesXRPLCurrency;

class CollabathonService
{
    use NormalizesXRPLCurrency;

    protected $wsClient;


    protected $connection;

    protected $manager;

    public function __construct(XrplConnection $connection, SystemWalletManager $manager)
    {
        $this->connection = $connection;
        $this->wsClient = $connection->getClient();
        $this->manager = $manager;
    }

    public function processPayment(
        int $collabathonId,
        string $type,
        string $fromAddress,
        string $toAddress,
        float $amount,
        ?string $description = null
    ): array {
        $record = CollabathonTransaction::create([
            'collabathon_id' => $collabathonId,
            'type' => $type,
            'source_address' => $fromAddress,
            'destination_address' => $toAddress,
            'amount' => $amount,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        try {
            $walletModel = Wallet::where('address', $fromAddress)->firstOrFail();
            $wallet = XRPLWallet::fromSeed($walletModel->seed);
            $sendMax = $this->calculateSendMax($amount, config('xrpl.currency', 'FEE'));
            $tx = array_merge(
                $this->buildBaseTx($wallet->getAddress()),
                [
                    'TransactionType' => 'Payment',
                    'Destination' => $toAddress,
                    'Amount' => xrplAmountField($amount),
                    // 'SendMax' => $sendMax,
                    'Memos' => xahau()->buildMemos([
                        'CollabathonID' => $collabathonId,
                        'Type' => $type,
                        'Description' => $description ?? sprintf(
                                "%s... -> %s... (%s %s)",
                                substr($fromAddress, 0, 8),
                                substr($toAddress, 0, 8),
                                $amount,
                                config('xrpl.currency', 'FEE')
                            ),
                    ]),

                ]
            );

            if (!isset($tx['SourceTag']) && $this->connection) {
                $tx['SourceTag'] = $this->connection->getSourceTag();
            }

            $signed = $wallet->sign($tx);
            $result = $this->submitSigned($signed);

            $engine = $result['engine_result'] ?? 'failed';
            $success = $this->isFinalSuccess($engine);

            $record->update([
                'tx_hash' => $result['tx_json']['hash'] ?? null,
                'status' => $success ? 'success' : 'failed',
                'response' => $result,
                'validated_at' => now(),
            ]);

            return [
                'success' => $success,
                'tx_hash' => $record->tx_hash,
                'message' => $result['engine_result_message'] ?? $engine,
            ];
        } catch (\Throwable $e) {
            Log::error('Collabathon Payment Error', ['error' => $e->getMessage()]);

            $record->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()],
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function processPayoutPayment(
    int $collabathonId,
    string $type,
    string $fromAddress,
    string $toAddress,
    float $amount,
    ?string $description = null
): array {
    $record = CollabathonTransaction::create([
        'collabathon_id' => $collabathonId,
        'type' => $type,
        'source_address' => $fromAddress,
        'destination_address' => $toAddress,
        'amount' => $amount,
        'status' => 'pending',
        'submitted_at' => now(),
    ]);

    try {
        ['seed' => $hotSeed] = $this->manager->getCredentials('hot');

        $result = $this->manager->submitTransaction($hotSeed, function ($sequence, $signerAddress) use ($collabathonId, $type, $toAddress, $amount, $description) {
            $tx = [
                'TransactionType' => 'Payment',
                'Account'         => $signerAddress,
                'Destination'     => $toAddress,
                'Amount'          => xrplAmountField($amount),
                'Sequence'        => $sequence,
                'Fee'             => $this->connection->getFee(),
            ];

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = $this->connection->getNetworkId();
            }

            $tx['Memos'] = xahau()->buildMemos([
                'CollabathonID' => $collabathonId,
                'Type'          => $type,
                'Description'   => $description ?? "Refund for Collabathon #$collabathonId"
            ]);

            return $tx;
        });

        $txHash = $result['result']['tx_json']['hash'] ?? null;
        $engineResult = $result['result']['meta']['TransactionResult'] ?? 'failed';
        $success = ($engineResult === 'tesSUCCESS');

        $record->update([
            'tx_hash'      => $txHash,
            'status'       => $success ? 'success' : 'failed',
            'response'     => $result,
            'validated_at' => now(),
        ]);

        return [
            'success' => $success,
            'tx_hash' => $txHash,
            'message' => $engineResult,
        ];

    } catch (\Throwable $e) {
        Log::error('Collabathon Refund Error', ['error' => $e->getMessage()]);

        $record->update([
            'status'   => 'failed',
            'response' => ['error' => $e->getMessage()],
        ]);

        return ['success' => false, 'message' => $e->getMessage()];
    }
}

    public function buyTicket(int $collabathonId, string $issuerAddress, string $buyerAddress, string $tokenId, float $amount): array
    {
        $network = config('xrpl.network_name');

        $record = CollabathonTransaction::create([
            'collabathon_id' => $collabathonId,
            'type' => 'BUY_TICKET',
            'source_address' => $buyerAddress,
            'destination_address' => $issuerAddress,
            'amount' => $amount,
            'nft_token_id' => $tokenId,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        try {
            // Wallet credentials
            $buyerWallet = Wallet::where('address', $buyerAddress)->firstOrFail();
            $issuerWallet = Wallet::where('address', $issuerAddress)->firstOrFail();

            // HOT wallet via credentials manager
            $hotCreds = $this->manager->getCredentials('hot');
            $hotAddress = $hotCreds['address'];
            $hotSeed = $hotCreds['seed'];

            $isXahau = (config('xrpl.network_name') === 'xahau');
            $amountField = xrplAmountField($amount);

            $sellTx = $this->buildBaseTx($hotAddress);
            if ($isXahau) {
                $sellTx['TransactionType'] = 'URITokenCreateSellOffer';
                $sellTx['URITokenID'] = $tokenId;
                $sellTx['Amount'] = $amountField;
                $sellTx['Destination'] = $buyerAddress;
            } else {
                $sellTx['TransactionType'] = 'NFTokenCreateOffer';
                $sellTx['NFTokenID'] = $tokenId;
                $sellTx['Amount'] = $amountField;
                $sellTx['Flags'] = 1;
                $sellTx['Destination'] = $buyerAddress;
            }

            $sellTx['Memos'] = xahau()->buildMemos([
                // 'CollabathonID' => $collabathonId,
                'Type' => 'BUY_TICKET',
            ]);

            $signedSell = $this->signWithNode($sellTx, $hotSeed);
            
            $sellResult = $this->submitSigned($signedSell);

            $engineResult = $sellResult['engine_result'] ?? ($sellResult['meta']['TransactionResult'] ?? '');
            if (! $this->isFinalSuccess($engineResult)) {
                throw new \RuntimeException(
                    'NFT transfer offer failed: '.
                        ($sellResult['engine_result_message'] ?? $engineResult ?? 'Unknown')
                );
            }

            $sellOfferId = $this->waitForValidatedOffer($signedSell['hash'], $tokenId, config('xrpl.network_name'));

            /**
             * STEP 2: Buyer accepts the NFT transfer
             */
            $acceptTx = $this->buildBaseTx($buyerAddress);
            if ($isXahau) {
                $acceptTx['TransactionType'] = 'URITokenBuy';
                $acceptTx['URITokenID'] = $tokenId;
                $acceptTx['Amount'] = $amountField;
            } else {
                $acceptTx['TransactionType'] = 'NFTokenAcceptOffer';
                $acceptTx['NFTokenSellOffer'] = $sellOfferId;
            }

            $acceptTx['Memos'] = xahau()->buildMemos([
                // 'CollabathonID' => $collabathonId,
                'Type' => 'BUY_TICKET',
            ]);

            $signedAccept = $this->signWithNode($acceptTx, $buyerWallet->seed);
            
            $acceptResult = $this->submitSigned($signedAccept);

            $engineResult = $acceptResult['engine_result'] ?? ($acceptResult['meta']['TransactionResult'] ?? '');
            if (! $this->isFinalSuccess($engineResult)) {
                throw new \RuntimeException(
                    'NFT ownership transfer failed: '.
                        ($acceptResult['engine_result_message'] ?? $engineResult ?? 'Unknown')
                );
            }

            // 3. Finalize record with NFT purchase success
            $record->update([
                'status' => 'success',
                'tx_hash' => $acceptResult['tx_json']['hash'] ?? null,
                'response' => [
                    'nft_transfer' => $acceptResult,
                ],
                'validated_at' => now(),
            ]);

            return [
                'success' => true,
                'tx_hash' => $record->tx_hash,
                'message' => 'Ticket purchased successfully',
            ];
        } catch (\Throwable $e) {
            $record->update([
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()],
            ]);

            Log::info('NFT Transfer Failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function waitForValidatedOffer(string $txHash, string $tokenId, string $network): string
    {
        if ($network === 'xahau') {
            return $tokenId;
        }

        $maxAttempts = 10;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $attempt++;

            $res = $this->wsClient->request([
                'command' => 'tx',
                'transaction' => $txHash,
            ]);

            if (isset($res['result']['meta'])) {
                $nodes = $res['result']['meta']['AffectedNodes'] ?? [];
                foreach ($nodes as $node) {
                    if (isset($node['CreatedNode']) && $node['CreatedNode']['LedgerEntryType'] === 'NFTokenOffer') {
                        return $node['CreatedNode']['LedgerIndex'];
                    }
                }
            }

            Log::info("Waiting for NFTokenOffer validation... Attempt $attempt");
            sleep(2);
        }

        throw new \RuntimeException('Timeout waiting for NFTokenOffer to be validated on ledger.');
    }

    protected function getTokenInfo(string $tokenId, string $network): array
    {
        try {
            if ($network === 'xahau') {
                return $this->wsClient
                    ->request([
                        'command' => 'ledger_entry',
                        'uri_token' => $tokenId,
                    ])['result']['node'] ?? [];
            }

            $address = $this->manager->getCredentials('hot')['address'];
            $marker = null;

            do {
                $res = $this->wsClient->request(array_filter([
                    'command' => 'account_nfts',
                    'account' => $address,
                    'limit' => 100,
                    'marker' => $marker,
                ]));

                foreach ($res['result']['account_nfts'] ?? [] as $nft) {
                    if ($nft['NFTokenID'] === $tokenId) {
                        return [
                            'Owner' => $address,
                            'NFTokenID' => $tokenId,
                        ];
                    }
                }

                $marker = $res['result']['marker'] ?? null;
            } while ($marker);

            return [];
        } catch (\Throwable) {
            return [];
        }
    }

    /* =========================================================
     |  HELPERS
     |========================================================= */

    protected function buildBaseTx(string $account): array
    {
        $accountInfo = $this->wsClient->request([
            'command' => 'account_info',
            'account' => $account,
            'ledger_index' => 'current',
        ]);

        $feeInfo = $this->wsClient->request(['command' => 'fee']);

        $tx = [
            'Account' => $account,
            'Sequence' => $accountInfo['result']['account_data']['Sequence'],
            'Fee' => $feeInfo['result']['drops']['open_ledger_fee'] ?? '1000',
        ];

        if (config('xrpl.network_name') === 'xahau') {
            $tx['NetworkID'] = (int) config('xrpl.network.testnet.id', 21338);
        }

        if (!isset($tx['SourceTag']) && $this->connection) {
            $tx['SourceTag'] = $this->connection->getSourceTag();
        }

        return $tx;
    }

    protected function submitSigned(array $signed): array
    {
        return $this->wsClient->request([
            'command' => 'submit',
            'tx_blob' => $signed['tx_blob'],
        ])['result'] ?? [];
    }

    protected function isFinalSuccess(string $engineResult): bool
    {
        return $engineResult === 'tesSUCCESS';
    }

    protected function signWithNode(array &$tx, string $seed): array
    {
        if (!isset($tx['SourceTag']) && $this->connection) {
            $tx['SourceTag'] = $this->connection->getSourceTag();
        }

        if (config('xrpl.network_name') !== 'xahau') {
            $wallet = XRPLWallet::fromSeed($seed);
            return $wallet->sign($tx);
        }

        $process = new Process([
            config('xrpl.node', 'node'),
            base_path('node/sign.js'),
        ]);

        $process->setEnv(['XAHAU_SEED' => $seed]);
        $process->setInput(json_encode($tx));
        $process->setTimeout(100);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $out = json_decode($process->getOutput(), true);

        if (! isset($out['tx_blob'])) {
            throw new \RuntimeException('Invalid signer output');
        }

        return $out;
    }

    /**
     * Formats the amount for XRP (string) or Issued Currencies (array)
     */
    protected function formatAmount($amount, $currency, $isNative)
    {
        if ($isNative) {
            return bcmul((string) $amount, '1000000', 0);
        }

        return [
            'currency' => $currency,
            'issuer' => config('xrpl.cold_wallet.address'),
            'value' => number_format((float) $amount, 8, '.', ''),
        ];
    }

    /**
     * Calculates SendMax to account for transfer fees
     */
    protected function calculateSendMax($amount, $currency)
    {
        $feePercent = (float) config('xrpl.issuer_settings.transfer_fee_percent', 0);
        $multiplier = 1 + ($feePercent / 100);
        $maxSpend = bcmul((string) $amount, (string) $multiplier, 8);

        return [
            'currency' => $currency,
            'issuer' => config('xrpl.cold_wallet.address'),
            'value' => $maxSpend,
        ];
    }

    /**
     * Fetches the next sequence number for an account
     */
    protected function getAccountSequence($address)
    {
        $info = $this->wsClient->request([
            'command' => 'account_info',
            'account' => $address,
            'ledger_index' => 'current',
        ]);

        if (! isset($info['result']['account_data']['Sequence'])) {
            throw new \RuntimeException("Account $address not found.");
        }

        return $info['result']['account_data']['Sequence'];
    }
}
