<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Payment;
use App\Support\OracleId;

class PaymentService
{
    public function mockPay(Order $order, string $method): Payment
    {
        return $this->createPayment($order, $method, 'paid', (float) $order->amount);
    }

    public function recordPayPal(Order $order, string $paypalOrderId, string $captureId, float $paidAmount): Payment
    {
        $method = config('paypal.mode') === 'live' ? 'paypal_live' : 'paypal_sandbox';

        return $this->createPayment($order, $method, 'paid', $paidAmount);
    }

    protected function createPayment(Order $order, string $method, string $status, float $amount): Payment
    {
        return Payment::create([
            'payment_id' => OracleId::next('PAYMENT', 'payment_id', 'PY'),
            'paid_amount' => $amount,
            'payment_method' => substr($method, 0, 20),
            'payment_status' => substr($status, 0, 20),
            'order_id' => $order->order_id,
        ]);
    }
}
