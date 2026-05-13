<?php

declare(strict_types=1);

$pageTitle = 'Reports — GroceryGo Trader';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">View report</h1>
  <hr class="wf-page-rule">

  <section class="wf-form-panel">
    <p class="muted">Placeholder for sales and order exports. Hook your Oracle or Laravel APIs here.</p>
    <div class="table-scroll" style="margin-top:20px;">
      <table class="data-table">
        <thead>
          <tr><th>Period</th><th>Orders</th><th>Revenue</th></tr>
        </thead>
        <tbody>
          <tr><td>This week</td><td>47</td><td>Rs 842,500</td></tr>
          <tr><td>Last week</td><td>52</td><td>Rs 901,200</td></tr>
        </tbody>
      </table>
    </div>
    <p style="margin-top:20px;"><a class="btn btn-outline" href="dashboard.php">← Back to dashboard</a></p>
  </section>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
