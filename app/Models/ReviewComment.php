<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewComment extends BaseOracleModel
{
    protected $table = 'REVIEW_COMMENT';

    protected $primaryKey = 'comment_id';

    public $timestamps = false;

    protected $fillable = [
        'comment_id',
        'review_id',
        'comment_body',
        'comment_date',
        'customer_id',
    ];

    protected function casts(): array
    {
        return [
            'comment_date' => 'datetime',
        ];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'review_id', 'review_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
