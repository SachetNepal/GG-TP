<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);
$id = trim((string) ($_GET['id'] ?? ''));

if ($id === '' || $shopId === '') {
    flash_set('error', 'Invalid product.');
    portal_redirect('/trader/manage-products.php');
}

$row = db_fetch_one(
    'SELECT * FROM product WHERE product_id = :id AND shop_id = :sid',
    ['id' => $id, 'sid' => $shopId]
);

if (!$row) {
    flash_set('error', 'Product not found.');
    portal_redirect('/trader/manage-products.php');
}

$categories = db_fetch_all(
    'SELECT category_id, cat_name FROM category ORDER BY cat_name'
);

$pageTitle = 'Edit product';
$traderLayout = true;
$traderPageTitle = 'Edit product';
$traderPageEyebrow = 'Products';
$traderPageSubtitle = (string) ($row['product_name'] ?? '');
$traderPageActionsHtml = '<a class="btn btn-outline" href="' . h(portal_url('trader/manage-products.php')) . '">← Products</a>';
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="trader-dashboard">
        <?php require dirname(__DIR__) . '/includes/partials/trader-page-header.php'; ?>

    <form id="productForm"
          action="<?= h(portal_url('api/update-product.php')) ?>"
          method="post"
          class="dash-panel dash-form"
          data-redirect-published="<?= h(portal_url('trader/manage-products.php')) ?>">

        <?= portal_csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= h($id) ?>">

        <div class="form-grid cols-2">
            <div style="grid-column:1/-1;">
                <label for="product_name">Product name</label>
                <input class="input" id="product_name" name="product_name" required
                       value="<?= h((string) ($row['product_name'] ?? '')) ?>">
            </div>
            <div style="grid-column:1/-1;">
                <label for="description">Description</label>
                <textarea class="input" id="description" name="description" required><?= h((string) ($row['description'] ?? '')) ?></textarea>
            </div>
            <div>
                <label for="category_id">Category</label>
                <select class="input" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) ($c['category_id'] ?? 0) ?>"
                            <?= ((int) ($row['category_id'] ?? 0) === (int) ($c['category_id'] ?? 0)) ? 'selected' : '' ?>>
                            <?= h((string) ($c['cat_name'] ?? $c['category_name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="price">Price (USD $)</label>
                <input class="input" type="number" step="0.01" name="price" id="price" required
                       value="<?= h((string) ($row['price'] ?? '')) ?>">
            </div>
            <div>
                <label for="stock">Stock</label>
                <input class="input" type="number" min="0" name="stock" id="stock"
                       value="<?= (int) ($row['product_in_stock'] ?? 0) ?>">
            </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a class="btn btn-outline" href="<?= h(portal_url('trader/manage-products.php')) ?>">Cancel</a>
        </div>
    </form>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
