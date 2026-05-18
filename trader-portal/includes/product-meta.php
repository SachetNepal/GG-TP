<?php
/**
 * Parse product metadata embedded in DESCRIPTION (trader portal convention).
 */
declare(strict_types=1);

/**
 * @return array{status: string, raw: string}
 */
function product_parse_meta(?string $description): array
{
    $status = 'published';
    if ($description === null || $description === '') {
        return ['status' => $status, 'raw' => ''];
    }
    if (preg_match('/<!--(.+?)-->/s', $description, $m)) {
        $parts = explode('|', $m[1]);
        foreach ($parts as $part) {
            if (str_starts_with($part, 'STATUS:')) {
                $status = strtolower(trim(substr($part, 7)));
            }
        }
    }

    return ['status' => $status !== '' ? $status : 'published', 'raw' => $description];
}

function product_status_label(?string $description, int $stock): string
{
    $meta = product_parse_meta($description);
    if ($stock <= 0) {
        return 'inactive';
    }
    if ($meta['status'] === 'draft') {
        return 'draft';
    }

    return 'active';
}

/**
 * Update STATUS: in description metadata block.
 */
function product_display_description(?string $description): string
{
    if ($description === null || $description === '') {
        return '';
    }
    $text = $description;
    if (preg_match('/\|IMG:[^|\n]*/', $text)) {
        $text = preg_replace('/\|IMG:[^|\n]*/', '', $text) ?? $text;
    }
    if (preg_match('/<!--.+?-->/s', $text)) {
        $text = preg_replace('/\s*<!--.+?-->\s*/s', '', $text) ?? $text;
    }

    return trim($text);
}

function product_set_status_in_description(string $description, string $status): string
{
    $status = strtolower($status);
    if (!preg_match('/<!--(.+?)-->/s', $description, $m, PREG_OFFSET_CAPTURE)) {
        return rtrim($description) . "\n\n<!--STATUS:" . $status . '-->';
    }

    $inner = $m[1][0];
    $parts = array_filter(explode('|', $inner), static fn ($p) => !str_starts_with($p, 'STATUS:'));
    $parts[] = 'STATUS:' . $status;
    $replacement = '<!--' . implode('|', $parts) . '-->';

    return substr($description, 0, $m[0][1])
        . $replacement
        . substr($description, $m[0][1] + strlen($m[0][0]));
}
