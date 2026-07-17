<?php

namespace App\Client;

use WebSocket\Client;
use Illuminate\Support\Facades\Log;

class WsClient
{
    private Client $client;
    private bool $debug;

    /**
     * Create a new WebSocket client instance.
     *
     * @param string $url
     * @param bool $debug
     */
    public function __construct(string $url, bool $debug = false)
    {
        $this->debug = $debug;

        $this->client = new Client($url, [
            'timeout' => 10,
        ]);

        if ($this->debug) {
            // Log::info("🔗 WebSocket connected to: {$url}");
        }
    }

    /**
     * Send a JSON-RPC payload and wait for a response.
     *
     * @param array $payload
     * @return array|null
     */
    public function request(array $payload): ?array
    {
        try {
            if ($this->debug) {
                // Log::info("→ WS Send", $payload);
            }

            $this->client->send(json_encode($payload));
            $response = $this->client->receive();

            if ($this->debug) {
                // Log::info("← WS Response: {$response}");
            }

            $decoded = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Invalid JSON response: ' . json_last_error_msg());
            }

            return $decoded;
        } catch (\Throwable $e) {
            Log::error("⚠️ WebSocket Error: " . $e->getMessage(), [
                'payload' => $payload,
            ]);

            return [
                'error' => 'client_exception',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Close the WebSocket connection.
     */
    public function close(): void
    {
        try {
            $this->client->close();
            if ($this->debug) {
                // Log::info("🔌 WebSocket connection closed.");
            }
        } catch (\Throwable $e) {
            Log::error("⚠️ Error closing WebSocket: " . $e->getMessage());
        }
    }
}
