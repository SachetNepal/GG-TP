<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/dashboard-queries.php';

$me = require_trader();
$shopId = trader_shop_id($me);
$dash = trader_dashboard_data($shopId);
$chartJson = json_encode($dash['daily'], JSON_THROW_ON_ERROR);
$upcoming = $dash['upcoming'];
$top = $dash['top_products'];
$displayName = trim((string) ($me['display_name'] ?? 'Trader'));

$pageTitle = 'Trader Dashboard';
$traderLayout = true;
$wrapId = 'traderMain';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php $flash = flash_get();
        if ($flash): ?>
            <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>

        <?php if (! trader_has_shop($me)): ?>
            <div class="alert alert-warning">
                No shop is linked to your account yet. Complete your
                <a href="<?= h(portal_url('trader/profile.php')) ?>">shop profile</a> or contact support.
            </div>
        <?php endif; ?>

        <header class="dash-header">
            <div>
                <p class="dash-eyebrow">Trader dashboard</p>
                <h1 class="dash-title">Welcome back, <?= h($displayName) ?></h1>
                <p class="dash-subtitle"><?= h($dash['week_label']) ?> · Updated <?= h($dash['last_updated']) ?></p>
            </div>
            <div class="dash-header-actions">
                <a href="<?= h(portal_url('trader/add-product.php')) ?>" class="btn btn-primary">Add product</a>
                <a href="<?= h(portal_url('trader/orders.php')) ?>" class="btn btn-outline">View orders</a>
            </div>
        </header>

        <div class="kpi-row dash-kpi-row">
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Revenue this week</p>
                <p class="kpi-value" id="statRevenue">$<?= number_format($dash['revenue'], 2) ?></p>
            </article>
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Orders this week</p>
                <p class="kpi-value" id="statOrders"><?= (int) $dash['orders'] ?></p>
            </article>
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Active products</p>
                <p class="kpi-value" id="statProducts"><?= (int) $dash['products'] ?></p>
            </article>
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Collection slots</p>
                <p class="kpi-value" id="statSlots"><?= (int) $dash['slots'] ?></p>
            </article>
        </div>

        <div class="dash-grid">
            <section class="dash-panel">
                <h2 class="wf-section-title">Daily revenue</h2>
                <div class="chart-wrap dash-chart-wrap">
                    <canvas id="chartRevenue" height="260" aria-label="Daily revenue chart"></canvas>
                </div>
            </section>

            <section class="dash-panel">
                <div class="dash-panel-head">
                    <h2 class="wf-section-title">Recent orders</h2>
                    <a href="<?= h(portal_url('trader/orders.php')) ?>" class="dash-link">See all</a>
                </div>
                <div class="table-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$upcoming): ?>
                                <tr>
                                    <td colspan="4" class="dash-empty">No recent orders for your shop.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($upcoming as $u): ?>
                                    <?php
                                    $oid = (string) ($u['order_id'] ?? '');
                                    $status = strtolower((string) ($u['order_status'] ?? 'pending'));
                                    ?>
                                    <tr>
                                        <td>
                                            <a class="dash-order-link" href="<?= h(portal_url('trader/order-details.php?id=' . rawurlencode($oid))) ?>">
                                                #<?= h($oid) ?>
                                            </a>
                                        </td>
                                        <td><?= h((string) ($u['customer_name'] ?? '')) ?></td>
                                        <td><span class="status-pill status-pill--<?= h(preg_replace('/[^a-z]/', '', $status) ?: 'pending') ?>"><?= h(ucfirst($status)) ?></span></td>
                                        <td class="text-right"><strong>$<?= number_format((float) ($u['amount'] ?? 0), 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section class="dash-panel dash-panel--wide">
            <h2 class="wf-section-title">Top products this week</h2>
            <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$top): ?>
                            <tr><td colspan="5" class="dash-empty">No sales this week yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($top as $row): ?>
                                <?php
                                $stockStatus = (string) ($row['stock_status'] ?? 'OK');
                                $pillClass = match ($stockStatus) {
                                    'Out' => 'status-pill--out',
                                    'Low' => 'status-pill--low',
                                    default => 'status-pill--ok',
                                };
                                ?>
                                <tr>
                                    <td><strong><?= h((string) ($row['product_name'] ?? '')) ?></strong></td>
                                    <td><?= (int) ($row['order_count'] ?? 0) ?></td>
                                    <td>$<?= number_format((float) ($row['revenue'] ?? 0), 2) ?></td>
                                    <td><?= (int) ($row['stock'] ?? 0) ?></td>
                                    <td><span class="status-pill <?= h($pillClass) ?>"><?= h($stockStatus) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

<script>
window.__DASHBOARD_CHART__ = <?= $chartJson ?>;
window.__API_STATS__ = <?= json_encode(portal_url('api/dashboard-stats.php'), JSON_THROW_ON_ERROR) ?>;
</script>
<?php
require_once dirname(__DIR__) . '/includes/footer.php';
