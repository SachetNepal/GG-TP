<?php

namespace App\Support;

final class OrderUi
{
    public static function statusPillClass(string $status): string
    {
        return match (strtolower(trim($status))) {
            'completed', 'ready', 'confirmed' => 'status-pill--ok',
            'cancelled' => 'status-pill--out',
            'pending' => 'status-pill--pending',
            default => 'status-pill--low',
        };
    }
}
