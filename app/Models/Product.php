<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends BaseOracleModel
{
    protected $table = 'PRODUCT';
    protected $primaryKey = 'product_id';
    public $timestamps = false;
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'product_in_stock',
        'category_id',
        'shop_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'product_in_stock' => 'integer',
            'shop_id' => 'string',
            'category_id' => 'string',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function basketItems(): HasMany
    {
        return $this->hasMany(BasketItem::class, 'product_id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'PRODUCT_DISCOUNT', 'product_id', 'discount_id')
            ->withPivot('product_discount_id');
    }
}

