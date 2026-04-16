<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trader extends BaseOracleModel
{
    protected $table = 'TRADER';
    protected $primaryKey = 'trader_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class, 'trader_id', 'trader_id');
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class, 'trader_id', 'trader_id');
    }
}

