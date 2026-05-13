<?php

declare(strict_types=1);

$pageTitle = 'Edit trader profile — GroceryGo';

require __DIR__ . '/includes/layout-app-start.php';
?>
  <h1 class="wf-page-title">Edit details</h1>
  <hr class="wf-page-rule">

  <div class="wf-form-panel">
    <form method="get" action="profile.php" class="form-grid cols-2">
      <div>
        <label for="trader_name">Trader name</label>
        <input type="text" id="trader_name" name="trader_name" class="input" value="Bakery">
      </div>
      <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="input" value="bakery@gmail.com">
      </div>
      <div>
        <label for="shop_name">Shop name</label>
        <input type="text" id="shop_name" name="shop_name" class="input" value="Bakery">
      </div>
      <div>
        <label for="shop_address">Shop address</label>
        <input type="text" id="shop_address" name="shop_address" class="input" value="Downtown">
      </div>
      <div>
        <label for="phone">Phone number</label>
        <input type="tel" id="phone" name="phone" class="input" value="0000000000">
      </div>
      <div style="grid-column: 1 / -1;">
        <label for="hours">Opening hours</label>
        <input type="text" id="hours" name="hours" class="input" value="Mon–Sat: 8AM–6PM; Sun: Closed">
      </div>
      <div class="wf-form-actions" style="grid-column: 1 / -1;">
        <a class="btn btn-outline" href="profile.php">Cancel</a>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
  </div>
<?php
$chartJs = false;
$inlineScript = '';
require __DIR__ . '/includes/layout-app-end.php';
