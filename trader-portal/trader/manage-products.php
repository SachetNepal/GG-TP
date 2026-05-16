<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

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
            "SELECT p.product_id, p.product_name, p.price, p.product_in_stock, p.category_id, c.cat_name AS category_name
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

$pageTitle = 'Manage products';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <section class="panel">
        <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h1 class="panel-title" style="margin:0;">Products</h1>
            <a class="btn btn-primary" href="<?= h(portal_url('trader/add-product.php')) ?>">Add product</a>
        </div>
        <form method="get" class="form-grid cols-2" style="margin-bottom:16px;max-width:520px;">
            <div>
                <label for="q">Search</label>
                <input class="input" id="q" name="q" value="<?= h($search) ?>" placeholder="Name…">
            </div>
            <div style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-outline">Search</button>
            </div>
        </form>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Category</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$products): ?>
                        <tr><td colspan="5" class="muted">No products yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= h((string) ($p['product_name'] ?? '')) ?></td>
                                <td>£<?= number_format((float) ($p['price'] ?? 0), 2) ?></td>
                                <td><?= (int) ($p['product_in_stock'] ?? 0) ?></td>
                                <td><?= h((string) ($p['category_name'] ?? '')) ?></td>
                                <td style="white-space:nowrap;">
                                    <a class="btn btn-outline" style="padding:8px 12px;font-size:14px;" href="<?= h(portal_url('trader/edit-product.php?id=' . (int) ($p['product_id'] ?? 0))) ?>">Edit</a>
                                    <button type="button"
                                            class="btn btn-outline btn-delete-product"
                                            style="padding:8px 12px;font-size:14px;color:#991b1b;border-color:#fecaca;"
                                            data-product-id="<?= (int) ($p['product_id'] ?? 0) ?>"
                                            data-delete-url="<?= h(portal_url('api/delete-product.php')) ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
