<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);
$oid = trim((string) ($_GET['id'] ?? ''));

if ($oid === '' || ! trader_has_shop($me)) {
    portal_redirect('/trader/orders.php');
}

$allowed = db_fetch_scalar(
    "SELECT COUNT(*) FROM order_item oi
     INNER JOIN product p ON p.product_id = oi.product_id
     WHERE oi.order_id = :oid AND p.shop_id = :sid",
    ['oid' => $oid, 'sid' => $shopId]
);

if ((int) $allowed < 1) {
    flash_set('error', 'Order not linked to your shop.');
    portal_redirect('/trader/orders.php');
}

$order = db_fetch_one(
    "SELECT o.*, u.first_name, u.last_name, u.email, u.phone_num,
            pay.payment_status, pay.paid_amount
     FROM orders o
     INNER JOIN users u ON u.user_id = o.customer_id
     LEFT JOIN payment pay ON pay.order_id = o.order_id
     WHERE o.order_id = :oid",
    ['oid' => $oid]
);

$items = db_fetch_all(
    "SELECT oi.quantity, oi.price, p.product_name
     FROM order_item oi
     INNER JOIN product p ON p.product_id = oi.product_id
     WHERE oi.order_id = :oid AND p.shop_id = :sid",
    ['oid' => $oid, 'sid' => $shopId]
);

$pageTitle = 'Order #' . $oid;
$traderLayout = true;
$traderPageTitle = 'Order #' . $oid;
$traderPageEyebrow = 'Order details';
$traderPageSubtitle = $order ? trim((string) ($order['first_name'] ?? '') . ' ' . (string) ($order['last_name'] ?? '')) : '';
$traderPageActionsHtml = '<a class="btn btn-outline" href="' . h(portal_url('trader/orders.php')) . '">← All orders</a>';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <?php if ($order): ?>
            <?php $cur = strtolower((string) ($order['status'] ?? 'pending')); ?>
            <section class="dash-panel">
                <div class="dash-detail-grid">
                    <div class="dash-detail-item">
                        <span class="label">Customer</span>
                        <span class="value"><?= h(trim((string) ($order['first_name'] ?? '') . ' ' . (string) ($order['last_name'] ?? ''))) ?></span>
                    </div>
                    <div class="dash-detail-item">
                        <span class="label">Email</span>
                        <span class="value"><?= h((string) ($order['email'] ?? '')) ?></span>
                    </div>
                    <div class="dash-detail-item">
                        <span class="label">Phone</span>
                        <span class="value"><?= h((string) ($order['phone_num'] ?? '')) ?></span>
                    </div>
                    <div class="dash-detail-item">
                        <span class="label">Order total</span>
                        <span class="value">$<?= number_format((float) ($order['amount'] ?? 0), 2) ?></span>
                    </div>
                    <div class="dash-detail-item">
                        <span class="label">Status</span>
                        <span class="value"><span class="status-pill <?= h(trader_status_pill_class($cur)) ?>"><?= h(ucfirst($cur)) ?></span></span>
                    </div>
                    <div class="dash-detail-item">
                        <span class="label">Payment</span>
                        <span class="value"><?= h((string) ($order['payment_status'] ?? '—')) ?> · $<?= number_format((float) ($order['paid_amount'] ?? 0), 2) ?></span>
                    </div>
                </div>

                <form id="orderStatusForm" method="post" class="dash-filters cols-2" style="max-width:480px;"
                      data-update-url="<?= h(portal_url('api/update-order-status.php')) ?>">
                    <?= portal_csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= h($oid) ?>">
                    <div>
                        <label for="status">Update status</label>
                        <select class="input" id="status" name="status">
                            <?php foreach (['pending', 'placed', 'processing', 'ready', 'completed', 'cancelled'] as $st): ?>
                                <option value="<?= h($st) ?>" <?= $cur === $st ? 'selected' : '' ?>><?= h(ucfirst($st)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary" id="orderStatusBtn">Save status</button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <section class="dash-panel">
            <h2 class="wf-section-title">Line items (your shop)</h2>
            <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th class="text-right">Line total</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!$items): ?>
                            <tr><td colspan="3" class="dash-empty">No line items.</td></tr>
                        <?php else: ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><strong><?= h((string) ($it['product_name'] ?? '')) ?></strong></td>
                                    <td><?= (int) ($it['quantity'] ?? 0) ?></td>
                                    <td class="text-right">$<?= number_format((float) ($it['price'] ?? 0) * (int) ($it['quantity'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
