<?php

return [
    'plans' => [
        'standard' => [
            'name' => 'Standard Membership',
            'stripe_price_id' => env('PWOA_MEMBERSHIP_PRICE_ID'),
            'price' => 99.00,
            'currency' => 'USD',
            'interval' => 'year',
        ],
        'gold' => [
            'name' => 'Gold Membership',
            'stripe_price_id' => env('PWOA_GOLD_MEMBERSHIP_PRICE_ID'),
            'price' => 300.00,
            'currency' => 'USD',
            'interval' => 'year',
        ],
    ],

    // Auto-renew defaults
    'auto_renew' => true,
];
