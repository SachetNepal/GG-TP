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
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <p class="muted"><a href="<?= h(portal_url('trader/orders.php')) ?>">← Back to orders</a></p>
        <h1 class="panel-title">Order #<?= h($oid) ?></h1>
        <?php if ($order): ?>
            <p><strong>Customer:</strong> <?= h(trim((string) ($order['first_name'] ?? '') . ' ' . (string) ($order['last_name'] ?? ''))) ?></p>
            <p><strong>Email:</strong> <?= h((string) ($order['email'] ?? '')) ?></p>
            <p><strong>Phone:</strong> <?= h((string) ($order['phone_num'] ?? '')) ?></p>
            <p><strong>Total:</strong> £<?= number_format((float) ($order['amount'] ?? 0), 2) ?></p>
            <p><strong>Status:</strong> <?= h((string) ($order['status'] ?? '')) ?></p>
            <p><strong>Payment:</strong> <?= h((string) ($order['payment_status'] ?? '')) ?> · £<?= number_format((float) ($order['paid_amount'] ?? 0), 2) ?></p>
        <?php endif; ?>

        <h2 class="panel-title" style="margin-top:24px;">Your line items</h2>
        <table class="data-table">
            <thead>
                <tr><th>Product</th><th>Qty</th><th>Line total</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?= h((string) ($it['product_name'] ?? '')) ?></td>
                        <td><?= (int) ($it['quantity'] ?? 0) ?></td>
                        <td>£<?= number_format((float) ($it['price'] ?? 0) * (int) ($it['quantity'] ?? 0), 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
