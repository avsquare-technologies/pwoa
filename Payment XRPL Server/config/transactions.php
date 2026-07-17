<?php

return [

    'stream' => [
        'creator_pct' => env('TX_STREAM_CREATOR_PCT', 90),
        'treasury_pct' => env('TX_STREAM_TREASURY_PCT', 10),
    ],

    'download' => [
        'creator_pct' => env('TX_DOWNLOAD_CREATOR_PCT', 90),
        'treasury_pct' => env('TX_DOWNLOAD_TREASURY_PCT', 10),
    ],

    'mint' => [
        'creator_pct' => env('TX_MINT_CREATOR_PCT', 96.5),
        'treasury_pct' => env('TX_MINT_TREASURY_PCT', 2.5),
        'original_owner_pct' => env('TX_MINT_ORIGINAL_OWNER_PCT', 1),
    ],

    'escrow' => [
        'creator_pct' => env('TX_ESCROW_CREATOR_PCT', 98.5),
        'treasury_pct' => env('TX_ESCROW_TREASURY_PCT', 1.5),
    ],

    'skill' => [
        'tiers' => json_decode(env('SKILL_COMMISSION_TIERS', '[]'), true),
    ],

];
