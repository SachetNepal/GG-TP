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

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (! portal_verify_csrf()) {
        flash_set('error', 'Session expired. Please try again.');
        portal_redirect('/trader/product-reviews.php?id=' . rawurlencode($productId));
    }

    $reviewId = trim((string) ($_POST['review_id'] ?? ''));
    $reply = trim((string) ($_POST['trader_reply'] ?? ''));

    if ($reviewId === '' || $reply === '') {
        flash_set('error', 'Reply text is required.');
        portal_redirect('/trader/product-reviews.php?id=' . rawurlencode($productId));
    }

    if (strlen($reply) > 1000) {
        flash_set('error', 'Reply must be 1000 characters or fewer.');
        portal_redirect('/trader/product-reviews.php?id=' . rawurlencode($productId));
    }

    $owned = db_fetch_one(
        'SELECT r.review_id FROM review r
         INNER JOIN product p ON p.product_id = r.product_id
         WHERE r.review_id = :rid AND p.product_id = :pid AND p.shop_id = :sid',
        ['rid' => $reviewId, 'pid' => $productId, 'sid' => $shopId]
    );

    if (! $owned) {
        flash_set('error', 'Review not found.');
        portal_redirect('/trader/product-reviews.php?id=' . rawurlencode($productId));
    }

    try {
        db_execute(
            'UPDATE review SET trader_reply = :reply, trader_reply_date = SYSDATE WHERE review_id = :rid',
            ['reply' => $reply, 'rid' => $reviewId]
        );
        db_commit();
        flash_set('success', 'Your reply has been posted.');
    } catch (Throwable $e) {
        db_rollback();
        error_log('product-reviews reply: ' . $e->getMessage());
        flash_set('error', 'Could not save reply. Run php scripts/apply-oracle-updates.php if columns are missing.');
    }

    portal_redirect('/trader/product-reviews.php?id=' . rawurlencode($productId));
}

$reviews = [];
$commentsByReview = [];
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
                r.trader_reply, r.trader_reply_date,
                u.first_name, u.last_name
         FROM review r
         INNER JOIN users u ON u.user_id = r.customer_id
         WHERE r.product_id = :pid
         ORDER BY r.review_date DESC',
        ['pid' => $productId]
    );

    $commentRows = db_fetch_all(
        'SELECT c.comment_id, c.review_id, c.comment_body, c.comment_date,
                u.first_name, u.last_name
         FROM review_comment c
         INNER JOIN users u ON u.user_id = c.customer_id
         WHERE c.review_id IN (
             SELECT review_id FROM review WHERE product_id = :pid
         )
         ORDER BY c.comment_date ASC',
        ['pid' => $productId]
    );

    foreach ($commentRows as $row) {
        $rid = (string) ($row['review_id'] ?? '');
        $commentsByReview[$rid][] = $row;
    }
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
                    $reviewId = (string) ($review['review_id'] ?? '');
                    $name = trim((string) (($review['first_name'] ?? '') . ' ' . ($review['last_name'] ?? '')));
                    $rating = (int) ($review['rating'] ?? 0);
                    $dateRaw = $review['review_date'] ?? '';
                    $dateLabel = $dateRaw !== '' ? date('j M Y', strtotime((string) $dateRaw)) : '';
                    $traderReply = trim((string) ($review['trader_reply'] ?? ''));
                    $replyDateRaw = $review['trader_reply_date'] ?? '';
                    $replyDateLabel = $replyDateRaw !== '' ? date('j M Y', strtotime((string) $replyDateRaw)) : '';
                    $comments = $commentsByReview[$reviewId] ?? [];
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

                        <?php if ($comments): ?>
                            <ul class="trader-review-comments">
                                <?php foreach ($comments as $comment): ?>
                                    <?php
                                    $cName = trim((string) (($comment['first_name'] ?? '') . ' ' . ($comment['last_name'] ?? '')));
                                    $cDate = ($comment['comment_date'] ?? '') !== ''
                                        ? date('j M Y', strtotime((string) $comment['comment_date']))
                                        : '';
                                    ?>
                                    <li>
                                        <strong><?= h($cName !== '' ? $cName : 'Customer') ?></strong>
                                        <span class="muted small"> commented</span>
                                        <p><?= nl2br(h((string) ($comment['comment_body'] ?? ''))) ?></p>
                                        <?php if ($cDate !== ''): ?>
                                            <time class="muted small"><?= h($cDate) ?></time>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if ($traderReply !== ''): ?>
                            <div class="trader-review-shop-reply">
                                <p class="trader-review-reply-label">Your reply</p>
                                <p><?= nl2br(h($traderReply)) ?></p>
                                <?php if ($replyDateLabel !== ''): ?>
                                    <time class="muted small"><?= h($replyDateLabel) ?></time>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="trader-review-reply-form">
                            <?= portal_csrf_field() ?>
                            <input type="hidden" name="review_id" value="<?= h($reviewId) ?>">
                            <label for="reply-<?= h($reviewId) ?>"><?= $traderReply !== '' ? 'Update your reply' : 'Reply to this review' ?></label>
                            <textarea id="reply-<?= h($reviewId) ?>" name="trader_reply" rows="3" maxlength="1000" required placeholder="Thank the customer or address their feedback…"><?= h($traderReply) ?></textarea>
                            <button type="submit" class="btn btn-primary btn-sm"><?= $traderReply !== '' ? 'Update reply' : 'Post reply' ?></button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
