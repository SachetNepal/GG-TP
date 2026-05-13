<?php

declare(strict_types=1);

$pageTitle = 'Order #1042 — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Order detail</h1>
  <hr class="wf-page-rule">

  <p class="muted"><a href="orders.php">← Back to orders</a></p>

  <section class="panel">
    <div class="welcome-panel-row">
      <div>
        <p class="muted">Order</p>
        <p class="welcome-strong">#1042</p>
      </div>
      <div class="text-right">
        <p class="muted">Status</p>
        <p class="welcome-strong"><span class="badge">Ready</span></p>
      </div>
    </div>
    <p class="muted" style="margin-top:16px;">Customer: <strong>Jamie Chen</strong> · Collection: <strong>Today 14:30</strong></p>
  </section>
  <section class="panel">
    <h2 class="panel-title">Line items</h2>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>
        <tbody>
          <tr><td>Organic tomatoes (500g)</td><td>2</td><td>Rs 480</td></tr>
          <tr><td>Sourdough loaf</td><td>1</td><td>Rs 380</td></tr>
          <tr><td>Olive oil (500ml)</td><td>1</td><td>Rs 850</td></tr>
        </tbody>
      </table>
    </div>
    <p class="muted" style="margin-top:16px;text-align:right;"><strong>Total: Rs 3,420</strong></p>
  </section>
  <p><a class="btn btn-primary" href="orders.php">Mark collected (demo)</a></p>
  <p class="muted small"><a href="index.php">Preview hub</a></p>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
