<?php

namespace App\Support;

final class ProductMeta
{
    /**
     * @return array{
     *     text: string,
     *     status: string,
     *     images: list<string>,
     *     unit: ?string,
     *     max_per_order: ?int,
     *     tags: list<string>,
     *     subcategory: ?string,
     *     availability: ?string
     * }
     */
    public static function parse(?string $description): array
    {
        $raw = trim((string) $description);
        $result = [
            'text' => $raw,
            'status' => 'published',
            'images' => [],
            'unit' => null,
            'max_per_order' => null,
            'tags' => [],
            'subcategory' => null,
            'availability' => null,
        ];

        if ($raw === '') {
            return $result;
        }

        if (preg_match('/\|IMG:([^|\n]+)/', $raw, $imgMatch)) {
            $result['images'] = array_values(array_filter(array_map(
                'trim',
                explode(',', $imgMatch[1])
            )));
            $raw = preg_replace('/\|IMG:[^|\n]*/', '', $raw) ?? $raw;
        }

        if (preg_match('/<!--(.+?)-->/s', $raw, $metaMatch)) {
            foreach (explode('|', $metaMatch[1]) as $part) {
                $part = trim($part);
                if ($part === '') {
                    continue;
                }
                if (str_starts_with($part, 'STATUS:')) {
                    $status = strtolower(trim(substr($part, 7)));
                    $result['status'] = $status !== '' ? $status : 'published';
                } elseif (str_starts_with($part, 'UNIT:')) {
                    $result['unit'] = trim(substr($part, 5)) ?: null;
                } elseif (str_starts_with($part, 'MAX:')) {
                    $result['max_per_order'] = (int) substr($part, 4);
                } elseif (str_starts_with($part, 'TAGS:')) {
                    $result['tags'] = array_values(array_filter(array_map(
                        'trim',
                        explode(',', substr($part, 5))
                    )));
                } elseif (str_starts_with($part, 'SUBCAT:')) {
                    $result['subcategory'] = trim(substr($part, 7)) ?: null;
                } elseif (str_starts_with($part, 'AVAIL:')) {
                    $result['availability'] = trim(substr($part, 6)) ?: null;
                }
            }

            $raw = preg_replace('/\s*<!--.+?-->\s*/s', '', $raw) ?? $raw;
        }

        $result['text'] = trim($raw);

        return $result;
    }

    public static function displayDescription(?string $description): string
    {
        return self::parse($description)['text'];
    }

    /**
     * @return list<string>
     */
    public static function imageFilenames(?string $description): array
    {
        return self::parse($description)['images'];
    }

    public static function imageUrl(string $shopId, string $filename): string
    {
        $shopId = trim($shopId);
        $filename = ltrim(trim($filename), '/');

        return url('trader-portal/assets/uploads/products/' . rawurlencode($shopId) . '/' . rawurlencode($filename));
    }

    /**
     * @return list<string>
     */
    public static function imageUrls(?string $shopId, ?string $description): array
    {
        if ($shopId === null || trim($shopId) === '') {
            return [];
        }

        $urls = [];
        foreach (self::imageFilenames($description) as $file) {
            $urls[] = self::imageUrl($shopId, $file);
        }

        return $urls;
    }

    public static function primaryImageUrl(?string $shopId, ?string $description): ?string
    {
        $files = self::imageFilenames($description);
        if ($files === [] || $shopId === null || trim($shopId) === '') {
            return null;
        }

        return self::imageUrl($shopId, $files[0]);
    }

    public static function categoryPlaceholderUrl(?string $categoryName): ?string
    {
        if ($categoryName === null || trim($categoryName) === '') {
            return null;
        }

        $key = strtolower(trim($categoryName));
        $icon = match (true) {
            str_contains($key, 'meat') || str_contains($key, 'butcher') => 'butcher.png',
            str_contains($key, 'fish') => 'fishmonger.png',
            str_contains($key, 'baker') => 'bakery.png',
            str_contains($key, 'deli') => 'delicatessen.png',
            str_contains($key, 'fruit') || str_contains($key, 'vegetable') || str_contains($key, 'green') => 'greengrocer.png',
            default => null,
        };

        if ($icon === null) {
            return null;
        }

        return asset('assets/icons/' . $icon);
    }

    public static function displayImageUrl(?string $shopId, ?string $description, ?string $categoryName = null): ?string
    {
        $gallery = self::displayImageUrls($shopId, $description, $categoryName);

        return $gallery[0] ?? null;
    }

    /**
     * All customer-visible images (uploaded first, else one category placeholder).
     *
     * @return list<string>
     */
    public static function displayImageUrls(?string $shopId, ?string $description, ?string $categoryName = null): array
    {
        $uploaded = self::imageUrls($shopId, $description);
        if ($uploaded !== []) {
            return $uploaded;
        }

        $placeholder = self::categoryPlaceholderUrl($categoryName);
        if ($placeholder !== null) {
            return [$placeholder];
        }

        return [];
    }

    public static function usesCategoryPlaceholder(?string $shopId, ?string $description): bool
    {
        return self::imageFilenames($description) === [];
    }

    public static function parseStatus(?string $description): string
    {
        return self::parse($description)['status'];
    }

    public static function isVisibleToCustomers(?string $description, int $stock): bool
    {
        if ($stock <= 0) {
            return false;
        }

        return self::parseStatus($description) !== 'draft';
    }
}
