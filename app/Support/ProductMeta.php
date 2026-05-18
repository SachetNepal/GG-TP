<?php

namespace App\Support;

final class ProductMeta
{
    public static function parseStatus(?string $description): string
    {
        if ($description === null || $description === '') {
            return 'published';
        }

        if (preg_match('/<!--(.+?)-->/s', $description, $m)) {
            foreach (explode('|', $m[1]) as $part) {
                if (str_starts_with($part, 'STATUS:')) {
                    $status = strtolower(trim(substr($part, 7)));

                    return $status !== '' ? $status : 'published';
                }
            }
        }

        return 'published';
    }

    public static function isVisibleToCustomers(?string $description, int $stock): bool
    {
        if ($stock <= 0) {
            return false;
        }

        return self::parseStatus($description) !== 'draft';
    }
}
