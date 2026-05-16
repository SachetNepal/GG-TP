<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shop extends BaseOracleModel
{
    protected $table = 'SHOP';
    protected $primaryKey = 'shop_id';
    public $timestamps = false;

    protected $fillable = [
        'shop_name',
        'location',
        'trader_id',
        'contact_info',
    ];

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class, 'trader_id', 'trader_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_id', 'shop_id');
    }
}

