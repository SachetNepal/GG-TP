<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/dashboard-queries.php';

$me = require_trader();
$stats = trader_dashboard_data(trader_shop_id($me));

$pageTitle = 'Reports';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <h1 class="panel-title">Weekly reports</h1>
        <p class="muted" style="margin-bottom:20px;">Summary for your shop (same data as dashboard).</p>
        <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;">
            <article class="stat-card">
                <span class="muted">Revenue (week)</span>
                <strong>£<?= number_format((float) ($stats['revenue'] ?? 0), 2) ?></strong>
            </article>
            <article class="stat-card">
                <span class="muted">Orders (week)</span>
                <strong><?= (int) ($stats['orders'] ?? 0) ?></strong>
            </article>
            <article class="stat-card">
                <span class="muted">Active products</span>
                <strong><?= (int) ($stats['products'] ?? 0) ?></strong>
            </article>
            <article class="stat-card">
                <span class="muted">Collection slots</span>
                <strong><?= (int) ($stats['slots'] ?? 0) ?></strong>
            </article>
        </div>
        <?php if (!empty($stats['top_products'])): ?>
        <h2 style="margin-top:28px;font-size:1.1rem;">Top products</h2>
        <ul class="muted" style="margin-top:12px;">
            <?php foreach ($stats['top_products'] as $row): ?>
                <li><?= h((string) ($row['product_name'] ?? '')) ?> — <?= (int) ($row['order_count'] ?? 0) ?> sold</li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
