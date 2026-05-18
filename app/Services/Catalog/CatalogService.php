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
        return Category::query()->orderBy('cat_name')->get();
    }

    public function products(array $filters)
    {
        $query = Product::query()
            ->with(['shop', 'category'])
            ->withAvg('reviews', 'rating');

        $this->applyCustomerVisibility($query);
        $this->applyFilters($query, $filters);
        $this->applySort($query, (string) ($filters['sort'] ?? 'name'));

        return $query->paginate(20);
    }

    public function productDetail(string $productId): Product
    {
        $query = Product::query()
            ->withAvg('reviews', 'rating')
            ->with([
                'shop.trader.user',
                'category',
                'discounts',
                'reviews' => fn ($q) => $q->orderByDesc('review_date'),
                'reviews.customer.user',
            ]);

        $this->applyCustomerVisibility($query);

        return $query->findOrFail($productId);
    }

    protected function applyCustomerVisibility(Builder $query): void
    {
        $query->where('product_in_stock', '>', 0)
            ->where(function (Builder $q): void {
                $q->whereNull('description')
                    ->orWhereRaw("description NOT LIKE '%STATUS:draft%'");
            });
    }

    protected function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('product_name'),
            'price_desc' => $query->orderByDesc('price')->orderBy('product_name'),
            'rating_desc' => $query->orderByDesc('reviews_avg_rating')->orderBy('product_name'),
            default => $query->orderBy('product_name'),
        };
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $query
            ->when($filters['q'] ?? null, fn (Builder $q, $v) => $q->whereRaw('LOWER(product_name) LIKE ?', ['%'.strtolower($v).'%']))
            ->when(! empty($filters['category_id']), function (Builder $q) use ($filters): void {
                $q->whereIn('category_id', (array) $filters['category_id']);
            })
            ->when(! empty($filters['shop_id']), function (Builder $q) use ($filters): void {
                $q->whereIn('shop_id', (array) $filters['shop_id']);
            })
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

