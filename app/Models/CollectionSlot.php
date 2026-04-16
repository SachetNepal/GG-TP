<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionSlot extends BaseOracleModel
{
    protected $table = 'COLLECTION_SLOT';
    protected $primaryKey = 'slot_id';
    public $timestamps = false;

    protected $fillable = [
        'date',
        'time',
        'order_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'string',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}

