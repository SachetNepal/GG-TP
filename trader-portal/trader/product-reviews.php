<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);
$productId = trim((string) ($_GET['id'] ?? ''));

if ($productId === '' || ! trader_has_shop($me)) {
    flash_set('error', 'Invalid product.');
    portal_redirect('/trader/manage-products.php');
}

$product = db_fetch_one(
    'SELECT p.product_id, p.product_name, p.price
     FROM product p
     WHERE p.product_id = :pid AND p.shop_id = :sid',
    ['pid' => $productId, 'sid' => $shopId]
);

if (! $product) {
    flash_set('error', 'Product not found.');
    portal_redirect('/trader/manage-products.php');
}

$reviews = [];
$avgRating = 0.0;
$reviewCount = 0;

try {
    $stats = db_fetch_one(
        'SELECT AVG(r.rating) AS avg_rating, COUNT(*) AS review_count
         FROM review r
         WHERE r.product_id = :pid',
        ['pid' => $productId]
    );
    $avgRating = round((float) ($stats['avg_rating'] ?? 0), 1);
    $reviewCount = (int) ($stats['review_count'] ?? 0);

    $reviews = db_fetch_all(
        'SELECT r.review_id, r.rating, r.review_body, r.review_date,
                u.first_name, u.last_name
         FROM review r
         INNER JOIN users u ON u.user_id = r.customer_id
         WHERE r.product_id = :pid
         ORDER BY r.review_date DESC',
        ['pid' => $productId]
    );
} catch (Throwable $e) {
    error_log('product-reviews: ' . $e->getMessage());
}

$pageTitle = 'Product reviews';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <p class="muted" style="margin-bottom:12px;">
            <a href="<?= h(portal_url('trader/manage-products.php')) ?>">&larr; Back to products</a>
        </p>

        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
            <div>
                <h1 class="panel-title" style="margin:0 0 6px;"><?= h((string) ($product['product_name'] ?? 'Product')) ?></h1>
                <p class="muted">Product ID: <?= h($productId) ?> &middot; $<?= number_format((float) ($product['price'] ?? 0), 2) ?></p>
            </div>
            <?php if ($reviewCount > 0): ?>
                <?php $fullStars = (int) round($avgRating); ?>
                <div class="trader-rating-summary">
                    <span class="stars-gold" aria-hidden="true"><?= str_repeat('★', $fullStars) ?><?= str_repeat('☆', 5 - $fullStars) ?></span>
                    <span class="muted"><?= $avgRating ?> / 5 &middot; <?= $reviewCount ?> <?= $reviewCount === 1 ? 'review' : 'reviews' ?></span>
                </div>
            <?php endif; ?>
        </div>

        <?php if (! $reviews): ?>
            <p class="muted">No customer reviews yet for this product.</p>
        <?php else: ?>
            <div class="trader-reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <?php
                    $name = trim((string) (($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')));
                    $rating = (int) ($review['rating'] ?? 0);
                    $dateRaw = $review['review_date'] ?? '';
                    $dateLabel = $dateRaw !== '' ? date('j M Y', strtotime((string) $dateRaw)) : '';
                    ?>
                    <article class="card trader-review-card">
                        <div class="trader-review-head">
                            <strong><?= h($name !== '' ? $name : 'Customer') ?></strong>
                            <span class="stars-gold" aria-label="<?= $rating ?> out of 5"><?= str_repeat('★', $rating) ?><?= str_repeat('☆', 5 - $rating) ?></span>
                        </div>
                        <p><?= nl2br(h((string) ($review['review_body'] ?? ''))) ?></p>
                        <?php if ($dateLabel !== ''): ?>
                            <time class="muted small"><?= h($dateLabel) ?></time>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
