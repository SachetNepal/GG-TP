<?php

declare(strict_types=1);

$pageTitle = 'Add product — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Add product</h1>
  <hr class="wf-page-rule">

  <div class="wf-form-panel">
    <form method="get" action="products.php">
      <div class="form-grid cols-2">
        <div>
          <label for="product_id">Product ID</label>
          <input type="text" id="product_id" name="product_id" class="input" value="PRD-007" readonly>
        </div>
        <div>
          <label for="category">Category <span class="muted">*</span></label>
          <select id="category" name="category" class="input" required>
            <option value="meat" selected>Meat</option>
            <option value="produce">Produce</option>
            <option value="bakery">Bakery</option>
            <option value="dairy">Dairy</option>
          </select>
        </div>
      </div>

      <label for="name">Product name <span class="muted">*</span></label>
      <input type="text" id="name" name="name" class="input" placeholder="e.g. Fish" required>

      <label for="description">Description <span class="muted">*</span></label>
      <textarea id="description" name="description" class="input" placeholder="Detailed product description …" required></textarea>

      <div class="form-grid cols-3">
        <div>
          <label for="price">Price <span class="muted">*</span></label>
          <input type="number" id="price" name="price" class="input" placeholder="0.00" step="0.01" min="0" required>
        </div>
        <div>
          <label for="stock">Stock available <span class="muted">*</span></label>
          <input type="text" id="stock" name="stock" class="input" placeholder="0" required>
        </div>
        <div>
          <label for="quantity">Quantity <span class="muted">*</span></label>
          <input type="text" id="quantity" name="quantity" class="input" placeholder="Per item" required>
        </div>
      </div>

      <div class="form-grid cols-2">
        <div>
          <label for="min_order">Min order</label>
          <input type="number" id="min_order" name="min_order" class="input" placeholder="0" step="0.01" min="0">
        </div>
        <div>
          <label for="max_order">Max order</label>
          <input type="number" id="max_order" name="max_order" class="input" placeholder="0" min="0">
        </div>
      </div>

      <div class="form-grid cols-3">
        <div>
          <label for="discount">Discount %</label>
          <input type="number" id="discount" name="discount" class="input" placeholder="0.00" step="0.01" min="0" max="100">
        </div>
        <div>
          <label for="end_date">End date</label>
          <input type="date" id="end_date" name="end_date" class="input">
        </div>
        <div>
          <label for="start_date">Start date</label>
          <input type="date" id="start_date" name="start_date" class="input">
        </div>
      </div>

      <div class="wf-form-actions">
        <a class="btn btn-outline" href="products.php">Cancel</a>
        <button type="submit" class="btn btn-primary">Save product</button>
      </div>
    </form>
  </div>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
