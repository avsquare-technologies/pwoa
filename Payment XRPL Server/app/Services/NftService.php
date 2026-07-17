<?php

namespace App\Services;

use App\Models\Nft;
use App\Models\OrderNft;
use App\Models\Wallet;
use Hardcastle\XRPL_PHP\Core\RippleAddressCodec\AddressCodec;
use Hardcastle\XRPL_PHP\Wallet\Wallet as XRPLWallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use App\Traits\NormalizesXRPLCurrency;

class NftService
{
    use NormalizesXRPLCurrency;

    protected $connection;
    protected $manager;

    public function __construct(XrplConnection $connection, SystemWalletManager $manager)
    {
        $this->connection = $connection;
        $this->manager = $manager;
    }

    protected function isXahau(): bool
    {
        return config('xrpl.network_name') === 'xahau';
    }

    protected function signWithNode(array &$tx, string $seed): array
    {
        if (!isset($tx['SourceTag']) && $this->connection) {
            $tx['SourceTag'] = $this->connection->getSourceTag();
        }

        if (!$this->isXahau()) {
            $wallet = XRPLWallet::fromSeed($seed);
            return $wallet->sign($tx);
        }

        $process = new Process([
            config('xrpl.node', 'node'),
            base_path('node/sign.js'),
        ]);

        $process->setEnv([
            'XAHAU_SEED' => $seed,
        ]);

        $process->setInput(json_encode($tx));
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                'Node signing failed: ' . $process->getErrorOutput()
            );
        }

        $output = json_decode($process->getOutput(), true);

        if (!isset($output['tx_blob'], $output['hash'])) {
            throw new \RuntimeException('Invalid signer output');
        }

        return $output;
    }

    public function getCreatedTokenId(WsClient $ws, string $txHash, int $attempts = 10): ?string
    {
        for ($i = 0; $i < $attempts; $i++) {
            sleep(2);
            $response = $ws->request(['command' => 'tx', 'transaction' => $txHash]);

            if (isset($response['result']['meta'])) {
                $meta = $response['result']['meta'];

                if ($this->isXahau()) {
                    foreach ($meta['AffectedNodes'] as $node) {
                        if (
                            isset($node['CreatedNode']) &&
                            ($node['CreatedNode']['LedgerEntryType'] ?? '') === 'URIToken'
                        ) {
                            return $node['CreatedNode']['LedgerIndex'];
                        }
                    }
                } else {
                    if (isset($meta['nftoken_id'])) {
                        return $meta['nftoken_id'];
                    }
                }
            }
        }
        return null;
    }

    public function mintNft(
        string $creatorAddress,
        string $uri,
        int $taxon = 0,
        int $transferFee = 0,
        bool $isTransferable = true,
        bool $isBurnable = true,
        ?string $sellAmount = null
    ): array {
        $ws = null;
        try {
            $wallet = Wallet::where('address', $creatorAddress)->firstOrFail();
            $ws = $this->connection->getClient();
            $accountInfo = $this->getAccountInfo($ws, $wallet->address);
            $uriHex = strtoupper(bin2hex(trim($uri)));

            Log::info('Minting NFT', $accountInfo);
            $nft = Nft::create([
                'owner_address' => $wallet->address,
                'original_creator_address' => $wallet->address,
                'taxon' => $taxon,
                'uri' => $uri,
                'status' => Nft::STATUS_PENDING,
            ]);

            // Construct Transaction based on Network
            if ($this->isXahau()) {
                $tx = [
                    'TransactionType' => 'URITokenMint',
                    'Account' => $wallet->address,
                    'URI' => $uriHex,
                    'Taxon' => $taxon,
                    'Flags' => $isBurnable ? 1 : 0,
                ];
                if (config('xrpl.network.testnet.id')) {
                    $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
                }
            } else {
                $flags = 0;
                if ($isBurnable) $flags |= 1;
                if ($isTransferable) $flags |= 8;
                $tx = [
                    'TransactionType' => 'NFTokenMint',
                    'Account' => $wallet->address,
                    'URI' => $uriHex,
                    'NFTokenTaxon' => $taxon,
                    'TransferFee' => $transferFee,
                    'Flags' => $flags,
                ];
                // if (config('xrpl.network.testnet.id')) {
                //     $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
                // }
            }


            $tx['Sequence'] = $accountInfo['sequence'];
            $tx['Fee'] = (string) $this->getFee($ws);
            $tx['LastLedgerSequence'] = $accountInfo['ledger'] + 20;


            // $signed = $this->signWithNode($tx, $wallet->seed);
            // $submit = $ws->request(['command' => 'submit', 'tx_blob' => $signed['tx_blob']]);

            $maxRetries = 5;
            $attempt = 0;
            $submit = null;

            while ($attempt < $maxRetries) {

                $accountInfo = $this->getAccountInfo($ws, $wallet->address);

                $tx['Sequence'] = $accountInfo['sequence'];
                $tx['Fee'] = (string) $this->getFee($ws);
                $tx['LastLedgerSequence'] = $accountInfo['ledger'] + 20;

                $signed = $this->signWithNode($tx, $wallet->seed);

                $submit = $ws->request([
                    'command' => 'submit',
                    'tx_blob' => $signed['tx_blob']
                ]);

                $engineResult = $submit['result']['engine_result'] ?? null;

                Log::info('NFT mint attempt', [
                    'attempt' => $attempt + 1,
                    'sequence' => $tx['Sequence'],
                    'engine_result' => $engineResult
                ]);

                if ($engineResult === 'tesSUCCESS') {
                    break;
                }

                if ($engineResult === 'tefPAST_SEQ') {

                    Log::warning('Sequence already used. Retrying mint...');

                    usleep(500000);

                    $attempt++;
                    continue;
                }

                throw new \RuntimeException(
                    $submit['result']['engine_result_message'] ?? 'Mint failed'
                );
            }

            if ($attempt >= $maxRetries) {
                throw new \RuntimeException('Mint retry limit exceeded due to sequence conflicts');
            }

            $txHash = $submit['result']['tx_json']['hash'];
            $nft->update(['status' => Nft::STATUS_MINTED, 'tx_hash' => $txHash]);

            $tokenId = $this->getCreatedTokenId($ws, $txHash);
            $sellOfferData = null;

            if ($tokenId) {
                $nft->update(['nft_id' => $tokenId]);
                if ($sellAmount && $sellAmount > 0) {
                    $sellOfferData = $this->createSellOffer($wallet, $tokenId, $sellAmount, $ws);
                }
            }

            return [
                'success' => true,
                'nft_id' => $tokenId,
                'tx_hash' => $txHash,
                'sell_offer' => $sellOfferData
            ];
        } catch (\Throwable $e) {
            if (isset($nft)) $nft->update(['status' => Nft::STATUS_FAILED, 'error_message' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($ws) $ws->close();
        }
    }

    public function createSellOffer(Wallet $wallet, string $tokenId, string $amount, ?WsClient $ws = null): array
    {
        $shouldClose = false;
        if (!$ws) {
            $ws = $this->connection->getClient();
            $shouldClose = true;
        }

        $accountInfo = $this->getAccountInfo($ws, $wallet->address);
        $amountField = xrplAmountField($amount);

        if ($this->isXahau()) {
            $tx = [
                'TransactionType' => 'URITokenCreateSellOffer',
                'URITokenID' => $tokenId,
            ];
        } else {
            $tx = [
                'TransactionType' => 'NFTokenCreateOffer',
                'NFTokenID' => $tokenId,
                'Flags' => 1,
            ];
        }

        $tx += [
            'Account' => $wallet->address,
            'Amount' => $amountField,
            'Sequence' => $accountInfo['sequence'],
            'Fee' => (string) $this->getFee($ws),
        ];

        if ($this->isXahau() && config('xrpl.network.testnet.id')) {
            $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
        }

        $signed = $this->signWithNode($tx, $wallet->seed);
        $submit = $ws->request(['command' => 'submit', 'tx_blob' => $signed['tx_blob']]);

        if ($shouldClose) $ws->close();

        return [
            'tx_hash' => $submit['result']['tx_json']['hash'] ?? null,
            'status' => $submit['result']['engine_result'] ?? 'failed'
        ];
    }


    // public function buyNftNew(string $buyerAddress, string $tokenIdOrOfferIndex, string $amount): array
    // {
    //     $ws = $this->connection->getClient();
    //     try {
    //         $buyerWallet = Wallet::where('address', $buyerAddress)->firstOrFail();
    //         $accountInfo = $this->getAccountInfo($ws, $buyerAddress);

    //         if ($this->isXahau()) {
    //             $tx = [
    //                 'TransactionType' => 'URITokenBuy',
    //                 'URITokenID' => $tokenIdOrOfferIndex,
    //             ];
    //         } else {
    //             $tx = [
    //                 'TransactionType' => 'NFTokenAcceptOffer',
    //                 'NFTokenSellOffer' => $tokenIdOrOfferIndex,
    //             ];
    //         }

    //         $tx += [
    //             'Account' => $buyerAddress,
    //             'Amount' => xrplAmountField($amount),
    //             'Sequence' => $accountInfo['sequence'],
    //             'Fee' => (string) $this->getFee($ws),
    //         ];

    //         if ($this->isXahau() && config('xrpl.network.testnet.id')) {
    //             $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
    //         }

    //         $signed = $this->signWithNode($tx, $buyerWallet->seed);
    //         $submit = $ws->request(['command' => 'submit', 'tx_blob' => $signed['tx_blob']]);

    //         return [
    //             'success' => ($submit['result']['engine_result'] === 'tesSUCCESS'),
    //             'tx_hash' => $submit['result']['tx_json']['hash']
    //         ];
    //     } finally {
    //         $ws->close();
    //     }
    // }

    public function buyNft($orderId, $buyerAddress, $tokenId, $amount): array
    {
        Log::info('Buy NFT', ['order_id' => $orderId, 'buyer_address' => $buyerAddress, 'token_id' => $tokenId, 'amount' => $amount]);
        $ws = null;
        try {
            $buyerWallet = Wallet::where('address', $buyerAddress)->firstOrFail();

            $nft = Nft::where('nft_id', $tokenId)->firstOrFail();

            $sellerAddress = $nft->owner_address;
            $sellerWallet = Wallet::where('address', $sellerAddress)->firstOrFail();
            $sellerId = $sellerWallet->user_id;

            $ws = $this->connection->getClient();
            $accountInfo = $this->getAccountInfo($ws, $buyerAddress);
            // $amountField = xrplAmountField($amount);

            if (!$this->isXahau()) {
                $offersResponse = $ws->request([
                    'command' => 'nft_sell_offers',
                    'nft_id'  => $tokenId
                ]);

                $offers = $offersResponse['result']['offers'] ?? [];

                if (empty($offers)) {
                    throw new \RuntimeException("No sell offers found on the ledger for this NFT.");
                }

                $offerIndex = $offers[0]['nft_offer_index'];
            } else {
                // Xahau uses the TokenID directly
                $offerIndex = $tokenId;
            }


            if ($this->isXahau()) {
                $tx = [
                    'TransactionType' => 'URITokenBuy',
                    'URITokenID' => $tokenId,
                    'Amount'          => xrplAmountField($amount),
                ];
            } else {
                $tx = [
                    'TransactionType' => 'NFTokenAcceptOffer',
                    'NFTokenSellOffer' => $offerIndex,

                ];
            }

            $tx += [
                'Account' => $buyerAddress,
                'Sequence' => $accountInfo['sequence'],
                'Fee' => (string) $this->getFee($ws),
                'LastLedgerSequence' => $accountInfo['ledger'] + 20,
            ];

            // $tx['LastLedgerSequence'] = $accountInfo['ledger'] + 20;

            if (config('xrpl.network_name') === 'xahau') {
                $tx['NetworkID'] = (int) config('xrpl.network.testnet.id', 21338);
            }

            Log::info("tx", $tx);

            $signed = $this->signWithNode($tx, $buyerWallet->seed);
            $submit = $ws->request(['command' => 'submit', 'tx_blob' => $signed['tx_blob']]);
            Log::info("submit", $submit);
            if (($submit['result']['engine_result'] ?? null) !== 'tesSUCCESS') {

                throw new \RuntimeException('Buy Failed: ' . ($submit['result']['engine_result_message'] ?? 'Unknown'));
            }

            $txHash = $submit['result']['tx_json']['hash'];

            $this->distributeNftSaleRevenue($sellerWallet, $amount, $nft);

            DB::transaction(function () use ($nft, $buyerWallet, $sellerAddress, $sellerId, $amount, $txHash, $tokenId, $orderId) {

                OrderNft::create([
                    'nft_db_id' => $nft->id,
                    'nft_token_id' => $tokenId,
                    'order_id' => $orderId,
                    'buyer_address' => $buyerWallet->address,
                    'buyer_id' => $buyerWallet->user_id,
                    'seller_address' => $sellerAddress,
                    'seller_id' => $sellerId,
                    'amount' => $amount,
                    'tx_hash' => $txHash,
                    'status' => 'completed'
                ]);
                $nft->update([
                    'owner_address' => $buyerWallet->address,
                    'user_id' => $buyerWallet->user_id,
                    'status' => Nft::STATUS_CONFIRMED,
                    'response' => array_merge($nft->response ?? [], ['buy_tx' => $txHash])
                ]);
            });
            return ['success' => true, 'tx_hash' => $txHash];
        } catch (\Throwable $e) {
            Log::error('Buy NFT Failed', ['error' => $e->getMessage()]);
            throw $e;
        } finally {
            if ($ws)
                $ws->close();
        }
    }

    protected function distributeNftSaleRevenue(Wallet $sellerWallet, float $totalAmount, Nft $nft): void
    {
        try {
            $currency = config('xrpl.currency', 'FEE');
            $currency = $this->normalizeCurrency($currency);


            $treasuryPct      = (float) config('transactions.mint.treasury_pct', 2.5);
            $originalOwnerPct = (float) config('transactions.mint.original_owner_pct', 1.0);

            $treasuryAmount      = round($totalAmount * $treasuryPct / 100, 6);
            $originalOwnerAmount = round($totalAmount * $originalOwnerPct / 100, 6);

            if ($treasuryAmount > 0) {
                $hotWalletAddress = $this->manager->getCredentials('hot')['address'];

                $memos = xahau()->buildMemos([
                    'type' => 'NFT Treasury',
                    'seller' => $sellerWallet->address
                ]);

                if ($sellerWallet->address !== $hotWalletAddress) {
                    $this->manager->sendSystemPayment(
                        'hot',
                        $hotWalletAddress,
                        $treasuryAmount,
                        $currency,
                        true,
                        $sellerWallet->address,
                        false,
                        $memos
                    );

                    sleep(1);
                }
            }

            if ($originalOwnerAmount > 0) {
                $recipientAddress = $nft->original_creator_address;

                $memos = xahau()->buildMemos([
                    'type' => 'NFT Original Owner',
                    'seller' => $sellerWallet->address
                ]);

                if (!empty($recipientAddress) && $sellerWallet->address !== $recipientAddress) {
                    $this->manager->sendSystemPayment(
                        'original_buyer',
                        $recipientAddress,
                        $originalOwnerAmount,
                        $currency,
                        true,
                        $sellerWallet->address,
                        false,
                        $memos
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('Revenue Distribution Failure', [
                'nft_id' => $nft->nft_id,
                'error'  => $e->getMessage()
            ]);
        }
    }
    public function mintBatchByCount(
        string $creatorAddress,
        string $uri,
        int $count,
        int $taxon,
        float $price = 0
    ): array {

        set_time_limit(0);

        $network = config('xrpl.network_name');
        $batchId = (string) Str::uuid();

        $attempted = 0;
        $processed = 0;
        $failed    = 0;

        $mintedData = [];
        $ws = null;

        Cache::put("batch_progress_{$batchId}", [
            'total'     => $count,
            'processed' => 0,
            'failed'    => 0,
            'status'    => 'starting'
        ], now()->addHours(6));

        try {

            $userWallet = Wallet::where('address', $creatorAddress)->firstOrFail();

            $creds = $this->manager->getCredentials('hot');
            $adminWallet = XRPLWallet::fromSeed($creds['seed']);

            $ws = $this->connection->getClient();

            $this->ensureAdminHasReserve($ws, $adminWallet->getAddress(), 0, $count);

            $this->ensureMinterAuthorized($ws, $userWallet, $adminWallet);

            $info = $this->getAccountInfo($ws, $adminWallet->getAddress());
            $sequence = $info['sequence'];

            Log::info("[BATCH {$batchId}] Starting batch mint: {$count} NFTs");
            Log::info("[BATCH {$batchId}] Starting Sequence: {$sequence}");


            for ($i = 0; $i < $count; $i++) {

                $attempted++;
                $retryLimit = 5;
                $success = false;

                while (!$success && $retryLimit > 0) {

                    try {

                        $finalUri = ($count === 1) ? $uri : ($uri . '#' . ($i + 1));
                        $uniqueUriHex = strtoupper(
                            bin2hex($finalUri)
                        );

                        Log::info("[BATCH {$batchId}] Minting NFT #" . ($i + 1));
                        Log::info("[BATCH {$batchId}] Using Sequence: {$sequence}");

                        // ✅ Mint строго serial
                        $mintResult = $this->mintSerialNFT(
                            $ws,
                            $adminWallet,
                            $userWallet->address,
                            $network,
                            $uniqueUriHex,
                            $taxon,
                            $sequence
                        );

                        $tokenId = $mintResult['nft_token_id'];
                        $txHash  = $mintResult['tx_hash'];

                        $processed++;
                        $success = true;

                        // ✅ Save mint result
                        $mintedData[] = [
                            'index'         => $i + 1,
                            'owner_wallet_address' => $userWallet->address,
                            'nft_token_id'  => $tokenId,
                            'tx_hash'       => $txHash,
                            'ticket_sequence' => $sequence - 1,
                            'status'        => 'minted'
                        ];

                        // ✅ DB Insert
                        DB::table('collabathon_nfts')->insert([
                            'batch_id'        => $batchId,
                            'ticket_index'    => $i + 1,
                            'ticket_sequence' => $sequence - 1,
                            'creator_address' => $userWallet->address,
                            'tx_hash'         => $txHash,
                            'nft_token_id'    => $tokenId,
                            'status'          => 'confirmed',
                            'created_at'      => now()
                        ]);

                        Log::info("[BATCH {$batchId}] Minted Token: {$tokenId}");
                    } catch (\Throwable $e) {

                        $retryLimit--;

                        Log::warning("[BATCH {$batchId}] Mint retry failed: " . $e->getMessage());

                        // ⚠️ Refresh sequence if mismatch
                        if (
                            str_contains($e->getMessage(), 'tefPAST_SEQ') ||
                            str_contains($e->getMessage(), 'terPRE_SEQ')
                        ) {

                            Log::warning("[BATCH {$batchId}] Refreshing Sequence...");

                            $info = $this->getAccountInfo($ws, $adminWallet->getAddress());
                            $sequence = $info['sequence'];
                        }

                        // ⚠️ Re-authorize minter if unauthorized
                        if (str_contains($e->getMessage(), 'tefBAD_AUTH')) {
                            Log::warning("[BATCH {$batchId}] Minter not authorized. Forcing AccountSet...");
                            $this->ensureMinterAuthorized($ws, $userWallet, $adminWallet, true);
                        }

                        // Wait before retry
                        sleep(2);

                        if ($retryLimit === 0) {
                            throw $e;
                        }
                    }
                }

                // ✅ Cache Progress
                Cache::put("batch_progress_{$batchId}", [
                    'total'     => $count,
                    'attempted' => $attempted,
                    'processed' => $processed,
                    'failed'    => $failed,
                    'status'    => "Minting {$processed}/{$count}"
                ], now()->addHours(6));
            }

            // ======================================================
            // ✅ FINISHED
            // ======================================================

            Cache::put("batch_progress_{$batchId}", [
                'total'     => $count,
                'processed' => $processed,
                'failed'    => $failed,
                'status'    => 'completed'
            ], now()->addHours(1));

            return [
                'success'   => true,
                'batch_id'  => $batchId,
                'processed' => $processed,
                'failed'    => 0,
                'tickets'    => $mintedData
            ];
        } catch (\Throwable $e) {

            Cache::put("batch_progress_{$batchId}", [
                'total'  => $count,
                'status' => 'failed',
                'error'  => $e->getMessage()
            ], now()->addHours(2));

            throw $e;
        } finally {
            if ($ws) {
                $ws->close();
            }
        }
    }

    protected function mintSerialNFT(
        WsClient $ws,
        XRPLWallet $adminWallet,
        string $issuer,
        string $network,
        string $uriHex,
        int $taxon,
        int &$sequence
    ): array {

        // -----------------------------
        // Build TX
        // -----------------------------
        $tx = [
            'TransactionType' => $network === 'xahau'
                ? 'URITokenMint'
                : 'NFTokenMint',

            'Account'  => $adminWallet->getAddress(),
            'URI'      => $uriHex,
            'Sequence' => $sequence,
            'Fee'      => (string) $this->getFee($ws),

            // Prevent hanging forever
            'LastLedgerSequence' =>
            $this->getAccountInfo($ws, $adminWallet->getAddress())['ledger'] + 30,
        ];

        // -----------------------------
        // XAHAU Fields
        // -----------------------------
        if ($network === 'xahau') {

            $tx['Taxon']     = $taxon;
            $tx['Flags']     = 1;
            $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
        }

        // -----------------------------
        // XRPL Fields
        // -----------------------------
        else {

            $tx['Issuer']       = $issuer;
            $tx['NFTokenTaxon'] = $taxon;
            $tx['Flags']        = 9;
        }

        Log::info("Minting NFT for issuer: {$issuer}, network: {$network}", ['tx' => $tx]);

        // -----------------------------
        // Sign TX
        // -----------------------------
        $signed = $this->signWithNode($tx, $adminWallet->getSeed());
        Log::info("Signed NFT Mint Blob", ['signed' => $signed]);

        // ============================================================
        // ✅ XAHAU Path: submit_wait works
        // ============================================================
        if ($network === 'xahau') {

            $response = $ws->request([
                'command' => 'submit_wait',
                'tx_blob' => $signed['tx_blob']
            ]);

            $resultCode =
                $response['result']['meta']['TransactionResult'] ?? null;

            if ($resultCode !== 'tesSUCCESS') {
                throw new \Exception(
                    "XAHAU Mint failed at Sequence {$sequence}: {$resultCode}"
                );
            }

            $hash =
                $response['result']['tx_json']['hash'];

            $tokenId =
                $this->calculateURITokenID(
                    $adminWallet->getAddress(),
                    $uriHex
                );

            // ✅ Increment only after success
            $sequence++;

            return [
                'nft_token_id' => $tokenId,
                'tx_hash'      => $hash
            ];
        }

        // ============================================================
        // ✅ XRPL Path: submit_wait is unreliable, use submit + poll
        // ============================================================
        $submit = $ws->request([
            'command' => 'submit',
            'tx_blob' => $signed['tx_blob']
        ]);

        $engine =
            $submit['result']['engine_result'] ?? null;

        if ($engine !== 'tesSUCCESS') {

            $msg =
                $submit['result']['engine_result_message'] ?? 'Unknown';

            throw new \Exception(
                "XRPL Mint failed at Sequence {$sequence}: {$engine} - {$msg}"
            );
        }

        $hash = $submit['result']['tx_json']['hash'];

        $sequence++;
        // ✅ Wait until validated
        $meta =
            $this->waitForTxMeta($ws, $hash);

        $tokenId =
            $this->parseNFTokenIDFromMeta($meta);

        // ✅ Increment only after confirmed success

        return [
            'nft_token_id' => $tokenId,
            'tx_hash'      => $hash
        ];
    }



    protected function ensureMinterAuthorized($ws, $userWallet, $adminWallet, bool $force = false): void
    {
        $info = $ws->request([
            'command' => 'account_info',
            'account' => $userWallet->address,
            'ledger_index' => 'current'
        ]);

        $currentMinter = $info['result']['account_data']['NFTokenMinter'] ?? null;

        if ($force || $currentMinter !== $adminWallet->getAddress()) {
            Log::info("Authorizing Admin {$adminWallet->getAddress()} for User {$userWallet->address}");


            $seq = $info['result']['account_sequence_next']
                ?? $info['result']['account_data']['Sequence'];

            $tx = [
                'TransactionType' => 'AccountSet',
                'Account' => $userWallet->address,
                'SetFlag' => 10, // asfNFTokenMinter
                'NFTokenMinter' => $adminWallet->getAddress(),
                'Sequence' => $seq,
                'Fee' => (string) $this->getFee($ws),
            ];

            // Note: This signs with the USER seed because the user is granting permission
            $signed = $this->signWithNode($tx, $userWallet->seed);
            $res = $this->submitTx($ws, config('xrpl.network_name'), $signed['tx_blob']);

            $hash = $res['tx_json']['hash'] ?? $signed['hash'] ?? null;
            if ($hash) {
                $this->waitForTxMeta($ws, $hash);
            } else {
                sleep(4);
            }
        }
    }

    protected function createTickets(
        WsClient $ws,
        Wallet|XRPLWallet $adminWallet,
        int $count,
        string $network
    ): array {
        $info = $this->getAccountInfoForBatch($ws, $adminWallet->getAddress());

        $tx = [
            'TransactionType' => 'TicketCreate',
            'Account' => $adminWallet->getAddress(),
            'TicketCount' => $count,
            'Sequence' => $info['sequence'],
            'Fee' => (string) $this->getFee($ws),
        ];

        if ($network === 'xahau') {
            $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
        }

        $signed = $this->signWithNode($tx, $adminWallet->getSeed());

        // submit
        $result = $this->submitTx($ws, $network, $signed['tx_blob']);

        // ⏳ WAIT FOR VALIDATION ON XAHAU
        if ($network === 'xahau') {
            $hash = $result['tx_json']['hash'] ?? null;

            if (!$hash) {
                throw new \Exception('Missing TicketCreate hash on Xahau');
            }

            // reuse your existing validator
            $this->waitForTxMeta($ws, $hash);
        }

        // ✅ tickets now exist on ledger
        $res = $ws->request([
            'command' => 'account_objects',
            'account' => $adminWallet->getAddress(),
            'type' => 'ticket'
        ]);

        if (!isset($res['result']['account_objects'])) {
            throw new \Exception('No tickets found after validation');
        }

        return collect($res['result']['account_objects'])
            ->pluck('TicketSequence')
            ->sort()
            ->values()
            ->toArray();
    }

    protected function getAccountInfoForBatch(WsClient $ws, string $address): array
    {
        $info = $ws->request([
            'command' => 'account_info',
            'account' => $address,
            'ledger_index' => 'validated',
        ]);

        if (!isset($info['result']['account_data']['Sequence'])) {
            throw new \Exception("Missing Sequence");
        }

        return [
            'sequence' => (int) $info['result']['account_data']['Sequence'],
            'ledger'   => (int) ($info['result']['ledger_index'] ?? 0),
        ];
    }



    protected function submitTx(
        WsClient $ws,
        string $network,
        string $txBlob
    ): array {
        $command = ($network === 'xahau') ? 'submit_wait' : 'submit';

        $res = $ws->request([
            'command' => $command,
            'tx_blob' => $txBlob
        ]);

        if (!isset($res['result'])) {
            throw new \Exception('Submit failed: ' . json_encode($res));
        }

        $code = ($network === 'xahau')
            ? ($res['result']['meta']['TransactionResult'] ?? null)
            : ($res['result']['engine_result'] ?? null);

        if ($code !== 'tesSUCCESS') {
            throw new \Exception("Ledger rejected tx: {$code}");
        }

        return $res['result'];
    }


    protected function mintNFTForBatch(
        WsClient $ws,
        $adminWallet,
        $issuer,
        string $network,
        int $ticket,
        string $uriHex,
        int $taxon
    ) {
        $tx = ($network === 'xahau')
            ? [
                'TransactionType' => 'URITokenMint',
                'Account' => $adminWallet->getAddress(),
                'URI' => $uriHex,
                'Taxon' => $taxon,
                'Flags' => 1,
                'TicketSequence' => $ticket,
                'Sequence' => 0,
                'NetworkID' => (int) config('xrpl.network.testnet.id'),
                'Fee' => (string) $this->getFee($ws),
            ]
            : [
                'TransactionType' => 'NFTokenMint',
                'Account' => $adminWallet->getAddress(),
                'Issuer' => $issuer,
                'URI' => $uriHex,
                'NFTokenTaxon' => $taxon,
                'Flags' => 8,
                'TicketSequence' => $ticket,
                'Sequence' => 0,
                'Fee' => (string) $this->getFee($ws),
            ];

        $signed = $this->signWithNode($tx, $adminWallet->getSeed());
        $res = $this->submitTx($ws, $network, $signed['tx_blob']);

        // Capture the Hash
        $hash = $res['tx_json']['hash'] ?? $signed['hash'] ?? null;

        if ($network === 'xahau') {
            $tokenId = $this->calculateURITokenID($adminWallet->getAddress(), $uriHex);
        } else {
            $meta = $this->waitForTxMeta($ws, $hash);
            $tokenId = $this->parseNFTokenIDFromMeta($meta);
        }

        return [
            'nft_token_id' => $tokenId,
            'tx_hash'      => $hash
        ];
    }


    protected function createSellOfferForBatch(
        WsClient $ws,
        $adminWallet,
        string $network,
        int $ticket,
        string $tokenId,
        float $price
    ): void {
        // $amount = (string) bcmul((string) $price, '1000000', 0);
        $amount = xrplAmountField($price);

        $tx = [
            'Account' => $adminWallet->getAddress(),
            'TicketSequence' => $ticket,
            'Sequence' => 0,
            'Fee' => (string) $this->getFee($ws),
        ];

        if ($network === 'xahau') {
            $tx['TransactionType'] = 'URITokenCreateSellOffer';
            $tx['URITokenID'] = $tokenId;
            $tx['Amount'] = $amount;
            $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
        } else {
            $tx['TransactionType'] = 'NFTokenCreateOffer';
            $tx['NFTokenID'] = $tokenId;
            $tx['Amount'] = $amount;
            $tx['Flags'] = 1;
        }

        $signed = $this->signWithNode($tx, $adminWallet->getSeed());
        $this->submitTx($ws, $network, $signed['tx_blob']);
    }

    protected function waitForTxMeta(WsClient $ws, string $hash, int $retries = 20): array
    {
        for ($i = 0; $i < $retries; $i++) {

            $tx = $ws->request([
                'command' => 'tx',
                'transaction' => $hash
            ]);

            // Not ready yet
            if (($tx['result']['validated'] ?? false) !== true) {
                Log::warning("⏳ Waiting for validation...", [
                    "hash" => $hash,
                    "attempt" => $i + 1
                ]);
                sleep(2);
                continue;
            }

            // Meta exists
            if (isset($tx['result']['meta'])) {

                $result = $tx['result']['meta']['TransactionResult'] ?? '';

                if ($result !== 'tesSUCCESS') {
                    throw new \Exception("Ledger rejected tx: {$result}");
                }

                return $tx['result']['meta'];
            }

            sleep(2);
        }

        throw new \Exception("Transaction not validated after waiting: {$hash}");
    }


    private function parseNFTokenIDFromMeta(array $meta): string
    {
        // ✅ FAST PATH: XRPL already gives us the ID
        if (isset($meta['nftoken_id'])) {
            return $meta['nftoken_id'];
        }

        // Fallback: parse NFTokenPage (older nodes / edge cases)
        foreach ($meta['AffectedNodes'] as $node) {

            if (
                isset($node['ModifiedNode']) &&
                $node['ModifiedNode']['LedgerEntryType'] === 'NFTokenPage' &&
                isset($node['ModifiedNode']['FinalFields']['NFTokens'])
            ) {
                $tokens = $node['ModifiedNode']['FinalFields']['NFTokens'];
                $last = end($tokens);

                if (isset($last['NFToken']['NFTokenID'])) {
                    return $last['NFToken']['NFTokenID'];
                }
            }

            if (
                isset($node['CreatedNode']) &&
                $node['CreatedNode']['LedgerEntryType'] === 'NFTokenPage' &&
                isset($node['CreatedNode']['NewFields']['NFTokens'])
            ) {
                $tokens = $node['CreatedNode']['NewFields']['NFTokens'];
                $last = end($tokens);

                if (isset($last['NFToken']['NFTokenID'])) {
                    return $last['NFToken']['NFTokenID'];
                }
            }
        }

        Log::error('NFTokenID parse failed. Full meta: ' . json_encode($meta));

        throw new \Exception(
            'NFTokenMint succeeded but NFTokenID could not be resolved'
        );
    }



    protected function ensureTickets(WsClient &$ws, Wallet $wallet, int $needed): array
    {
        $objects = $ws->request(['command' => 'account_objects', 'account' => $wallet->address, 'type' => 'ticket']);
        $tickets = [];
        foreach ($objects['result']['account_objects'] ?? [] as $obj) {
            $tickets[] = $obj['TicketSequence'];
        }

        $missing = $needed - count($tickets);
        if ($missing > 0) {
            $accountInfo = $this->getAccountInfo($ws, $wallet->address);
            $tx = [
                'TransactionType' => 'TicketCreate',
                'Account' => $wallet->address,
                'TicketCount' => min($missing, 250),
                'Sequence' => $accountInfo['sequence'],
                'Fee' => (string) $this->getFee($ws),
            ];
            $signed = $this->signWithNode($tx, $wallet->seed);
            $ws->request(['command' => 'submit', 'tx_blob' => $signed['tx_blob']]);
            sleep(4);
            return $this->ensureTickets($ws, $wallet, $needed);
        }

        return array_slice($tickets, 0, $needed);
    }

    // protected function ensureAdminHasReserve(
    //     WsClient $ws,
    //     string $adminAddress,
    //     int $ticketsNeeded,
    //     int $nftsNeeded
    // ): void {
    //     $info = $ws->request([
    //         'command' => 'account_info',
    //         'account' => $adminAddress,
    //         'ledger_index' => 'validated'
    //     ]);

    //     if (!isset($info['result']['account_data']['Balance'])) {
    //         throw new \Exception('Unable to fetch admin wallet balance');
    //     }

    //     $balanceXrp = bcdiv(
    //         $info['result']['account_data']['Balance'],
    //         '1000000',
    //         6
    //     );

    //     $baseReserve = 10;
    //     $perObjectReserve = 2;

    //     $requiredReserve =
    //         $baseReserve +
    //         (($ticketsNeeded + $nftsNeeded) * $perObjectReserve);

    //     $safetyBuffer = 2;

    //     $requiredTotal = $requiredReserve + $safetyBuffer;

    //     if ($balanceXrp < $requiredTotal) {
    //         throw new \Exception(
    //             "Admin wallet has insufficient reserve. " .
    //                 "Balance: {$balanceXrp} XRP, " .
    //                 "Required: {$requiredTotal} XRP"
    //         );
    //     }
    // }

    protected function ensureAdminHasReserve(
        WsClient $ws,
        string $adminAddress,
        int $ticketsNeeded,
        int $nftsNeeded
    ): void {
        // Fetch live reserve values
        $serverState = $ws->request(['command' => 'server_state']);
        $baseReserveDrops = $serverState['result']['state']['validated_ledger']['reserve_base'];
        $perObjectReserveDrops = $serverState['result']['state']['validated_ledger']['reserve_inc'];

        $baseReserve = bcdiv((string) $baseReserveDrops, '1000000', 6);
        $perObjectReserve = bcdiv((string) $perObjectReserveDrops, '1000000', 6);

        // Fetch admin balance
        $info = $ws->request([
            'command'      => 'account_info',
            'account'      => $adminAddress,
            'ledger_index' => 'validated'
        ]);

        if (!isset($info['result']['account_data']['Balance'])) {
            throw new \Exception('Unable to fetch admin wallet balance');
        }

        $balanceXrp = bcdiv(
            $info['result']['account_data']['Balance'],
            '1000000',
            6
        );

        // NFTs live in pages of up to 32 — reserve per page, not per NFT
        $nftPageReserves = (int) ceil($nftsNeeded / 32);
        $ownerObjects    = $ticketsNeeded + $nftPageReserves;

        $requiredReserve = bcadd(
            $baseReserve,
            bcmul((string) $ownerObjects, $perObjectReserve, 6),
            6
        );

        $safetyBuffer = '2.000000';
        $requiredTotal = bcadd($requiredReserve, $safetyBuffer, 6);

        // ✅ Safe arbitrary-precision comparison
        if (bccomp($balanceXrp, $requiredTotal, 6) === -1) {
            throw new \Exception(
                "Admin wallet has insufficient reserve. " .
                "Balance: {$balanceXrp} XRP, " .
                "Required: {$requiredTotal} XRP"
            );
        }
    }

    // protected function getAccountInfo(WsClient $ws, string $address): array
    // {
    //     $info = $ws->request([
    //         'command' => 'account_info',
    //         'account' => $address,
    //         'ledger_index' => 'validated',
    //     ]);

    //     if (!isset($info['result']['account_data']['Sequence'])) {
    //         throw new \Exception('Account info missing Sequence');
    //     }

    //     $ledgerIndex =
    //         $info['result']['ledger_current_index']
    //         ?? $info['result']['ledger_index']
    //         ?? null;

    //     if (!$ledgerIndex) {
    //         throw new \Exception('Account info missing ledger index');
    //     }

    //     return [
    //         'sequence' => (int) $info['result']['account_data']['Sequence'],
    //         'ledger'   => (int) $ledgerIndex,
    //     ];
    // }

    protected function getAccountInfo(WsClient $ws, string $address): array
    {
        $info = $ws->request([
            'command' => 'account_info',
            'account' => $address,
            'ledger_index' => 'current',
        ]);

        if (!isset($info['result']['account_data']['Sequence'])) {
            throw new \Exception('Account info missing Sequence');
        }

        $sequence = $info['result']['account_sequence_next'] ?? $info['result']['account_data']['Sequence'];

        return [
            'sequence' => (int) $sequence,
            'ledger'   => (int) ($info['result']['ledger_current_index'] ?? $info['result']['ledger_index'] ?? 0),
        ];
    }



    protected function getFee(WsClient $ws): int
    {
        try {
            $feeInfo = $ws->request(['command' => 'fee']);
            return (int) ($feeInfo['result']['drops']['open_ledger_fee'] ?? 20) + 10;
        } catch (\Exception $e) {
            return 50;
        }
    }

    protected function calculateURITokenID(string $rAddress, string $uriHex): string
    {
        $codec = new AddressCodec();
        $accountIdBuffer = $codec->decodeAccountID($rAddress);
        $accountIdHex = "";
        foreach ($accountIdBuffer as $byte) {
            $accountIdHex .= sprintf("%02X", $byte);
        }
        $payload = hex2bin($accountIdHex . $uriHex);
        $hash = hash('sha512', $payload, true);
        return strtoupper(bin2hex(substr($hash, 0, 32)));
    }



    public function burnNft(string $ownerAddress, string $tokenId): array
    {
        $ws = null;
        try {
            $wallet = Wallet::where('address', $ownerAddress)->firstOrFail();
            $ws = $this->connection->getClient();
            $accountInfo = $this->getAccountInfo($ws, $wallet->address);

            $nft = Nft::where('nft_id', $tokenId)->first();

            if (!$nft) {
                throw new \Exception('NFT not found');
            }

            // Construct Transaction based on Network
            if ($this->isXahau()) {
                $tx = [
                    'TransactionType' => 'URITokenBurn',
                    'URITokenID' => $tokenId,
                ];
                if (config('xrpl.network.testnet.id')) {
                    $tx['NetworkID'] = (int) config('xrpl.network.testnet.id');
                }
            } else {
                $tx = [
                    'TransactionType' => 'NFTokenBurn',
                    'NFTokenID' => $tokenId,
                ];
            }

            $tx += [
                'Account' => $wallet->address,
                'Sequence' => $accountInfo['sequence'],
                'Fee' => (string) $this->getFee($ws),
                'LastLedgerSequence' => $accountInfo['ledger'] + 20,
            ];

            // Sign and Submit
            $signed = $this->signWithNode($tx, $wallet->seed);
            $submit = $ws->request([
                'command' => 'submit',
                'tx_blob' => $signed['tx_blob']
            ]);

            $engineResult = $submit['result']['engine_result'] ?? null;

            if ($engineResult !== 'tesSUCCESS') {
                throw new \RuntimeException(
                    $submit['result']['engine_result_message'] ?? 'Burn failed'
                );
            }

            $nft->status = 'burned';
            $nft->response = ['burn_tx' => $submit['result']['tx_json']['hash']];
            $nft->save();

            return [
                'success' => true,
                'tx_hash' => $submit['result']['tx_json']['hash'],
                'status' => $engineResult
            ];

        } catch (\Throwable $e) {
            Log::error('Burn NFT Failed', ['error' => $e->getMessage(), 'token_id' => $tokenId]);
            throw $e;
        } finally {
            if ($ws) $ws->close();
        }
    }

}
