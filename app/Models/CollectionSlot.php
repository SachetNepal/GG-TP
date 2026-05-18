<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionSlot extends BaseOracleModel
{
    protected $table = 'COLLECTION_SLOT';
    protected $primaryKey = 'slot_id';
    public $timestamps = false;

    protected $fillable = [
        'slot_id',
        'date_',
        'time_',
        'order_id',
        'pickup_location',
    ];

    protected function casts(): array
    {
        return [
            'date_' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /** Human-readable pickup date for customer orders list. */
    public function displayDate(): string
    {
        if ($this->date_ === null) {
            return '—';
        }

        return Carbon::parse($this->date_)->format('l, j M Y');
    }

    /** Pickup time window as stored at checkout. */
    public function displayTime(): string
    {
        $time = trim((string) ($this->time_ ?? ''));

        return $time !== '' ? $time : '—';
    }

    public function displayLocation(): string
    {
        $loc = trim((string) ($this->pickup_location ?? ''));

        return $loc !== '' ? $loc : '—';
    }

    protected function collectionSummary(): Attribute
    {
        return Attribute::get(fn (): string => implode(' · ', array_filter([
            $this->displayLocation() !== '—' ? $this->displayLocation() : null,
            $this->displayDate() !== '—' ? $this->displayDate() : null,
            $this->displayTime() !== '—' ? $this->displayTime() : null,
        ])));
    }
}

