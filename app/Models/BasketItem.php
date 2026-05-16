<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BasketItem extends BaseOracleModel
{
    protected $table = 'BASKET_ITEM';
    protected $primaryKey = 'basket_item_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'basket_id',
        'product_id',
        'quantity',
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

