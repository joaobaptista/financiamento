<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payments Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "mock", "mercadopago"
    |
    */

    'driver' => env('PAYMENTS_DRIVER', 'mock'),

    'checkout_incomplete' => [
        'delay_minutes' => (int) env('CHECKOUT_INCOMPLETE_DELAY_MINUTES', 60),
    ],
];
