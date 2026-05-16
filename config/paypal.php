<?php

return [

    'mode' => env('PAYPAL_MODE', 'sandbox'),

    'client_id' => env('PAYPAL_CLIENT_ID', ''),

    'client_secret' => env('PAYPAL_CLIENT_SECRET', env('PAYPAL_SECRET', '')),

    'currency' => env('PAYPAL_CURRENCY', 'USD'),

    'base_url' => env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'),

    /** Sandbox: fixed test charge (e.g. 10.00) instead of cart total. */
    'sandbox_test_amount' => env('PAYPAL_SANDBOX_TEST_AMOUNT', '10.00'),

];
