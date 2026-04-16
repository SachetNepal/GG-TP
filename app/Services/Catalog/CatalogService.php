<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CatalogService
{
    public function categories()
    {
        return Category::query()->orderBy('category_name')->get();
    }

    public function products(array $filters)
    {
        $query = Product::query()
            ->with(['shop', 'category'])
            ->withAvg('reviews', 'rating');

        $this->applyFilters($query, $filters);

        return $query->orderBy('product_name')->paginate(20);
    }

    public function productDetail(int $productId): Product
    {
        return Product::query()
            ->with(['shop.trader.user', 'category', 'reviews.customer.user', 'discounts'])
            ->findOrFail($productId);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $query
            ->when($filters['q'] ?? null, fn (Builder $q, $v) => $q->whereRaw('LOWER(product_name) LIKE ?', ['%'.strtolower($v).'%']))
            ->when($filters['category_id'] ?? null, fn (Builder $q, $v) => $q->where('category_id', $v))
            ->when($filters['shop_id'] ?? null, fn (Builder $q, $v) => $q->where('shop_id', $v))
            ->when($filters['min_price'] ?? null, fn (Builder $q, $v) => $q->where('price', '>=', $v))
            ->when($filters['max_price'] ?? null, fn (Builder $q, $v) => $q->where('price', '<=', $v))
            ->when($filters['min_rating'] ?? null, function (Builder $q, $v): void {
                $q->whereExists(function ($sub) use ($v) {
                    $sub->select(DB::raw(1))
                        ->from('REVIEW')
                        ->whereColumn('REVIEW.product_id', 'PRODUCT.product_id')
                        ->groupBy('REVIEW.product_id')
                        ->havingRaw('AVG(REVIEW.rating) >= ?', [$v]);
                });
            });
    }
}

