<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PinataService
{
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->apiKey = config('services.pinata.api_key');
        $this->apiSecret = config('services.pinata.api_secret');

        if (empty($this->apiKey) || empty($this->apiSecret)) {
            throw new RuntimeException('Pinata API keys are not configured in services.php or .env');
        }
    }

    /**
     * Upload a file to Pinata IPFS
     * Returns the IPFS URI (e.g., ipfs://QmHash...)
     */
    public function uploadFile(mixed $file): string
    {
        $fileContent = null;
        $fileName = 'file';

        if ($file instanceof UploadedFile) {
            $fileContent = file_get_contents($file->getRealPath());
            $fileName = $file->getClientOriginalName();
        } else if (is_string($file) && file_exists($file)) {
            $fileContent = file_get_contents($file);
            $fileName = basename($file);
        } else {
            throw new RuntimeException('Invalid file provided for Pinata upload');
        }

        $response = Http::withHeaders([
            'pinata_api_key' => $this->apiKey,
            'pinata_secret_api_key' => $this->apiSecret,
        ])->attach(
                'file',
                $fileContent,
                $fileName
            )->post('https://api.pinata.cloud/pinning/pinFileToIPFS');

        if (!$response->successful()) {
            throw new RuntimeException('Pinata File Upload Failed: ' . $response->body());
        }

        $hash = $response->json()['IpfsHash'];

        return "ipfs://{$hash}";
    }

    /**
     * Upload JSON Metadata to Pinata IPFS
     * Used for NFT Metadata (Standard Token JSON)
     */
    public function uploadJson(array $jsonData): string
    {
        $response = Http::withHeaders([
            'pinata_api_key' => $this->apiKey,
            'pinata_secret_api_key' => $this->apiSecret,
            'Content-Type' => 'application/json',
        ])->post('https://api.pinata.cloud/pinning/pinJSONToIPFS', [
                    'pinataContent' => $jsonData,
                    'pinataMetadata' => [
                        'name' => $jsonData['name'] ?? 'NFT Metadata',
                    ]
                ]);

        if (!$response->successful()) {
            throw new RuntimeException('Pinata JSON Upload Failed: ' . $response->body());
        }

        $hash = $response->json()['IpfsHash'];

        return "ipfs://{$hash}";
    }
}
