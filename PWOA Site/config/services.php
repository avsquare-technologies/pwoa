<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'xrpl' => [
        'network' => env('XRPL_NETWORK', 'wss://s.altnet.rippletest.net:51233'),
        'currency' => env('XRPL_CURRENCY', 'WASH'),
        'admin_user_id' => env('XRPL_ADMIN_USER_ID', 1),
        'explorer_url' => env('XRPL_EXPLORER_URL', 'https://test.bithomp.com'),
        'wash_to_usd' => env('WASH_TO_USD',1),
    ],

    'xumm' => [
        'api_key' => env('XUMM_API_KEY'),
        'api_secret' => env('XUMM_API_SECRET'),
    ],

    'pinata' => [
        'api_key' => env('PINATA_API_KEY'),
        'api_secret' => env('PINATA_API_SECRET'),
    ],

    'admin_email' => "hello@example.com",
];
