<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_response(['ok' => false, 'error' => 'POST required'], 405);
}

$me = auth_user();
if (!$me || strtolower($me['role']) !== 'trader' || ! trader_has_shop($me)) {
    json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
}

if (!portal_verify_csrf()) {
    json_response(['ok' => false, 'error' => 'CSRF'], 419);
}

$shopId = trader_shop_id($me);
$pid = trim((string) ($_POST['product_id'] ?? ''));
$name = trim((string) ($_POST['product_name'] ?? ''));
$desc = trim((string) ($_POST['description'] ?? ''));
$categoryId = trim((string) ($_POST['category_id'] ?? ''));
$price = (float) str_replace(',', '.', (string) ($_POST['price'] ?? '0'));
$stock = (int) ($_POST['stock'] ?? 0);

if ($pid === '' || $name === '' || $categoryId === '' || $price <= 0) {
    json_response(['ok' => false, 'error' => 'Validation failed'], 422);
}

$sql = 'UPDATE product SET product_name = :n, description = :d, price = :p,
        product_in_stock = :s, category_id = :c
        WHERE product_id = :pid AND shop_id = :sid';

try {
    $st = db_execute($sql, [
        'n' => $name,
        'd' => $desc,
        'p' => $price,
        's' => $stock,
        'c' => $categoryId,
        'pid' => $pid,
        'sid' => $shopId,
    ]);
    if (!$st) {
        throw new RuntimeException('Update failed');
    }
    oci_free_statement($st);
    db_commit();
    json_response(['ok' => true]);
} catch (Throwable $e) {
    db_rollback();
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
