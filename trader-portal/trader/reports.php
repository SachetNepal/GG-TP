<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/report-queries.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$period = strtolower(trim((string) ($_GET['period'] ?? 'weekly')));
if (!in_array($period, ['daily', 'weekly', 'monthly'], true)) {
    $period = 'weekly';
}

$report = trader_report_period($shopId, $period);
$top = trader_report_top_products($shopId, $period);

$labels = [
    'daily' => 'Daily',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly',
];

$pageTitle = 'Reports';
$traderLayout = true;
$traderPageTitle = 'Reports';
$traderPageEyebrow = 'Analytics';
$traderPageSubtitle = $labels[$period] . ' summary for your shop';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <nav class="dash-tabs" aria-label="Report period">
            <?php foreach (['daily', 'weekly', 'monthly'] as $p): ?>
                <a class="btn <?= $period === $p ? 'btn-primary' : 'btn-outline' ?>"
                   href="<?= h(portal_url('trader/reports.php?period=' . $p)) ?>">
                    <?= h($labels[$p]) ?> report
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="kpi-row dash-kpi-row">
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Revenue</p>
                <p class="kpi-value">$<?= number_format((float) ($report['revenue'] ?? 0), 2) ?></p>
            </article>
            <article class="kpi-card dash-kpi-card">
                <p class="kpi-label">Orders</p>
                <p class="kpi-value"><?= (int) ($report['orders'] ?? 0) ?></p>
            </article>
        </div>

        <?php if (!empty($report['rows'])): ?>
        <section class="dash-panel">
            <h2 class="wf-section-title">Breakdown</h2>
            <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr><th>Period</th><th>Orders</th><th class="text-right">Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report['rows'] as $row): ?>
                            <tr>
                                <td><?= h((string) ($row['label'] ?? '')) ?></td>
                                <td><?= (int) ($row['ord_count'] ?? 0) ?></td>
                                <td class="text-right">$<?= number_format((float) ($row['amt'] ?? 0), 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>

        <section class="dash-panel dash-panel--wide">
            <h2 class="wf-section-title">Top products</h2>
            <?php if (!$top): ?>
                <p class="dash-empty" style="padding:20px 0;">No product sales in this period.</p>
            <?php else: ?>
                <div class="table-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Units sold</th>
                                <th class="text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top as $row): ?>
                                <tr>
                                    <td><strong><?= h((string) ($row['product_name'] ?? '')) ?></strong></td>
                                    <td><?= (int) ($row['order_count'] ?? 0) ?></td>
                                    <td class="text-right">$<?= number_format((float) ($row['revenue'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
