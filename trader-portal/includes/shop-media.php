<?php

declare(strict_types=1);

function shop_logo_public_url(string $shopId): ?string
{
    $shopId = trim($shopId);
    if ($shopId === '') {
        return null;
    }

    $dir = dirname(__DIR__) . '/assets/uploads/shop/' . $shopId;
    if (! is_dir($dir)) {
        return null;
    }

    $matches = glob($dir . '/logo.*');
    if ($matches === false || $matches === []) {
        return null;
    }

    return portal_url('assets/uploads/shop/' . rawurlencode($shopId) . '/' . rawurlencode(basename($matches[0])));
}
