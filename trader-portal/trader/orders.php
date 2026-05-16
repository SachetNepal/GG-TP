<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$q = trim((string) ($_GET['q'] ?? ''));
$st = trim((string) ($_GET['status'] ?? ''));

$binds = ['sid' => $shopId];
$filter = 'o.order_id IN (
    SELECT DISTINCT oi.order_id FROM order_item oi
    INNER JOIN product p ON p.product_id = oi.product_id
    WHERE p.shop_id = :sid
)';
if ($q !== '') {
    $filter .= " AND (LOWER(u.first_name || ' ' || u.last_name) LIKE :fq OR TO_CHAR(o.order_id) LIKE :fqid)";
    $binds['fq'] = '%' . strtolower($q) . '%';
    $binds['fqid'] = '%' . $q . '%';
}
if ($st !== '') {
    $filter .= ' AND LOWER(o.status) = LOWER(:fst)';
    $binds['fst'] = $st;
}

$orders = [];
if (trader_has_shop($me)) {
    try {
        $orders = db_fetch_all(
            "SELECT o.order_id, o.amount, o.status AS order_status, o.order_date,
                    u.first_name, u.last_name, pay.payment_status
             FROM orders o
             INNER JOIN users u ON u.user_id = o.customer_id
             LEFT JOIN payment pay ON pay.order_id = o.order_id
             WHERE $filter
             ORDER BY o.order_date DESC
             FETCH FIRST 100 ROWS ONLY",
            $binds
        );
    } catch (Throwable $e) {
        $orders = [];
    }
}

$pageTitle = 'Orders';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <h1 class="panel-title">Orders</h1>
        <?php if (! trader_has_shop($me)): ?>
            <p class="muted">Link a shop to your account to view orders.</p>
        <?php else: ?>
        <form method="get" class="form-grid cols-2" style="margin-bottom:18px;">
            <div>
                <label for="q">Search</label>
                <input class="input" id="q" name="q" value="<?= h($q) ?>" placeholder="Customer or order ID">
            </div>
            <div>
                <label for="status">Order status</label>
                <select class="input" id="status" name="status">
                    <option value="">All</option>
                    <?php foreach (['pending','accepted','preparing','ready','completed','cancelled','placed'] as $opt): ?>
                        <option value="<?= h($opt) ?>" <?= $st === $opt ? 'selected' : '' ?>><?= h(ucfirst($opt)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;gap:8px;">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a class="btn btn-outline" href="<?= h(portal_url('trader/orders.php')) ?>">Reset</a>
            </div>
        </form>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Order status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$orders): ?>
                        <tr><td colspan="6" class="muted">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <?php $oid = (string) ($o['order_id'] ?? ''); ?>
                            <tr>
                                <td>#<?= h($oid) ?></td>
                                <td><?= h(trim((string) ($o['first_name'] ?? '') . ' ' . (string) ($o['last_name'] ?? ''))) ?></td>
                                <td>£<?= number_format((float) ($o['amount'] ?? 0), 2) ?></td>
                                <td><?= h((string) ($o['payment_status'] ?? '—')) ?></td>
                                <td><span class="pill"><?= h((string) ($o['order_status'] ?? '')) ?></span></td>
                                <td><a class="btn btn-outline" style="padding:6px 12px;font-size:13px;" href="<?= h(portal_url('trader/order-details.php?id=' . rawurlencode($oid))) ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
