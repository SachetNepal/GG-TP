<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    json_response(['ok' => false, 'error' => 'Method'], 405);
}

$me = auth_user();
if (!$me || strtolower($me['role']) !== 'trader' || (int) $me['shop_id'] < 1) {
    json_response(['ok' => false, 'error' => 'Unauthorized'], 401);
}

$shopId = (int) $me['shop_id'];
$q = trim((string) ($_GET['q'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$per = 12;
$off = ($page - 1) * $per;

$sub = 'SELECT DISTINCT oi.order_id FROM order_item oi
        INNER JOIN product p ON p.product_id = oi.product_id
        WHERE p.shop_id = :sid';

$binds = ['sid' => $shopId];
$filter = "o.order_id IN ($sub)";

if ($q !== '') {
    $filter .= " AND (LOWER(u.first_name || ' ' || u.last_name) LIKE :q OR TO_CHAR(o.order_id) LIKE :qid)";
    $binds['q'] = '%' . strtolower($q) . '%';
    $binds['qid'] = '%' . $q . '%';
}
if ($status !== '') {
    $filter .= ' AND LOWER(o.status) = LOWER(:st)';
    $binds['st'] = $status;
}

try {
    $rows = db_fetch_all(
        "SELECT o.order_id, o.amount, o.status AS order_status, o.order_date,
                u.first_name, u.last_name, pay.payment_status
         FROM \"ORDER\" o
         INNER JOIN \"USER\" u ON u.user_id = o.user_id
         LEFT JOIN payment pay ON pay.order_id = o.order_id
         WHERE $filter
         ORDER BY o.order_date DESC
         OFFSET :off ROWS FETCH NEXT :per ROWS ONLY",
        array_merge($binds, ['off' => $off, 'per' => $per])
    );

    $total = (int) (db_fetch_scalar(
        "SELECT COUNT(*) FROM \"ORDER\" o
         INNER JOIN \"USER\" u ON u.user_id = o.user_id
         WHERE $filter",
        $binds
    ) ?? 0);
} catch (Throwable $e) {
    error_log('fetch-orders: ' . $e->getMessage());
    $rows = [];
    $total = 0;
}

json_response([
    'ok' => true,
    'orders' => $rows,
    'page' => $page,
    'total' => $total,
    'perPage' => $per,
]);
