<?php

namespace App\Services\Trader;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TraderProductService
{
    public function create(User $traderUser, array $data): Product
    {
        // Ensure shop ownership in service layer.
        $trader = $traderUser->trader;
        abort_if(!$trader, 403, 'Trader profile not found');

        return DB::connection('oracle')->transaction(function () use ($trader, $data) {
            $shopIds = $trader->shops()->pluck('shop_id')->all();
            abort_if(!in_array((string) $data['shop_id'], array_map('strval', $shopIds), true), 403, 'Cannot create product for this shop');

            return Product::create($data);
        });
    }

    public function update(User $traderUser, Product $product, array $data): Product
    {
        $trader = $traderUser->trader;
        abort_if(!$trader, 403, 'Trader profile not found');
        abort_if((string) $product->shop?->trader_id !== (string) $trader->trader_id, 403, 'Cannot update this product');

        DB::connection('oracle')->transaction(function () use ($product, $data): void {
            $product->update($data);
        });

        return $product->fresh(['shop', 'category']);
    }

    public function setActive(User $traderUser, Product $product, bool $isActive): Product
    {
        $trader = $traderUser->trader;
        abort_if(!$trader, 403, 'Trader profile not found');
        abort_if((string) $product->shop?->trader_id !== (string) $trader->trader_id, 403, 'Cannot update this product');

        // Oracle schema does not define active flag; reuse stock semantics.
        $product->product_in_stock = $isActive ? max(1, (int) $product->product_in_stock) : 0;
        $product->save();

        return $product->fresh();
    }
}

