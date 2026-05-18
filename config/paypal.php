<?php

return [

    'mode' => env('PAYPAL_MODE', 'sandbox'),

    'client_id' => env('PAYPAL_CLIENT_ID', ''),

    'client_secret' => env('PAYPAL_CLIENT_SECRET', env('PAYPAL_SECRET', '')),

    'currency' => env('PAYPAL_CURRENCY', 'USD'),

    'base_url' => env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'),

];
