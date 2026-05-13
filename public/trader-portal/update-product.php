<?php

declare(strict_types=1);

$pageTitle = 'Update product — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Update product</h1>
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
      <input type="text" id="name" name="name" class="input" value="Fish" required>

      <label for="description">Description <span class="muted">*</span></label>
      <textarea id="description" name="description" class="input" required>Fish is full of protein.</textarea>

      <div class="form-grid cols-3">
        <div>
          <label for="price">Price <span class="muted">*</span></label>
          <div class="wf-input-prefix">
            <span>Rs</span>
            <input type="number" id="price" name="price" class="input" value="1000" step="0.01" min="0" required>
          </div>
        </div>
        <div>
          <label for="stock">Stock available <span class="muted">*</span></label>
          <input type="text" id="stock" name="stock" class="input" value="10 KG" required>
        </div>
        <div>
          <label for="quantity">Quantity <span class="muted">*</span></label>
          <input type="number" id="quantity" name="quantity" class="input" value="4" min="0" required>
        </div>
      </div>

      <div class="form-grid cols-2">
        <div>
          <label for="min_order">Min order</label>
          <input type="number" id="min_order" name="min_order" class="input" value="1" min="0">
        </div>
        <div>
          <label for="max_order">Max order</label>
          <input type="number" id="max_order" name="max_order" class="input" value="5" min="0">
        </div>
      </div>

      <div class="form-grid cols-3">
        <div>
          <label for="discount">Discount %</label>
          <input type="number" id="discount" name="discount" class="input" value="0" step="0.01" min="0" max="100">
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
        <button type="submit" class="btn btn-primary">Update product</button>
      </div>
    </form>
  </div>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
