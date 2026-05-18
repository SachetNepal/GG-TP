<?php
/**
 * Bootstrap Laravel for mail and shared auth services from the trader portal.
 */
declare(strict_types=1);

function portal_laravel_app(): \Illuminate\Foundation\Application
{
    static $app = null;
    if ($app !== null) {
        return $app;
    }

    $root = dirname(__DIR__, 2);
    require $root . '/vendor/autoload.php';
    $app = require $root . '/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    return $app;
}
