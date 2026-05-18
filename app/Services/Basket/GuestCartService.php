<?php

namespace App\Services\Basket;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class GuestCartService
{
    private const SESSION_KEY = 'guest_cart';

    /**
     * @return array<string, int> product_id => quantity
     */
    public function items(): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        return is_array($cart) ? array_filter($cart, fn ($q) => (int) $q > 0) : [];
    }

    public function addItem(string $productId, int $quantity = 1): void
    {
        $cart = $this->items();
        $current = (int) ($cart[$productId] ?? 0);
        $this->setQuantity($productId, min(20, $current + max(1, $quantity)));
    }

    public function setQuantity(string $productId, int $quantity): void
    {
        $cart = $this->items();

        if ($quantity <= 0) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);

            return;
        }

        Product::query()->findOrFail($productId);
        $cart[$productId] = min(20, $quantity);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function summary(): array
    {
        $lines = [];
        $total = 0.0;

        foreach ($this->items() as $productId => $qty) {
            $product = Product::query()->find($productId);
            if (! $product) {
                continue;
            }
            $qty = (int) $qty;
            $unit = (float) $product->price;
            $lineTotal = $unit * $qty;
            $total += $lineTotal;

            $lines[] = [
                'basket_item_id' => null,
                'product_id' => $productId,
                'product_name' => $product->product_name,
                'unit_price' => $unit,
                'quantity' => $qty,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'basket_id' => null,
            'items' => $lines,
            'total' => $total,
        ];
    }

    public function isEmpty(): bool
    {
        return $this->items() === [];
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function mergeIntoUserBasket(User $user, BasketService $basketService): void
    {
        $items = $this->items();
        if ($items === []) {
            return;
        }

        foreach ($items as $productId => $qty) {
            $qty = max(1, (int) $qty);
            for ($i = 0; $i < $qty; $i++) {
                try {
                    $basketService->addItem($user, (string) $productId);
                } catch (\Throwable) {
                    // Skip invalid products; keep merging the rest
                }
            }
        }

        $this->clear();
    }
}
