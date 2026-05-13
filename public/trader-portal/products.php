<?php

declare(strict_types=1);

$pageTitle = 'Products — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Products</h1>
  <hr class="wf-page-rule">

  <div class="form-actions-top">
    <p class="muted" style="margin:0;flex:1;">Manage your catalogue.</p>
    <a class="btn btn-primary" href="add-product.php">Add product</a>
  </div>

  <section class="panel" style="margin-top:16px;">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Name</th><th>SKU</th><th>Price</th><th>Stock</th><th></th></tr></thead>
        <tbody>
          <tr><td>Organic tomatoes (500g)</td><td>TOM-500</td><td>Rs 240</td><td>48</td><td><a class="btn btn-outline" style="padding:8px 12px;font-size:14px;" href="update-product.php">Edit</a></td></tr>
          <tr><td>Sourdough loaf</td><td>BRD-SOUR</td><td>Rs 380</td><td>6</td><td><a class="btn btn-outline" style="padding:8px 12px;font-size:14px;" href="update-product.php">Edit</a></td></tr>
          <tr><td>Free-range eggs (6)</td><td>EGG-6</td><td>Rs 299</td><td>120</td><td><a class="btn btn-outline" style="padding:8px 12px;font-size:14px;" href="update-product.php">Edit</a></td></tr>
        </tbody>
      </table>
    </div>
  </section>
  <p class="muted small"><a href="index.php">Preview hub</a></p>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
