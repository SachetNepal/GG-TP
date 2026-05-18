<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/product-images.php';

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

$fullDescription = (string) ($row['description'] ?? '');
$displayDescription = product_display_description($fullDescription);
$existingImages = product_image_filenames($fullDescription);

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
          enctype="multipart/form-data"
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
                <textarea class="input" id="description" name="description" required><?= h($displayDescription) ?></textarea>
            </div>
            <div>
                <label for="category_id">Category</label>
                <select class="input" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= h((string) ($c['category_id'] ?? '')) ?>"
                            <?= ((string) ($row['category_id'] ?? '') === (string) ($c['category_id'] ?? '')) ? 'selected' : '' ?>>
                            <?= h((string) ($c['cat_name'] ?? $c['category_name'] ?? '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="price">Price (USD $)</label>
                <input class="input" type="number" step="0.01" min="0.01" max="9999.99" name="price" id="price" required
                       value="<?= h((string) ($row['price'] ?? '')) ?>" placeholder="e.g. 5.50">
                <p class="muted small field-hint">Max $9,999.99</p>
            </div>
            <div>
                <label for="stock">Stock</label>
                <input class="input" type="number" min="0" max="9999" step="1" name="stock" id="stock"
                       value="<?= (int) ($row['product_in_stock'] ?? 0) ?>">
                <p class="muted small field-hint">0–9,999 units</p>
            </div>
        </div>

        <section class="dash-panel" style="margin-top:20px;padding:20px;">
            <h2 class="panel-title">Product images</h2>
            <?php if ($existingImages !== []): ?>
                <p class="muted small" style="margin-bottom:10px;">Current images — click × to remove when saving.</p>
                <div class="preview-grid" id="existingImages">
                    <?php foreach ($existingImages as $img): ?>
                        <figure class="preview-figure" data-existing-image="<?= h($img) ?>">
                            <img src="<?= h(product_image_public_url($shopId, $img)) ?>" alt="">
                            <button type="button" class="remove-img" data-remove-existing="<?= h($img) ?>" aria-label="Remove image">×</button>
                            <input type="hidden" name="keep_images[]" value="<?= h($img) ?>">
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="muted small" style="margin-bottom:10px;">No images yet. Add some below.</p>
            <?php endif; ?>

            <div class="dropzone" id="dropzone" tabindex="0" style="margin-top:16px;">
                <p><strong>Drag &amp; drop</strong> more images here or click to browse.</p>
                <p class="muted small">JPEG, PNG, WebP · max <?= (int) MAX_UPLOAD_MB ?>MB each</p>
            </div>
            <input type="file" name="images[]" id="imagesInput" multiple accept="image/*" class="sr-only">
            <div class="preview-grid" id="imagePreview"></div>
        </section>

        <div style="margin-top:20px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a class="btn btn-outline" href="<?= h(portal_url('trader/manage-products.php')) ?>">Cancel</a>
        </div>
    </form>
    </div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
