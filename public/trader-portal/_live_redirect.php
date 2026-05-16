<?php

declare(strict_types=1);

/**
 * Send browsers to the live Oracle trader portal (project root trader-portal/).
 */
function portal_live_redirect(string $path = '/login.php'): never
{
    $base = '/GG-TP/trader-portal';
    $config = dirname(__DIR__, 2) . '/trader-portal/config.php';
    if (is_readable($config)) {
        require_once $config;
        $base = rtrim(PORTAL_BASE, '/');
    }

    $path = '/' . ltrim($path, '/');
    $query = $_SERVER['QUERY_STRING'] ?? '';
    $url = $base . $path . ($query !== '' ? '?' . $query : '');

    header('Location: ' . $url, true, 301);
    exit;
}
