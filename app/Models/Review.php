<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends BaseOracleModel
{
    protected $table = 'REVIEW';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    protected $fillable = [
        'review_id',
        'rating',
        'review_body',
        'review_date',
        'customer_id',
        'product_id',
        'trader_reply',
        'trader_reply_date',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'review_date' => 'datetime',
            'trader_reply_date' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReviewComment::class, 'review_id', 'review_id')
            ->orderBy('comment_date');
    }
}

