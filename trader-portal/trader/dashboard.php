<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/dashboard-queries.php';

$me = require_trader();
$shopId = (int) $me['shop_id'];
$dash = trader_dashboard_data($shopId);
$chartJson = json_encode($dash['daily'], JSON_THROW_ON_ERROR);
$upcoming = $dash['upcoming'];
$top = $dash['top_products'];

$pageTitle = 'Trader Dashboard';
$traderLayout = true;
$wrapId = 'traderMain';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <?php $flash = flash_get();
    if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>

    <?php if ($shopId < 1): ?>
        <div class="alert alert-warning">
            No shop is linked to your trader account. Create a <code>SHOP</code> row for your <code>TRADER_ID</code> in Oracle, then refresh.
        </div>
    <?php endif; ?>

    <section class="panel welcome-panel">
        <div class="welcome-panel-row">
            <div>
                <p class="muted">Dashboard · Last updated</p>
                <p class="welcome-strong"><?= h($dash['last_updated']) ?></p>
            </div>
            <div class="text-right">
                <p class="muted">Period</p>
                <p class="welcome-strong"><?= h($dash['week_label']) ?></p>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <p class="stat-label">This week's revenue</p>
            <p class="stat-value" id="statRevenue">£<?= number_format($dash['revenue'], 2) ?></p>
        </article>
        <article class="stat-card">
            <p class="stat-label">Orders this week</p>
            <p class="stat-value" id="statOrders"><?= (int) $dash['orders'] ?></p>
        </article>
        <article class="stat-card">
            <p class="stat-label">Active products</p>
            <p class="stat-value" id="statProducts"><?= (int) $dash['products'] ?></p>
        </article>
        <article class="stat-card">
            <p class="stat-label">Collection slots</p>
            <p class="stat-value" id="statSlots"><?= (int) $dash['slots'] ?></p>
        </article>
    </section>

    <div class="grid-two">
        <section class="panel chart-panel">
            <h2 class="panel-title">Daily revenue this week</h2>
            <div class="chart-wrap">
                <canvas id="chartRevenue" height="260" aria-label="Daily revenue chart"></canvas>
            </div>
        </section>
        <section class="panel">
            <h2 class="panel-title">Upcoming orders</h2>
            <div class="order-cards">
                <?php if (!$upcoming): ?>
                    <p class="muted">No recent orders for your shop.</p>
                <?php else: ?>
                    <?php foreach ($upcoming as $u): ?>
                        <article class="mini-order-card">
                            <div class="mini-order-top">
                                <span class="badge"><?= h((string) ($u['order_status'] ?? '')) ?></span>
                                <strong>£<?= number_format((float) ($u['amount'] ?? 0), 2) ?></strong>
                            </div>
                            <p class="mini-order-name"><?= h((string) ($u['customer_name'] ?? '')) ?></p>
                            <p class="muted small">
                                <?= h((string) ($u['order_id'] ?? '')) ?> ·
                                <?= isset($u['order_date']) ? h((string) $u['order_date']) : '—' ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="panel">
        <h2 class="panel-title">Top products this week</h2>
        <div class="table-scroll">
            <table class="data-table">
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
                        <tr><td colspan="5" class="muted">No sales this week yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($top as $row): ?>
                            <tr>
                                <td><?= h((string) ($row['product_name'] ?? '')) ?></td>
                                <td><?= h((string) ($row['order_count'] ?? '')) ?></td>
                                <td>£<?= number_format((float) ($row['revenue'] ?? 0), 2) ?></td>
                                <td><?= h((string) ($row['stock'] ?? '')) ?></td>
                                <td><span class="pill"><?= h((string) ($row['stock_status'] ?? '')) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<script>
window.__DASHBOARD_CHART__ = <?= $chartJson ?>;
window.__API_STATS__ = <?= json_encode(portal_url('api/dashboard-stats.php'), JSON_THROW_ON_ERROR) ?>;
</script>
<?php
require_once dirname(__DIR__) . '/includes/footer.php';
