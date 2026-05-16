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

$pid = trim((string) ($_POST['product_id'] ?? ''));
$shopId = trader_shop_id($me);

if ($pid === '') {
    json_response(['ok' => false, 'error' => 'Invalid product'], 422);
}

try {
    $st = db_execute(
        'DELETE FROM product WHERE product_id = :pid AND shop_id = :sid',
        ['pid' => $pid, 'sid' => $shopId]
    );
    if (!$st) {
        throw new RuntimeException('Delete failed');
    }
    oci_free_statement($st);
    db_commit();
    json_response(['ok' => true]);
} catch (Throwable $e) {
    db_rollback();
    json_response(['ok' => false, 'error' => $e->getMessage()], 500);
}
