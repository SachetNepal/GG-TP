<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/report-queries.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$period = strtolower(trim((string) ($_GET['period'] ?? 'weekly')));
if (! in_array($period, ['daily', 'weekly', 'monthly'], true)) {
    $period = 'weekly';
}

$dateParam = isset($_GET['date']) ? (string) $_GET['date'] : null;
$weekParam = isset($_GET['week']) ? (string) $_GET['week'] : null;
$monthParam = isset($_GET['month']) ? (string) $_GET['month'] : null;

$context = trader_report_build_context($period, $dateParam, $weekParam, $monthParam);
$date = (string) $context['date'];
$week = (string) $context['week'];
$month = (string) $context['month'];

$report = trader_report_period_data($shopId, $context);
$top = trader_report_top_products_data($shopId, $context);

$availableWeeks = trader_report_available_weeks($shopId);
$availableMonths = trader_report_available_months($shopId);
if (! in_array($week, $availableWeeks, true)) {
    array_unshift($availableWeeks, $week);
}
if (! in_array($month, $availableMonths, true)) {
    array_unshift($availableMonths, $month);
}

$labels = TRADER_REPORT_PERIOD_LABELS;
$today = (new DateTimeImmutable('today'))->format('Y-m-d');

$tabQuery = static function (string $p) use ($date, $week, $month): string {
    return trader_report_export_query([
        'period' => $p,
        'date' => $date,
        'week' => $week,
        'month' => $month,
    ]);
};

$exportBase = portal_url('trader/reports-export.php');
$exportQuery = [
    'date' => $date,
    'week' => $week,
    'month' => $month,
];

$pageTitle = 'Reports';
$traderLayout = true;
$traderPageTitle = 'Reports';
$traderPageEyebrow = 'Analytics';
$traderPageSubtitle = $labels[$period] . ' — ' . (string) $context['range_label'];
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <nav class="dash-tabs report-period-tabs" aria-label="Report period">
            <?php foreach (['daily', 'weekly', 'monthly'] as $p): ?>
                <a class="btn <?= $period === $p ? 'btn-primary' : 'btn-outline' ?>"
                   href="<?= h(portal_url('trader/reports.php' . $tabQuery($p))) ?>">
                    <?= h($labels[$p]) ?> report
                </a>
            <?php endforeach; ?>
        </nav>

        <section class="dash-panel report-range-panel">
            <h2 class="wf-section-title">Report range</h2>
            <p class="report-range-hint">Choose the day, week, or month for each report type. Your selection applies to the view and downloads below.</p>
            <form method="get" action="<?= h(portal_url('trader/reports.php')) ?>" class="report-range-form dash-form">
                <input type="hidden" name="period" value="<?= h($period) ?>">

                <div class="report-range-grid">
                    <label class="report-range-field <?= $period === 'daily' ? 'report-range-field--active' : '' ?>">
                        <span class="report-range-label">Day</span>
                        <input type="date" name="date" class="input" value="<?= h($date) ?>" max="<?= h($today) ?>">
                    </label>

                    <label class="report-range-field <?= $period === 'weekly' ? 'report-range-field--active' : '' ?>">
                        <span class="report-range-label">Week</span>
                        <select name="week" class="input">
                            <?php foreach ($availableWeeks as $w):
                                $wb = trader_report_week_bounds($w);
                                $weekLabel = $wb ? $wb['label'] : $w;
                                ?>
                                <option value="<?= h($w) ?>"<?= $week === $w ? ' selected' : '' ?>><?= h($weekLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="report-range-field <?= $period === 'monthly' ? 'report-range-field--active' : '' ?>">
                        <span class="report-range-label">Month</span>
                        <select name="month" class="input">
                            <?php foreach ($availableMonths as $m):
                                $mb = trader_report_month_bounds($m);
                                ?>
                                <option value="<?= h($m) ?>"<?= $month === $m ? ' selected' : '' ?>><?= h($mb['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="report-range-actions">
                    <button type="submit" class="btn btn-primary">Apply range</button>
                </div>
            </form>

            <div class="report-download-row">
                <p class="report-download-label">Download Excel</p>
                <div class="report-download-btns">
                    <a class="btn btn-outline btn-sm"
                       href="<?= h($exportBase . trader_report_export_query(array_merge($exportQuery, ['scope' => 'daily']))) ?>">
                        Daily
                    </a>
                    <a class="btn btn-outline btn-sm"
                       href="<?= h($exportBase . trader_report_export_query(array_merge($exportQuery, ['scope' => 'weekly']))) ?>">
                        Weekly
                    </a>
                    <a class="btn btn-outline btn-sm"
                       href="<?= h($exportBase . trader_report_export_query(array_merge($exportQuery, ['scope' => 'monthly']))) ?>">
                        Monthly
                    </a>
                    <a class="btn btn-primary btn-sm"
                       href="<?= h($exportBase . trader_report_export_query(array_merge($exportQuery, ['scope' => 'all']))) ?>">
                        All three sheets
                    </a>
                </div>
            </div>
        </section>

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

        <?php if (! empty($report['rows'])): ?>
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
            <?php if (! $top): ?>
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
