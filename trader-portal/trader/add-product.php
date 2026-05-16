<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$me = require_trader();
$shopId = trader_shop_id($me);

$categories = [];
try {
    $categories = db_fetch_all(
        'SELECT category_id, cat_name FROM category ORDER BY cat_name'
    );
} catch (Throwable $e) {
    $categories = [];
}

$pageTitle = 'Add product';
$traderLayout = true;
require_once dirname(__DIR__) . '/includes/header.php';
?>
    <div class="form-actions-top">
        <button type="button" class="btn btn-outline" data-product-action="new">Add new product</button>
        <button type="button" class="btn btn-outline" data-product-action="draft">Save as draft</button>
        <button type="button" class="btn btn-accent" data-product-action="publish">Publish product</button>
    </div>

    <form id="productForm"
          action="<?= h(portal_url('api/add-product.php')) ?>"
          method="post"
          enctype="multipart/form-data"
          data-redirect-published="<?= h(portal_url('trader/manage-products.php')) ?>">

        <?= portal_csrf_field() ?>
        <input type="hidden" name="status" id="statusField" value="draft">
        <input type="hidden" name="tags" id="tagsField" value="">

        <section class="panel">
            <h2 class="panel-title">Product information</h2>
            <div class="form-grid cols-2">
                <div style="grid-column: 1 / -1;">
                    <label for="product_name">Product name</label>
                    <input class="input" type="text" id="product_name" name="product_name" required maxlength="200">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label for="description">Description</label>
                    <textarea class="input" id="description" name="description" required></textarea>
                </div>
                <div>
                    <label for="category_id">Category</label>
                    <select class="input" id="category_id" name="category_id" required>
                        <option value="">Select…</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= h((string) ($c['category_id'] ?? '')) ?>">
                                <?= h((string) ($c['cat_name'] ?? $c['category_name'] ?? '')) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="subcategory">Subcategory</label>
                    <input class="input" type="text" id="subcategory" name="subcategory" placeholder="Optional — stored in notes">
                </div>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel-title">Pricing &amp; stock</h2>
            <div class="form-grid cols-2">
                <div>
                    <label for="price">Price (£)</label>
                    <input class="input" type="number" step="0.01" min="0" id="price" name="price" required>
                </div>
                <div>
                    <label for="unit">Unit / weight</label>
                    <input class="input" type="text" id="unit" name="unit" placeholder="e.g. 500g">
                </div>
                <div>
                    <label for="stock">Stock available</label>
                    <input class="input" type="number" min="0" id="stock" name="stock" value="0">
                </div>
                <div>
                    <label for="max_per_order">Max per order</label>
                    <input class="input" type="number" min="1" id="max_per_order" name="max_per_order" value="10">
                </div>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel-title">Dietary &amp; tags</h2>
            <div class="pill-row">
                <?php
                $tags = ['Free Range', 'Grass Fed', 'Locally Sourced', 'Gluten Free', 'Halal', 'Special Offer'];
                foreach ($tags as $t):
                ?>
                    <button type="button" class="pill-btn" data-tag="<?= h($t) ?>"><?= h($t) ?></button>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="panel">
            <h2 class="panel-title">Product images</h2>
            <div class="dropzone" id="dropzone" tabindex="0">
                <p><strong>Drag &amp; drop</strong> images here or click to browse.</p>
                <p class="muted small">JPEG, PNG, WebP · max <?= (int) MAX_UPLOAD_MB ?>MB each</p>
            </div>
            <input type="file" name="images[]" id="imagesInput" multiple accept="image/*" class="sr-only">
            <div class="preview-grid" id="imagePreview"></div>
        </section>

        <section class="panel">
            <h2 class="panel-title">Status &amp; visibility</h2>
            <div class="form-grid cols-2">
                <div>
                    <label for="availability">Available for</label>
                    <select class="input" id="availability" name="availability">
                        <option value="both">Collection &amp; delivery</option>
                        <option value="collection_only">Collection only</option>
                        <option value="delivery">Delivery only</option>
                    </select>
                </div>
            </div>
            <div class="notice-box">
                Published products appear in your shop once listings are visible to customers (subject to moderation rules).
            </div>
        </section>
    </form>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
