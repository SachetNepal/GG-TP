<?php

declare(strict_types=1);

$pageTitle = 'Orders — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Orders</h1>
  <hr class="wf-page-rule">

  <section class="panel">
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Order</th><th>Customer</th><th>Amount</th><th>Status</th><th>Slot</th><th></th></tr></thead>
        <tbody>
          <tr><td>#1042</td><td>Jamie Chen</td><td>Rs 3,420</td><td><span class="pill">Ready</span></td><td>Today 14:30</td><td><a class="btn btn-outline" style="padding:6px 12px;font-size:13px;" href="order-detail.php">View</a></td></tr>
          <tr><td>#1043</td><td>Sam Rivera</td><td>Rs 1,890</td><td><span class="pill">Picking</span></td><td>Today 16:00</td><td><a class="btn btn-outline" style="padding:6px 12px;font-size:13px;" href="order-detail.php">View</a></td></tr>
          <tr><td>#1040</td><td>Alex Morgan</td><td>Rs 5,210</td><td><span class="pill">Collected</span></td><td>Yesterday</td><td><a class="btn btn-outline" style="padding:6px 12px;font-size:13px;" href="order-detail.php">View</a></td></tr>
        </tbody>
      </table>
    </div>
  </section>
  <p class="muted small"><a href="index.php">Preview hub</a></p>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
