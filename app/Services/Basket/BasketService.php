<?php

namespace App\Services\Basket;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BasketService
{
    public function getBasket(User $user): Basket
    {
        $customer = $this->resolveCustomer($user);

        return Basket::query()->firstOrCreate(
            ['customer_id' => $customer->customer_id],
            ['created_date' => now()]
        );
    }

    public function addItem(User $user, int $productId): Basket
    {
        $basket = $this->getBasket($user);
        Product::query()->findOrFail($productId);

        DB::connection('oracle')->transaction(function () use ($basket, $productId): void {
            BasketItem::create([
                'basket_id' => $basket->basket_id,
                'product_id' => $productId,
            ]);
        });

        return $this->hydrateBasket($basket->basket_id);
    }

    public function updateItemQuantity(User $user, int $basketItemId, int $quantity): Basket
    {
        $basket = $this->getBasket($user);
        $item = BasketItem::query()
            ->where('basket_item_id', $basketItemId)
            ->where('basket_id', $basket->basket_id)
            ->firstOrFail();

        DB::connection('oracle')->transaction(function () use ($basket, $item, $quantity): void {
            $sameProductCount = BasketItem::query()
                ->where('basket_id', $basket->basket_id)
                ->where('product_id', $item->product_id)
                ->count();

            if ($quantity > $sameProductCount) {
                for ($i = 0; $i < ($quantity - $sameProductCount); $i++) {
                    BasketItem::create(['basket_id' => $basket->basket_id, 'product_id' => $item->product_id]);
                }
            } elseif ($quantity < $sameProductCount) {
                $toDelete = BasketItem::query()
                    ->where('basket_id', $basket->basket_id)
                    ->where('product_id', $item->product_id)
                    ->orderByDesc('basket_item_id')
                    ->limit($sameProductCount - $quantity)
                    ->get();

                foreach ($toDelete as $row) {
                    $row->delete();
                }
            }
        });

        return $this->hydrateBasket($basket->basket_id);
    }

    public function removeItem(User $user, int $basketItemId): Basket
    {
        $basket = $this->getBasket($user);

        BasketItem::query()
            ->where('basket_item_id', $basketItemId)
            ->where('basket_id', $basket->basket_id)
            ->delete();

        return $this->hydrateBasket($basket->basket_id);
    }

    public function summary(Basket $basket): array
    {
        $basket->load('items.product');
        $grouped = $basket->items->groupBy('product_id');

        $lines = [];
        $total = 0.0;
        foreach ($grouped as $productId => $items) {
            $product = $items->first()->product;
            $qty = $items->count();
            $lineTotal = ((float) $product->price) * $qty;
            $total += $lineTotal;

            $lines[] = [
                'product_id' => $productId,
                'product_name' => $product->product_name,
                'unit_price' => (float) $product->price,
                'quantity' => $qty,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'basket_id' => $basket->basket_id,
            'items' => $lines,
            'total' => $total,
        ];
    }

    protected function resolveCustomer(User $user): Customer
    {
        $customer = $user->customer;
        abort_if(!$customer, 403, 'Customer profile required');
        return $customer;
    }

    protected function hydrateBasket(int $basketId): Basket
    {
        return Basket::query()->with('items.product')->findOrFail($basketId);
    }
}

