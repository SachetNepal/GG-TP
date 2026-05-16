<?php
/**
 * JSON API: dashboard KPIs + chart series + lists (AJAX).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/dashboard-queries.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$me = auth_user();
if (!$me || ($me['trader_id'] ?? '') === '') {
    json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
}

$data = trader_dashboard_data(trader_shop_id($me));
$data['ok'] = true;
json_response($data);
