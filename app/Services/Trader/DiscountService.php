<?php

namespace App\Services\Trader;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    public function assign(User $user, array $data): array
    {
        $trader = $user->trader;
        abort_if(!$trader, 403, 'Trader profile required');

        $product = Product::query()->with('shop')->findOrFail($data['product_id']);
        abort_if((int) $product->shop?->trader_id !== (int) $trader->trader_id, 403, 'Cannot apply discount to this product');

        return DB::connection('oracle')->transaction(function () use ($data, $product) {
            $discount = Discount::create([
                'rate' => $data['rate'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
            ]);

            $link = ProductDiscount::create([
                'product_id' => $product->product_id,
                'discount_id' => $discount->discount_id,
            ]);

            return compact('discount', 'link');
        });
    }
}

