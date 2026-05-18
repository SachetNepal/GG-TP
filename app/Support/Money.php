<?php

namespace App\Support;

final class Money
{
    public static function symbol(): string
    {
        return (string) config('shop.symbol', '$');
    }

    public static function format(float|int $amount, int $decimals = 2): string
    {
        return self::symbol().number_format((float) $amount, $decimals);
    }
}
