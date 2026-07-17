<?php

return [

    'network' => [
        'default' => env('XRPL_NETWORK_TYPE', 'mainnet'),

        'mainnet' => [
            'url' => env('XRPL_NETWORK', 'wss://xrplcluster.com'),
            'id'  => env('XRPL_NETWORK_ID', 0),
            'explorer' => 'https://bithomp.com',
        ],

        'testnet' => [
            'url' => env('XRPL_NETWORK', 'wss://s.altnet.rippletest.net:51233'),
            'id'  => env('XRPL_NETWORK_ID', 1),
            'explorer' => 'https://test.bithomp.com',
        ],
    ],
    'network_name'  => env('XRPL_NETWORK_NAME', 'xrpl'),
    'source_tag'    => env('XRPL_SOURCE_TAG', '2606210001'),
    // 'faucet_url'    => 'https://xahau-test.net/accounts',
    'faucet_url'    => env('XRPL_FAUCET_URL', 'https://faucet.altnet.rippletest.net/accounts'),
    // 'network_id'    => 21338,
    'network_id'    => env('XRPL_NETWORK_ID', 1),
    // 'native_currency'  => 'XAH',
    'native_currency'  => env('XRPL_NATIVE_CURRENCY', 'XRP'),
    'currency'      => env('XRPL_CURRENCY', 'WASH'),
    'node'          => 'node',
    'fee'           => env('XRPL_FEE', '12'),

    'hot_wallet' => [
        'address' => env('XRPL_HOT_WALLET_ADDRESS'),
        'seed'    => env('XRPL_HOT_WALLET_SEED'),
    ],

    'cold_wallet' => [
        'address' => env('XRPL_COLD_WALLET_ADDRESS'),
        'seed'    => env('XRPL_COLD_WALLET_SEED'),
    ],

    'escrow_wallet' => [
        'address' => env('XRPL_ESCROW_WALLET_ADDRESS'),
        'seed'    => env('XRPL_ESCROW_WALLET_SEED'),
    ],


    'issuer_settings' => [
        'transfer_fee_percent' => env('XRPL_TRANSFER_FEE', 2.5),
        'domain' => env('XRPL_DOMAIN', 'pwoa.org'),
        'tick_size' => env('XRPL_TICK_SIZE', 5),
    ],
    'check_expiration' => env('XRPL_CHECK_EXPIRATION', 5),

    'activation_amount' => env('XRPL_ACTIVATION_AMOUNT', 4),
    'min_reserve_threshold' => env('XRPL_MIN_RESERVE_THRESHOLD', 2.2),
    'refill_amount' => env('XRPL_REFILL_AMOUNT', 2),

    'wallet_key' => env('XRPL_WALLET_KEY'),
];
