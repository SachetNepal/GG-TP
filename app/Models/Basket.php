<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Basket extends BaseOracleModel
{
    protected $table = 'BASKET';
    protected $primaryKey = 'basket_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'basket_id',
        'created_date',
        'customer_id',
    ];

    protected function casts(): array
    {
        return [
            'created_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BasketItem::class, 'basket_id', 'basket_id');
    }
}

