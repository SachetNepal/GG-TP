<?php
/**
 * Common bootstrap: config, session, UTF-8.
 */
declare(strict_types=1);

$configPath = dirname(__DIR__) . '/config.php';
if (!is_readable($configPath)) {
    http_response_code(500);
    exit('Configuration missing. Copy config.example.php to config.php');
}

require_once $configPath;

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';
