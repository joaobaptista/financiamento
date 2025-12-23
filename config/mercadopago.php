<?php

return [
    'base_url' => env('MERCADOPAGO_BASE_URL', 'https://api.mercadopago.com'),

    // Never expose this token in frontend code.
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),

    // Optional: used to validate webhook signatures (if you enable it).
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),

    // Used when creating payments.
    'currency' => env('MERCADOPAGO_CURRENCY', 'BRL'),

    // Webhook URL Mercado Pago will call (configure in MP panel).
    'webhook_url' => env('MERCADOPAGO_WEBHOOK_URL', rtrim((string) env('APP_URL', ''), '/') . '/api/webhooks/mercadopago'),
];
