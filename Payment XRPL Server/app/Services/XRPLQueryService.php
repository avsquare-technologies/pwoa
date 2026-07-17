<?php

namespace App\Services;

use App\Models\Wallet;
use App\Services\WsClient;

class XRPLQueryService
{
    protected $connection;

    public function __construct(XrplConnection $connection)
    {
        $this->connection = $connection;
    }

    public function accountInfo(Wallet $wallet): array
    {
        // $ws = new WsClient(config('xrpl.network.xahau'), true);
        $ws = $this->connection->getClient();

        $res = $ws->request([
            'command' => 'account_info',
            'account' => $wallet->address,
            'ledger_index' => 'validated',
        ]);

        $ws->close();

        $balanceDrops = $res['result']['account_data']['Balance'] ?? 0;

        return [
            'balance_xrp' => $balanceDrops / 1_000_000,
        ];
    }

    public function transactions(Wallet $wallet, ?string $start, ?string $end): array
    {
        // you can reuse your AccountTx logic here cleanly
        return [];
    }
}
