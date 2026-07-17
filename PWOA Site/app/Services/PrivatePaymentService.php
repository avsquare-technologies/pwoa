<?php

namespace App\Services;

use App\Models\TokenTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrivatePaymentService
{
    protected string $url;
    protected string $secret;

    public function __construct()
    {
        $this->url = config('payment_service.url');
        $this->secret = config('payment_service.secret');
    }

    public function createWallet(int $userId, string $email, string $name)
    {
        try {
            $payload = [
                'user_id' => $userId,
                'email' => $email,
                'name' => $name,
            ];

            Log::info("🚀 Private API Wallet Create Request for User: {$userId}", $payload);

            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/wallet/create", $payload);

            Log::debug("📩 Private API Wallet Create Response Raw", [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('❌ Private API Wallet Creation Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('🔥 Private API Wallet Creation Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Issue tokens and track the transaction locally.
     * 
     * @param int $userId
     * @param string $address
     * @param float $amount
     * @param string $currency
     * @return TokenTransaction
     */
    public function issueTokens(int $userId, string $address, float $amount, string $currency = 'WASH'): TokenTransaction
    {
        // 1. Create a local pending record
        $transaction = TokenTransaction::create([
            'user_id' => $userId,
            'amount' => $amount,
            'currency' => $currency,
            'destination_address' => $address,
            'status' => 'pending',
        ]);

        try {
            // 2. Call the Private Payment Service
            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/wallet/add-fund", [
                'destination' => $address,
                'amount' => $amount,
                'currency' => $currency,
            ]);

            $body = $response->json();

            // 3. Handle Communication Success
            if ($response->successful() && isset($body['result'])) {
                $engineResult = $body['result']['engine_result'] 
                    ?? $body['result']['meta']['TransactionResult'] 
                    ?? $body['result']['metadata']['TransactionResult'] 
                    ?? 'failed';

                $hash = $body['result']['tx_json']['hash'] 
                    ?? $body['result']['hash'] 
                    ?? null;

                if ($engineResult === 'tesSUCCESS') {
                    // Success!
                    $transaction->update([
                        'status' => 'success',
                        'tx_hash' => $hash,
                        'metadata' => $body,
                    ]);
                    
                    Log::info("Token Issuance Success for User {$userId}: {$hash}");
                } else {
                    // Business Logic Failure (XRPL Error)
                    $errorMessage = $body['result']['engine_result_message'] ?? $engineResult;
                    $transaction->update([
                        'status' => 'failed',
                        'error_message' => "XRPL Error: {$errorMessage}",
                        'metadata' => $body,
                    ]);

                    Log::error("Token Issuance XRPL Failure for User {$userId}: {$errorMessage}");
                }
            } else {
                // API Communication Failure
                $errorMessage = $body['error'] ?? $body['message'] ?? 'Unknown API Error';
                $transaction->update([
                    'status' => 'failed',
                    'error_message' => "API Error: {$errorMessage}",
                    'metadata' => $body,
                ]);

                Log::error("Token Issuance API Failure for User {$userId}: {$errorMessage}");
            }
        } catch (\Exception $e) {
            // Unexpected Exception (Timeout, Network Down, etc.)
            $transaction->update([
                'status' => 'failed',
                'error_message' => "Exception: " . $e->getMessage(),
            ]);

            Log::error("Token Issuance Exception for User {$userId}: " . $e->getMessage());
        }

        return $transaction;
    }

    public function issueWashTokens(string $address, float $amount)
    {
        // Legacy method - just calls the new one with simplified logic
        // This is kept for backward compatibility if needed elsewhere
        try {
            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/wallet/add-fund", [
                'destination' => $address,
                'amount' => $amount,
                'currency' => 'WASH',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Private API $WASH Token Issuance Exception: ' . $e->getMessage());
            return null;
        }
    }




    public function getSystemWallet(string $type = 'cold')
    {
        try {
            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/wallet/system-wallet/{$type}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Private API System Wallet Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function sendExternal(string $source, string $destination, float $amount)
    {
        try {
            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/wallet/send-user-external", [
                'source' => $source,
                'destination' => $destination,
                'amount' => $amount,
                'currency' => 'WASH',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Private API External Transfer Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Private API External Transfer Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function mintBatchNft(string $creatorAddress, string $uri, int $count, int $taxon, float $price = 0)
    {
        try {
            $payload = [
                'creator_address' => $creatorAddress,
                'uri' => $uri,
                'count' => $count,
                'taxon' => $taxon,
                'price' => $price,
            ];

            Log::info('🚀 Private API Batch Mint Request', $payload);

            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->timeout(600)->acceptJson()->post("{$this->url}/nft/batch-mint", $payload);

            Log::debug('📩 Private API Batch Mint Response Raw', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('❌ Private API Batch Mint Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => $response->json()['error'] ?? 'Batch mint failed'];
        } catch (\Exception $e) {
            Log::error('🔥 Private API Batch Mint Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function buyNftTicket(string $buyerAddress, string $sellerAddress, string $tokenId, float $amount)
    {
        try {
            $payload = [
                // 'order_id' => (int) rand(100000000, 999999999),
                'collabathon_id' => (int) rand(1000, 9999), // Adding random collabathon_id for context
                'buyer_address' => $buyerAddress,
                'seller_address' => $sellerAddress,
                'token_id' => $tokenId,
                'amount' => $amount,
                // 'currency' => 'WASH',
            ];

            Log::info('🚀 Private API Buy NFT Request', $payload);

            $response = Http::withHeaders([
                'X-Private-Token' => $this->secret,
            ])->acceptJson()->post("{$this->url}/collabathon/buy-ticket", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('❌ Private API Buy NFT Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['success' => false, 'error' => $response->json()['error'] ?? 'NFT purchase failed'];
        } catch (\Exception $e) {
            Log::error('🔥 Private API Buy NFT Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
