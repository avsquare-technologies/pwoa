<?php

namespace App\Helpers;

use App\Services\WsClient;
use Exception;
use Hardcastle\Buffer\Buffer;
use Hardcastle\XRPL_PHP\Client\JsonRpcClient;
use Hardcastle\XRPL_PHP\Core\RippleAddressCodec\AddressCodec;
use Illuminate\Support\Facades\Log;

class XahauHelper
{
    protected JsonRpcClient|WsClient $client;

    protected string $network;

    public function __construct()
    {
        try {
            $this->network = env('XRPL_NETWORK', 'ws://143.244.131.108:16016');
            // $this->network = $networks[$networkKeyOrUrl] ?? $networkKeyOrUrl;

            // Choose client type automatically
            if (str_starts_with($this->network, 'http')) {
                // JSON-RPC
                $this->client = new JsonRpcClient($this->network);
                Log::info("🧠 Using JsonRpcClient for {$this->network}");
            } elseif (str_starts_with($this->network, 'ws')) {
                // WebSocket
                $this->client = new WsClient($this->network, true);
                Log::info("🧩 Using WsClient for {$this->network}");
            }
        } catch (Exception $e) {
            Log::error('XahauHelper constructor failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getNetwork()
    {
        return $this->network;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function dropsToXrp($drops): float
    {
        return (float) $drops / 1_000_000;
    }

    public function xrpToDrops($xrp)
    {
        return (string) intval(round($xrp * 1_000_000));
    }

    public function decimalTo32bitHexFlip(int $decimal): string
    {
        $hex = str_pad(dechex($decimal), 8, '0', STR_PAD_LEFT);
        $bytes = str_split($hex, 2);
        $littleEndian = implode('', array_reverse($bytes));

        return $littleEndian;
    }

    public function rAddressToAccountID(string $classicAddress): ?string
    {
        try {
            $codec = new AddressCodec;
            $buf = $codec->decodeAccountId($classicAddress);

            if (method_exists($buf, 'toHex')) {
                $hex = $buf->toHex();
            } else {
                $hex = $buf->toString();
            }

            return strtoupper($hex);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;

            return null;
        }
    }

    public function accountIDToRAddress(string $accountIdHex): ?string
    {
        try {
            $codec = new AddressCodec;

            // Convert hex to Buffer
            $buf = Buffer::from($accountIdHex, 'hex');

            // Encode into rAddress (classic address)
            $rAddress = $codec->encodeAccountID($buf);

            return $rAddress;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage() . PHP_EOL;

            return null;
        }
    }

    public function stringToHex(string $text): string
    {
        return strtoupper(bin2hex($text));
    }

    public function hexToString(string $hex): string
    {
        $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);

        if (strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }

        return hex2bin($hex);
    }

    public function buildMemos(array $data): array
    {
        $memos = [];

        foreach ($data as $type => $value) {

            $typeHex   = $this->stringToHex((string) $type);
            $dataHex   = $this->stringToHex((string) $value);
            $formatHex = $this->stringToHex('text/plain');

            // XRPL MemoData limit ≈ 1024 bytes (hex = 2048 chars)
            if (strlen($dataHex) > 2048) {
                throw new \RuntimeException("MemoData exceeds XRPL 1KB limit");
            }

            $memos[] = [
                'Memo' => [
                    'MemoType'   => $typeHex,
                    'MemoData'   => $dataHex,
                    'MemoFormat' => $formatHex,
                ]
            ];
        }

        return $memos;
    }
}
