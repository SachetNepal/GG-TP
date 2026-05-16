<?php

/**
 * Sandbox diagnostic: returns a PayPal order ID as JSON when logged in.
 * Open while logged in: http://localhost/GG-TP/public/create-paypal-order.php
 * (or your APP_URL + /public/create-paypal-order.php)
 */
declare(strict_types=1);

use App\Services\Checkout\PayPalService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

$request = Request::capture();
$kernel->handle($request);

header('Content-Type: application/json');

if ($request->method() !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Use GET']);
    exit;
}

if (!auth()->check()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Log in on the site first, then open this URL again.']);
    exit;
}

try {
    /** @var PayPalService $paypal */
    $paypal = $app->make(PayPalService::class);
    if (!$paypal->isConfigured()) {
        throw new RuntimeException('PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET (or PAYPAL_SECRET) are required in .env');
    }
    $order = $paypal->createCheckoutOrder(0.0);
    echo json_encode([
        'ok' => true,
        'id' => $order['id'],
        'status' => $order['status'],
        'amount' => $order['amount'],
        'currency' => $order['currency'],
        'sandbox' => $paypal->isSandbox(),
    ], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(502);
    echo json_encode([
        'ok' => false,
        'message' => $e->getMessage(),
    ], JSON_THROW_ON_ERROR);
}
