<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$paypal = app(App\Services\Checkout\PayPalService::class);

echo "mode: ".(config('paypal.mode') ?: '?')."\n";
echo "currency: ".config('paypal.currency')."\n";
echo "base_url: ".config('paypal.base_url')."\n";
echo "client_id prefix: ".substr($paypal->clientId(), 0, 8)."...\n";
echo "sandbox: ".($paypal->isSandbox() ? 'yes' : 'no')."\n";

try {
    $order = $paypal->createCheckoutOrder(10.0);
    echo "order id: ".$order['id']."\n";
    echo "status: ".$order['status']."\n";
    echo "api amount: ".$order['currency'].' '.$order['amount']."\n";
    $links = $order['raw']['links'] ?? [];
    foreach ($links as $link) {
        echo "link ".$link['rel'].': '.$link['href']."\n";
    }
} catch (Throwable $e) {
    echo "ERROR: ".$e->getMessage()."\n";
    exit(1);
}
