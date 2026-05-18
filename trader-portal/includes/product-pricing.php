<?php

declare(strict_types=1);

const PRODUCT_MAX_PRICE = 9999.99;
const PRODUCT_MAX_STOCK = 9999;
const PRODUCT_MAX_PER_ORDER = 20;

/**
 * Validate trader product pricing & stock fields. Sets $error on failure.
 */
function product_validate_pricing_stock(float $price, int $stock, int $maxOrder, ?string &$error = null): bool
{
    if ($price <= 0 || $price > PRODUCT_MAX_PRICE) {
        $error = 'Price must be greater than $0 and at most $' . number_format(PRODUCT_MAX_PRICE, 2) . '.';

        return false;
    }

    if ($stock < 0 || $stock > PRODUCT_MAX_STOCK) {
        $error = 'Stock available must be between 0 and ' . number_format(PRODUCT_MAX_STOCK) . '.';

        return false;
    }

    if ($maxOrder < 1 || $maxOrder > PRODUCT_MAX_PER_ORDER) {
        $error = 'Max per order must be between 1 and ' . PRODUCT_MAX_PER_ORDER . '.';

        return false;
    }

    return true;
}
