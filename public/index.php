<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

/*
 * XAMPP subdirectory install (e.g. http://localhost/GG-TP/login).
 * Must run before Laravel boots; config() is not available yet.
 */
if (is_readable($envFile = __DIR__.'/../.env')) {
    Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
}

$appUrl = $_ENV['APP_URL'] ?? 'http://localhost/GG-TP';
$appBasePath = parse_url($appUrl, PHP_URL_PATH) ?: '';
if (is_string($appBasePath) && $appBasePath !== '' && $appBasePath !== '/') {
    $appBasePath = rtrim($appBasePath, '/');
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($requestPath === $appBasePath || str_starts_with($requestPath, $appBasePath.'/')) {
        $stripped = substr($requestPath, strlen($appBasePath)) ?: '/';
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $_SERVER['REQUEST_URI'] = $stripped.($query !== '' ? '?'.$query : '');
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
