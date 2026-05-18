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
$traderPageTitle = 'Orders';
$traderPageEyebrow = 'Fulfillment';
$traderPageSubtitle = count($orders) . ' order(s) shown';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <?php if (! trader_has_shop($me)): ?>
            <section class="dash-panel">
                <p class="dash-empty">Link a shop to your account to view orders.</p>
            </section>
        <?php else: ?>
            <section class="dash-panel">
                <form method="get" class="dash-filters cols-3">
                    <div>
                        <label for="q">Search</label>
                        <input class="input" id="q" name="q" value="<?= h($q) ?>" placeholder="Customer or order ID">
                    </div>
                    <div>
                        <label for="status">Order status</label>
                        <select class="input" id="status" name="status">
                            <option value="">All statuses</option>
                            <?php foreach (['pending', 'placed', 'processing', 'ready', 'completed', 'cancelled'] as $opt): ?>
                                <option value="<?= h($opt) ?>" <?= $st === $opt ? 'selected' : '' ?>><?= h(ucfirst($opt)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a class="btn btn-outline" href="<?= h(portal_url('trader/orders.php')) ?>">Reset</a>
                    </div>
                </form>

                <div class="table-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$orders): ?>
                                <tr><td colspan="6" class="dash-empty">No orders found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <?php
                                    $oid = (string) ($o['order_id'] ?? '');
                                    $status = (string) ($o['order_status'] ?? '');
                                    ?>
                                    <tr>
                                        <td><a class="dash-order-link" href="<?= h(portal_url('trader/order-details.php?id=' . rawurlencode($oid))) ?>">#<?= h($oid) ?></a></td>
                                        <td><?= h(trim((string) ($o['first_name'] ?? '') . ' ' . (string) ($o['last_name'] ?? ''))) ?></td>
                                        <td><strong>$<?= number_format((float) ($o['amount'] ?? 0), 2) ?></strong></td>
                                        <td><?= h((string) ($o['payment_status'] ?? '—')) ?></td>
                                        <td><span class="status-pill <?= h(trader_status_pill_class($status)) ?>"><?= h(ucfirst(strtolower($status))) ?></span></td>
                                        <td><a class="btn btn-outline" href="<?= h(portal_url('trader/order-details.php?id=' . rawurlencode($oid))) ?>">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
