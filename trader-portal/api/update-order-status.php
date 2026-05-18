<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'POST required'], 405);
}

$me = auth_user();
if (!$me || ($me['trader_id'] ?? '') === '' || !trader_has_shop($me)) {
    json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
}
$shopId = trader_shop_id($me);

$csrf = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!is_string($csrf) || !hash_equals($_SESSION['_csrf'] ?? '', $csrf)) {
    json_response(['ok' => false, 'error' => 'CSRF'], 419);
}

$orderId = trim((string) ($_POST['order_id'] ?? ''));
$status = strtolower(trim((string) ($_POST['status'] ?? '')));
$allowed = ['pending', 'placed', 'processing', 'ready', 'completed', 'cancelled'];

if ($orderId === '' || !in_array($status, $allowed, true)) {
    json_response(['ok' => false, 'error' => 'Invalid status'], 422);
}

$linked = (int) (db_fetch_scalar(
    "SELECT COUNT(*) FROM order_item oi
     INNER JOIN product p ON p.product_id = oi.product_id
     WHERE oi.order_id = :oid AND p.shop_id = :sid",
    ['oid' => $orderId, 'sid' => $shopId]
) ?? 0);

if ($linked < 1) {
    json_response(['ok' => false, 'error' => 'Order not found'], 404);
}

try {
    $st = db_execute(
        'UPDATE orders SET status = :st WHERE order_id = :oid',
        ['st' => $status, 'oid' => $orderId]
    );
    if ($st) {
        oci_free_statement($st);
    }
    db_commit();
    json_response(['ok' => true, 'status' => $status]);
} catch (Throwable $e) {
    db_rollback();
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
