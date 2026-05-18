<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function findForCustomer(User $user, string $orderId): Order
    {
        return Order::query()
            ->with(['items.product.shop', 'payment', 'collectionSlot'])
            ->where('order_id', $orderId)
            ->where('customer_id', $user->user_id)
            ->firstOrFail();
    }

    public function cancelForCustomer(User $user, string $orderId): Order
    {
        $order = Order::query()
            ->where('order_id', $orderId)
            ->where('customer_id', $user->user_id)
            ->firstOrFail();

        if ($order->status === 'completed') {
            abort(422, 'Completed orders cannot be cancelled.');
        }

        if ($order->status === 'cancelled') {
            return $order;
        }

        $order->status = 'cancelled';
        $order->save();

        return $order->fresh(['items.product', 'payment', 'collectionSlot']);
    }
}
