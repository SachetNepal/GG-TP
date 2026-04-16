<?php

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public function mockPay(Order $order, string $method): Payment
    {
        return Payment::create([
            'paid_amount' => $order->amount,
            'payment_method' => $method,
            'payment_status' => 'paid',
            'order_id' => $order->order_id,
        ]);
    }
}

