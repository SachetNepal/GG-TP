<?php

namespace App\Support;

use App\Models\Shop;

final class ShopMedia
{
    /**
     * Default banner/icon when no custom logo is uploaded.
     */
    public static function defaultLogoForShopName(?string $shopName): ?string
    {
        if ($shopName === null || trim($shopName) === '') {
            return null;
        }

        $key = strtolower(trim($shopName));
        $icon = match (true) {
            str_contains($key, 'butcher') || str_contains($key, 'meat') => 'butcher.png',
            str_contains($key, 'fish') || str_contains($key, 'catch') => 'fishmonger.png',
            str_contains($key, 'baker') => 'bakery.png',
            str_contains($key, 'deli') => 'delicatessen.png',
            str_contains($key, 'green') || str_contains($key, 'grocery') || str_contains($key, 'fruit') => 'greengrocer.png',
            default => null,
        };

        return $icon !== null ? asset('assets/icons/' . $icon) : null;
    }

    /**
     * Trader-uploaded logo or category-style default.
     */
    public static function logoUrl(?string $shopId, ?string $shopName = null): ?string
    {
        if ($shopId === null || trim($shopId) === '') {
            return self::defaultLogoForShopName($shopName);
        }

        $uploaded = self::uploadedLogoUrl($shopId);
        if ($uploaded !== null) {
            return $uploaded;
        }

        return self::defaultLogoForShopName($shopName);
    }

    public static function uploadedLogoUrl(string $shopId): ?string
    {
        $shopId = trim($shopId);
        if ($shopId === '') {
            return null;
        }

        $dir = base_path('trader-portal/assets/uploads/shop/' . $shopId);
        if (! is_dir($dir)) {
            return null;
        }

        $matches = glob($dir . '/logo.*');
        if ($matches === false || $matches === []) {
            return null;
        }

        $file = basename($matches[0]);

        return url('trader-portal/assets/uploads/shop/' . rawurlencode($shopId) . '/' . rawurlencode($file));
    }

    public static function forShop(Shop $shop): ?string
    {
        return self::logoUrl($shop->shop_id, $shop->shop_name);
    }
}
