<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends BaseOracleModel
{
    protected $table = 'CUSTOMER';
    protected $primaryKey = 'customer_id';
    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'customer_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    public function basket(): HasOne
    {
        return $this->hasOne(Basket::class, 'customer_id', 'customer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id', 'customer_id');
    }
}

