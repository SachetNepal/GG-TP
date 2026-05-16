<?php

namespace App\Services\Checkout;

use App\Models\CollectionSlot;
use Carbon\Carbon;
use Carbon\CarbonInterface;

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

        $pickupDate = $this->resolvePickupDate($date);
        $pickupDateSql = $pickupDate->format('Y-m-d');

        $count = CollectionSlot::query()
            ->whereRaw('TRUNC("DATE_") = TO_DATE(?, \'YYYY-MM-DD\')', [$pickupDateSql])
            ->whereRaw('"TIME_" = ?', [$time])
            ->count();

        if ($count >= self::MAX_ORDERS_PER_SLOT) {
            abort(422, 'Selected slot is full');
        }
    }

    /** Oracle DATE_ column — convert UI day label to next calendar date. */
    public function resolvePickupDate(string $dayName): CarbonInterface
    {
        if (!in_array($dayName, self::VALID_DATES, true)) {
            abort(422, 'Invalid collection day');
        }

        $weekday = match ($dayName) {
            'Wednesday' => Carbon::WEDNESDAY,
            'Thursday' => Carbon::THURSDAY,
            'Friday' => Carbon::FRIDAY,
        };

        return Carbon::now()->next($weekday)->startOfDay();
    }
}

