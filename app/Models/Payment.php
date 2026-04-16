<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseOracleModel
{
    protected $table = 'PAYMENT';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;

    protected $fillable = [
        'paid_amount',
        'payment_method',
        'payment_status',
        'order_id',
    ];

    protected function casts(): array
    {
        return [
            'paid_amount' => 'float',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}

