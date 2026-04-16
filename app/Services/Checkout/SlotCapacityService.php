<?php

namespace App\Services\Checkout;

use App\Models\CollectionSlot;

class SlotCapacityService
{
    public const VALID_DATES = ['Wednesday', 'Thursday', 'Friday'];
    public const VALID_TIMES = ['10 AM – 1 PM', '1 PM – 4 PM', '4 PM – 7 PM'];
    public const MAX_ORDERS_PER_SLOT = 20;

    public function assertSlotAvailable(string $date, string $time): void
    {
        if (!in_array($date, self::VALID_DATES, true)) {
            abort(422, 'Invalid collection day');
        }

        if (!in_array($time, self::VALID_TIMES, true)) {
            abort(422, 'Invalid collection time');
        }

        $count = CollectionSlot::query()
            ->where('date', $date)
            ->where('time', $time)
            ->count();

        if ($count >= self::MAX_ORDERS_PER_SLOT) {
            abort(422, 'Selected slot is full');
        }
    }
}

