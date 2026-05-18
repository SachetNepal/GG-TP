<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/report-export.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$scope = strtolower(trim((string) ($_GET['scope'] ?? 'all')));
if (! in_array($scope, ['daily', 'weekly', 'monthly', 'all'], true)) {
    $scope = 'all';
}

$date = isset($_GET['date']) ? (string) $_GET['date'] : null;
$week = isset($_GET['week']) ? (string) $_GET['week'] : null;
$month = isset($_GET['month']) ? (string) $_GET['month'] : null;

$shopName = (string) ($_SESSION['display_name'] ?? 'Trader');
$row = db_fetch_one('SELECT shop_name FROM shop WHERE shop_id = :sid', ['sid' => $shopId]);
if ($row && ! empty($row['shop_name'])) {
    $shopName = (string) $row['shop_name'];
}

trader_report_send_export($shopId, $shopName, $scope, $date, $week, $month);
