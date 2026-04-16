<?php

namespace App\Services\Checkout;

use App\Models\CollectionSlot;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Basket\BasketService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected BasketService $basketService,
        protected SlotCapacityService $slotCapacityService,
        protected PaymentService $paymentService,
    ) {
    }

    public function checkout(User $user, array $payload): array
    {
        $basket = $this->basketService->getBasket($user);
        $summary = $this->basketService->summary($basket);

        if (empty($summary['items'])) {
            abort(422, 'Basket is empty');
        }

        $this->slotCapacityService->assertSlotAvailable($payload['slot_date'], $payload['slot_time']);

        return DB::connection('oracle')->transaction(function () use ($user, $basket, $summary, $payload) {
            $order = Order::create([
                'order_date' => now(),
                'status' => 'placed',
                'amount' => $summary['total'],
                'user_id' => $user->user_id,
            ]);

            foreach ($summary['items'] as $line) {
                OrderItem::create([
                    'quantity' => $line['quantity'],
                    'price' => $line['unit_price'],
                    'order_id' => $order->order_id,
                    'product_id' => $line['product_id'],
                ]);
            }

            $slot = CollectionSlot::create([
                'date' => $payload['slot_date'],
                'time' => $payload['slot_time'],
                'order_id' => $order->order_id,
            ]);

            $payment = $this->paymentService->mockPay($order, $payload['payment_method']);

            // Clear basket rows after successful checkout.
            $basket->items()->delete();

            return [
                'order' => $order->fresh(['items.product', 'collectionSlot', 'payment']),
                'collection_slot' => $slot,
                'payment' => $payment,
            ];
        });
    }
}

