<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$products = [];
$discounts = [];
$error = '';
$success = '';

if (trader_has_shop($me)) {
    try {
        $products = db_fetch_all(
            'SELECT product_id, product_name FROM product WHERE shop_id = :sid ORDER BY product_name',
            ['sid' => $shopId]
        );
        $discounts = db_fetch_all(
            "SELECT d.discount_id, d.rate, d.start_date, d.end_date,
                    pd.product_discount_id, p.product_id, p.product_name
             FROM product_discount pd
             INNER JOIN discount d ON d.discount_id = pd.discount_id
             INNER JOIN product p ON p.product_id = pd.product_id
             WHERE p.shop_id = :sid
             ORDER BY d.start_date DESC",
            ['sid' => $shopId]
        );
    } catch (Throwable $e) {
        $error = 'Could not load discounts.';
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && portal_verify_csrf()) {
    $action = (string) ($_POST['action'] ?? '');
    try {
        if ($action === 'add') {
            $productId = trim((string) ($_POST['product_id'] ?? ''));
            $rate = (float) ($_POST['rate'] ?? 0);
            $start = trim((string) ($_POST['start_date'] ?? ''));
            $end = trim((string) ($_POST['end_date'] ?? ''));
            if ($productId === '' || $rate <= 0 || $start === '' || $end === '') {
                throw new RuntimeException('Product, rate, and dates are required.');
            }
            $owned = (int) (db_fetch_scalar(
                'SELECT COUNT(*) FROM product WHERE product_id = :pid AND shop_id = :sid',
                ['pid' => $productId, 'sid' => $shopId]
            ) ?? 0);
            if ($owned < 1) {
                throw new RuntimeException('Invalid product.');
            }
            $did = db_next_prefixed_id('discount', 'discount_id', 'D');
            $pdid = db_next_prefixed_id('product_discount', 'product_discount_id', 'PD');
            db_execute(
                'INSERT INTO discount (discount_id, rate, start_date, end_date)
                 VALUES (:did, :rate, TO_DATE(:sd, \'YYYY-MM-DD\'), TO_DATE(:ed, \'YYYY-MM-DD\'))',
                ['did' => $did, 'rate' => $rate, 'sd' => $start, 'ed' => $end]
            );
            db_execute(
                'INSERT INTO product_discount (product_discount_id, product_id, discount_id)
                 VALUES (:pdid, :pid, :did)',
                ['pdid' => $pdid, 'pid' => $productId, 'did' => $did]
            );
            db_commit();
            $success = 'Discount added.';
        } elseif ($action === 'update') {
            $did = trim((string) ($_POST['discount_id'] ?? ''));
            $rate = (float) ($_POST['rate'] ?? 0);
            $start = trim((string) ($_POST['start_date'] ?? ''));
            $end = trim((string) ($_POST['end_date'] ?? ''));
            $linked = (int) (db_fetch_scalar(
                "SELECT COUNT(*) FROM product_discount pd
                 INNER JOIN product p ON p.product_id = pd.product_id
                 WHERE pd.discount_id = :did AND p.shop_id = :sid",
                ['did' => $did, 'sid' => $shopId]
            ) ?? 0);
            if ($linked < 1 || $rate <= 0) {
                throw new RuntimeException('Invalid discount.');
            }
            db_execute(
                'UPDATE discount SET rate = :rate,
                 start_date = TO_DATE(:sd, \'YYYY-MM-DD\'),
                 end_date = TO_DATE(:ed, \'YYYY-MM-DD\')
                 WHERE discount_id = :did',
                ['rate' => $rate, 'sd' => $start, 'ed' => $end, 'did' => $did]
            );
            db_commit();
            $success = 'Discount updated.';
        } elseif ($action === 'remove') {
            $pdid = trim((string) ($_POST['product_discount_id'] ?? ''));
            $row = db_fetch_one(
                "SELECT pd.discount_id FROM product_discount pd
                 INNER JOIN product p ON p.product_id = pd.product_id
                 WHERE pd.product_discount_id = :pdid AND p.shop_id = :sid",
                ['pdid' => $pdid, 'sid' => $shopId]
            );
            if (!$row) {
                throw new RuntimeException('Discount not found.');
            }
            $did = (string) $row['discount_id'];
            db_execute('DELETE FROM product_discount WHERE product_discount_id = :pdid', ['pdid' => $pdid]);
            db_execute('DELETE FROM discount WHERE discount_id = :did', ['did' => $did]);
            db_commit();
            $success = 'Discount removed.';
        }
        portal_redirect('/trader/discounts.php');
    } catch (Throwable $e) {
        db_rollback();
        $error = $e->getMessage();
    }
}

$pageTitle = 'Discounts';
$traderLayout = true;
$traderPageTitle = 'Discounts';
$traderPageEyebrow = 'Promotions';
$traderPageSubtitle = count($discounts) . ' active discount(s)';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if ($success !== ''): ?>
            <div class="alert alert-success"><?= h($success) ?></div>
        <?php endif; ?>

        <section class="dash-panel dash-form">
            <h2 class="wf-section-title">Add discount</h2>
            <form method="post" class="form-grid cols-2 dash-filters" style="margin-bottom:0;">
                <?= portal_csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <div>
                    <label for="product_id">Product</label>
                    <select class="input" id="product_id" name="product_id" required>
                        <option value="">Select…</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= h((string) ($p['product_id'] ?? '')) ?>"><?= h((string) ($p['product_name'] ?? '')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="rate">Rate (%)</label>
                    <input class="input" type="number" step="0.01" min="0.01" max="100" id="rate" name="rate" required>
                </div>
                <div>
                    <label for="start_date">Start date</label>
                    <input class="input" type="date" id="start_date" name="start_date" required>
                </div>
                <div>
                    <label for="end_date">End date</label>
                    <input class="input" type="date" id="end_date" name="end_date" required>
                </div>
                <div class="filter-actions" style="grid-column:1/-1;">
                    <button type="submit" class="btn btn-primary">Add discount</button>
                </div>
            </form>
        </section>

        <section class="dash-panel">
            <h2 class="wf-section-title">Active discounts</h2>
            <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Rate</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$discounts): ?>
                            <tr><td colspan="5" class="dash-empty">No discounts yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($discounts as $d): ?>
                                <tr>
                                    <td><strong><?= h((string) ($d['product_name'] ?? '')) ?></strong></td>
                                    <td><?= number_format((float) ($d['rate'] ?? 0), 1) ?>%</td>
                                    <td><?= h(substr((string) ($d['start_date'] ?? ''), 0, 10)) ?></td>
                                    <td><?= h(substr((string) ($d['end_date'] ?? ''), 0, 10)) ?></td>
                                    <td>
                                        <details class="dash-discount-edit">
                                            <summary class="btn btn-outline" style="display:inline-block;cursor:pointer;">Edit</summary>
                                            <div class="dash-discount-edit-body">
                                                <form method="post">
                                                    <?= portal_csrf_field() ?>
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="discount_id" value="<?= h((string) ($d['discount_id'] ?? '')) ?>">
                                                    <label>Rate %</label>
                                                    <input class="input" type="number" step="0.01" name="rate" value="<?= h((string) ($d['rate'] ?? '')) ?>" required>
                                                    <label>Start</label>
                                                    <input class="input" type="date" name="start_date" value="<?= h(substr((string) ($d['start_date'] ?? ''), 0, 10)) ?>" required>
                                                    <label>End</label>
                                                    <input class="input" type="date" name="end_date" value="<?= h(substr((string) ($d['end_date'] ?? ''), 0, 10)) ?>" required>
                                                    <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save</button>
                                                </form>
                                                <form method="post" onsubmit="return confirm('Remove this discount?');">
                                                    <?= portal_csrf_field() ?>
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_discount_id" value="<?= h((string) ($d['product_discount_id'] ?? '')) ?>">
                                                    <button type="submit" class="btn btn-outline btn-danger" style="margin-top:8px;">Remove</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
