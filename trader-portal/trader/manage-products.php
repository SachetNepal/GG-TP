<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/product-meta.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$search = trim((string) ($_GET['q'] ?? ''));

$binds = ['sid' => $shopId];
$where = 'p.shop_id = :sid';
if ($search !== '') {
    $where .= ' AND LOWER(p.product_name) LIKE :q';
    $binds['q'] = '%' . strtolower($search) . '%';
}

$products = [];
if (trader_has_shop($me)) {
    try {
        $products = db_fetch_all(
            "SELECT p.product_id, p.product_name, p.price, p.product_in_stock, p.description, p.category_id, c.cat_name AS category_name
             FROM product p
             LEFT JOIN category c ON c.category_id = p.category_id
             WHERE $where
             ORDER BY p.product_id DESC",
            $binds
        );
    } catch (Throwable $e) {
        $products = [];
    }
}

$pageTitle = 'Products';
$traderLayout = true;
$traderPageTitle = 'Products';
$traderPageEyebrow = 'Manage shop';
$traderPageSubtitle = count($products) . ' product(s) in your catalogue';
$traderPageActionsHtml = '<a class="btn btn-primary" href="' . h(portal_url('trader/add-product.php')) . '">Add product</a>';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

        <section class="dash-panel">
            <form method="get" class="dash-filters cols-2">
                <div>
                    <label for="q">Search products</label>
                    <input class="input" id="q" name="q" value="<?= h($search) ?>" placeholder="Product name…">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search !== ''): ?>
                        <a class="btn btn-outline" href="<?= h(portal_url('trader/manage-products.php')) ?>">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-scroll">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$products): ?>
                            <tr><td colspan="6" class="dash-empty">No products yet. Add your first product to get started.</td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                                <?php
                                $pid = (string) ($p['product_id'] ?? '');
                                $status = product_status_label((string) ($p['description'] ?? ''), (int) ($p['product_in_stock'] ?? 0));
                                $pillClass = trader_status_pill_class($status);
                                ?>
                                <tr>
                                    <td><strong><?= h((string) ($p['product_name'] ?? '')) ?></strong></td>
                                    <td>$<?= number_format((float) ($p['price'] ?? 0), 2) ?></td>
                                    <td><?= (int) ($p['product_in_stock'] ?? 0) ?></td>
                                    <td><span class="status-pill <?= h($pillClass) ?>"><?= h(ucfirst($status)) ?></span></td>
                                    <td><?= h((string) ($p['category_name'] ?? '')) ?></td>
                                    <td>
                                        <div class="dash-actions-cell">
                                            <?php if ($status === 'active'): ?>
                                                <button type="button" class="btn btn-outline btn-toggle-product"
                                                        data-product-id="<?= h($pid) ?>" data-action="deactivate"
                                                        data-toggle-url="<?= h(portal_url('api/toggle-product.php')) ?>">Deactivate</button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-outline btn-toggle-product"
                                                        data-product-id="<?= h($pid) ?>" data-action="activate"
                                                        data-toggle-url="<?= h(portal_url('api/toggle-product.php')) ?>">Activate</button>
                                            <?php endif; ?>
                                            <a class="btn btn-outline" href="<?= h(portal_url('trader/edit-product.php?id=' . rawurlencode($pid))) ?>">Edit</a>
                                            <a class="btn btn-outline" href="<?= h(portal_url('trader/product-reviews.php?id=' . rawurlencode($pid))) ?>">Reviews</a>
                                            <button type="button" class="btn btn-outline btn-danger btn-delete-product"
                                                    data-product-id="<?= h($pid) ?>"
                                                    data-delete-url="<?= h(portal_url('api/delete-product.php')) ?>">Delete</button>
                                        </div>
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
