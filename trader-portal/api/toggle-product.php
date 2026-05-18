<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/product-meta.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'POST required'], 405);
}

$me = auth_user();
if (!$me || ($me['trader_id'] ?? '') === '' || !trader_has_shop($me)) {
    json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
}

$csrf = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!is_string($csrf) || !hash_equals($_SESSION['_csrf'] ?? '', $csrf)) {
    json_response(['ok' => false, 'error' => 'CSRF'], 419);
}

$productId = trim((string) ($_POST['product_id'] ?? ''));
$action = strtolower(trim((string) ($_POST['action'] ?? '')));
$shopId = trader_shop_id($me);

if ($productId === '' || !in_array($action, ['activate', 'deactivate'], true)) {
    json_response(['ok' => false, 'error' => 'Invalid request'], 422);
}

$row = db_fetch_one(
    'SELECT product_id, description, product_in_stock FROM product
     WHERE product_id = :pid AND shop_id = :sid',
    ['pid' => $productId, 'sid' => $shopId]
);

if (!$row) {
    json_response(['ok' => false, 'error' => 'Product not found'], 404);
}

$desc = (string) ($row['description'] ?? '');
$newStatus = $action === 'activate' ? 'published' : 'draft';
$newStock = $action === 'activate' ? max(1, (int) ($row['product_in_stock'] ?? 0)) : 0;
$newDesc = product_set_status_in_description($desc, $newStatus);

try {
    $st = db_execute(
        'UPDATE product SET description = :d, product_in_stock = :s
         WHERE product_id = :pid AND shop_id = :sid',
        ['d' => $newDesc, 's' => $newStock, 'pid' => $productId, 'sid' => $shopId]
    );
    if ($st) {
        oci_free_statement($st);
    }
    db_commit();
    json_response([
        'ok' => true,
        'status' => product_status_label($newDesc, $newStock),
    ]);
} catch (Throwable $e) {
    db_rollback();
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
