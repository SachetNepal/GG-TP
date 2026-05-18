<?php

namespace App\Models;

use App\Support\ProductMeta;
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

    public function customerDescription(): string
    {
        return ProductMeta::displayDescription($this->description);
    }

    /**
     * @return list<string> Trader-uploaded image URLs only
     */
    public function customerImageUrls(): array
    {
        return ProductMeta::imageUrls($this->shop_id, $this->description);
    }

    /**
     * @return list<string> Uploaded images, or a single category placeholder
     */
    public function customerGalleryUrls(): array
    {
        $categoryName = $this->relationLoaded('category')
            ? ($this->category->category_name ?? null)
            : null;

        return ProductMeta::displayImageUrls($this->shop_id, $this->description, $categoryName);
    }

    public function customerPrimaryImageUrl(): ?string
    {
        $gallery = $this->customerGalleryUrls();

        return $gallery[0] ?? null;
    }

    public function customerUsesPlaceholderImage(): bool
    {
        return ProductMeta::usesCategoryPlaceholder($this->shop_id, $this->description);
    }
}

