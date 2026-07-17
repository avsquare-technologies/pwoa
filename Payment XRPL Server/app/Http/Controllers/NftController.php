<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\NftService;
use App\Services\WsClient;
use App\Services\XrplConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NftController extends Controller
{
    protected NftService $nftService;
    protected $connection;


    public function __construct(NftService $nftService, XrplConnection $connection)
    {
        $this->nftService = $nftService;
        $this->connection = $connection;
    }


    public function mint(Request $request)
    {
        try {
            Log::info('NFT Mint API Request', $request->all());
            // 1. Validate Input
            $validated = $request->validate([
                'creator_address' => 'required|string|starts_with:r',
                'uri' => 'required|string|max:256',
                'taxon' => 'nullable|integer|min:0',
                'transfer_fee' => 'nullable|integer|min:0|max:50000',
                'is_transferable' => 'nullable|boolean',
                'is_burnable' => 'nullable|boolean',
                'amount' => 'nullable',
            ]);

            // 2. Call Service
            $result = $this->nftService->mintNft(
                creatorAddress: $validated['creator_address'],
                uri: $validated['uri'],
                taxon: $validated['taxon'] ?? 0,
                transferFee: $validated['transfer_fee'] ?? 0,
                isTransferable: $validated['is_transferable'] ?? true,
                isBurnable: $validated['is_burnable'] ?? true,
                sellAmount: $validated['amount'] ?? null
            );
            Log::info('NFT Mint API Response', $result);

            // 3. Return Success
            return response()->json([
                'success' => true,
                'message' => 'NFT Mint transaction submitted successfully',
                'data' => $result,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('NFT Mint API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mint NFT',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function batchMint(Request $request)
    {
        $validated = $request->validate([
            'creator_address' => 'required|string',
            'uri'             => 'required|string',
            'count'           => 'required|integer|min:1|max:5000',
            'taxon'           => 'required|integer',
            'price'           => 'nullable|numeric'
        ]);

        Log::info('NFT Batch Mint API Request Received', $validated);

        $creator = $validated['creator_address'];
        $uri     = $validated['uri'];
        $count   = $validated['count'];
        $taxon   = $validated['taxon'];
        $price   = $validated['price'] ?? 0;

        $chunkSize = 200;

        $totalProcessed = 0;
        $totalFailed    = 0;
        $allTickets     = [];
        $batchIds       = [];

        try {

            set_time_limit(0);

            while ($count > 0) {

                $currentChunk = min($chunkSize, $count);

                Log::info("Minting Chunk: {$currentChunk} NFTs");

                $result = $this->nftService->mintBatchByCount(
                    $creator,
                    $uri,
                    $currentChunk,
                    $taxon,
                    $price
                );

                $batchIds[] = $result['batch_id'];

                $totalProcessed += $result['processed'];
                $totalFailed    += $result['failed'];

                if (!empty($result['tickets'])) {
                    $allTickets = array_merge($allTickets, $result['tickets']);
                }

                $count -= $currentChunk;

                sleep(2); // XRPL ledger breathing room
            }

            return response()->json([
                'success'   => ($totalFailed === 0),
                'batch_ids' => $batchIds,
                'processed' => $totalProcessed,
                'failed'    => $totalFailed,
                'tickets'   => $allTickets
            ]);

        } catch (\Throwable $e) {

            Log::error("Batch Mint Controller Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function getBatchStatus($creatorAddress)
    {
        $latestBatch = DB::table('collabathon_nfts')
            ->where('creator_address', $creatorAddress)
            ->orderBy('created_at', 'desc')
            ->select('batch_id')
            ->first();

        if (!$latestBatch) {
            return response()->json([
                'success' => false,
                'message' => 'No minting history found for this address.'
            ], 404);
        }

        $batchId = $latestBatch->batch_id;

        $response = Cache::get("batch_progress_{$batchId}");

        if (isset($response)) {
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Batch not found'
        ]);
    }

    public function buy(Request $request)
    {
        try {
            Log::info('NFT Buy API Request', $request->all());

            $validated = $request->validate([
                'order_id' => 'required|integer',
                'buyer_address' => 'required|string|starts_with:r',
                'token_id' => 'required|string|size:64',
                'amount' => 'required',
            ]);

            $result = $this->nftService->buyNft(
                orderId: $validated['order_id'],
                buyerAddress: $validated['buyer_address'],
                tokenId: $validated['token_id'],
                amount: $validated['amount']
            );

            Log::info('NFT Buy API Result', $request->all());

            return response()->json([
                'success' => true,
                'message' => 'NFT Buy transaction submitted successfully',
                'data' => $result,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('NFT Buy API Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to buy NFT', 'error' => $e->getMessage()], 500);
        }
    }


    public function burn(Request $request)
    {
        try {
            Log::info('NFT Burn API Request', $request->all());

            $validated = $request->validate([
                'owner_address' => 'required|string|starts_with:r',
                'token_id'      => 'required|string',
            ]);

            $result = $this->nftService->burnNft(
                ownerAddress: $validated['owner_address'],
                tokenId: $validated['token_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'NFT Burn transaction submitted successfully',
                'data' => $result,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation Error', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('NFT Burn API Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to burn NFT', 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function syncIds(Request $request)
    {
        $validated = $request->validate([
            'tx_hashes' => 'required|array',
            'tx_hashes.*' => 'required|string|size:64',
        ]);

        $hashes = $validated['tx_hashes'];
        $results = [];

        // $ws = new WsClient(config('xrpl.network.xahau'), false);
        $ws = $this->connection->getClient();

        try {
            Log::info("Sync: Checking " . count($hashes) . " transactions for IDs.");

            foreach ($hashes as $hash) {
                $tokenId = $this->nftService->getCreatedTokenId($ws, $hash, 1);

                if ($tokenId) {
                    $results[$hash] = $tokenId;

                    DB::table('collabathon_nfts')
                        ->where('tx_hash', $hash)
                        ->update([
                            'nft_token_id' => $tokenId,
                            'status' => 'confirmed',
                            'updated_at' => now()
                        ]);
                }
            }

        } catch (\Throwable $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        } finally {
            $ws->close();
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    public function createSellOffer(Request $request)
    {
        // 1. Validate Request from Public App
        $validated = $request->validate([
            'wallet_address' => 'required|string|starts_with:r',
            'token_id'       => 'required|string|size:64',
            'amount'         => 'required', // Can be string or number
        ]);

        try {
            Log::info("Private API: Creating Sell Offer", $validated);

            // 2. Find Wallet (to get the Seed)
            $wallet = Wallet::where('address', $validated['wallet_address'])->firstOrFail();

            // 3. Call your Service
            $result = $this->nftService->createSellOffer(
                $wallet,
                $validated['token_id'],
                (string)$validated['amount']
            );

            // 4. Return Success
            return response()->json([
                'success' => true,
                'message' => 'Sell Offer submitted to ledger.',
                'data'    => $result
            ]);

        } catch (\Throwable $e) {
            Log::error("Private API Sell Offer Failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
