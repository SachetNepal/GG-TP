<?php

declare(strict_types=1);

$pageTitle = 'Trader portal — GroceryGo';
require __DIR__ . '/includes/layout-auth-start.php';
?>
      <article class="card auth-card" style="max-width: 520px;">
        <header class="auth-header">
          <h1>Trader portal</h1>
          <p>Wireframe-aligned tools · served from <code style="font-size:13px;">public/trader-portal/</code></p>
        </header>
        <div class="notice-box" style="margin-bottom:18px;padding:14px;border-radius:12px;border:1px dashed rgba(31,122,77,0.35);background:rgba(31,122,77,0.06);font-size:14px;">
          Run <code>php artisan serve</code> then open<br><strong>http://127.0.0.1:8000/trader-portal/</strong>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
          <a class="btn btn-primary auth-submit" href="login.php">Trader login</a>
          <a class="btn auth-secondary-btn" href="register.php">Register</a>
          <a class="btn auth-secondary-btn" href="dashboard.php">Dashboard</a>
          <a class="btn auth-secondary-btn" href="add-product.php">Add product</a>
          <a class="btn auth-secondary-btn" href="update-product.php">Update product</a>
          <a class="btn auth-secondary-btn" href="profile.php">Trader profile</a>
          <a class="btn auth-secondary-btn" href="products.php">Products</a>
          <a class="btn auth-secondary-btn" href="orders.php">Orders</a>
          <a class="btn auth-secondary-btn" href="reports.php">View report</a>
        </div>
      </article>
<?php
require __DIR__ . '/includes/layout-auth-end.php';
