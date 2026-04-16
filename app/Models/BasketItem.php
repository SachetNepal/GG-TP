<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketItem extends BaseOracleModel
{
    protected $table = 'BASKET_ITEMS';
    protected $primaryKey = 'basket_item_id';
    public $timestamps = false;

    protected $fillable = [
        'basket_id',
        'product_id',
    ];

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class, 'basket_id', 'basket_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

