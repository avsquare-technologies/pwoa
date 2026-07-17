<?php

namespace App\Services;

use App\Services\WsClient;

class XrplConnection
{
    protected $config;
    protected $network;
    protected $network_name;

    public function __construct()
    {
        $this->network = config('xrpl.network.default', 'testnet');
        $this->config = config("xrpl.network.{$this->network}");
        $this->network_name = config('xrpl.network_name', 'xrpl');
        // dump($this->config, $this->network);
    }

    public function getClient(): WsClient
    {
        return new WsClient($this->config['url'], true);
    }

    public function getNetworkId(): int
    {
        return $this->config['id'];
    }

    public function getFee(): string
    {
        return config('xrpl.fee', '12');
    }
    public function getNetworkName(): string
    {
        return $this->network_name;
    }

    public function getSourceTag(): ?int
    {
        return (int) config('xrpl.source_tag', 2606210001);
    }
}
