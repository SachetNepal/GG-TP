<?php

declare(strict_types=1);

$me = $me ?? auth_user();
$isTrader = $me && ($me['trader_id'] ?? '') !== '';

?>
<header class="site-header">
    <input type="checkbox" id="site-nav-toggle" class="site-nav-checkbox">
    <div class="container navbar-wrap">
        <a href="<?= h(app_url()) ?>" class="brand" aria-label="GroceryGo home">
            <img src="<?= h(app_url('assets/logo/GroceryGo-main.png')) ?>" alt="GroceryGo" class="brand-logo">
        </a>

        <label for="site-nav-toggle" class="menu-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <nav class="main-nav" aria-label="Primary">
            <div class="nav-primary">
                <a href="<?= h(app_url()) ?>">Home</a>
                <a href="<?= h(app_url('shops')) ?>">Shops</a>
                <a href="<?= h(app_url('categories')) ?>">Categories</a>
                <a href="<?= h(app_url('about')) ?>">About Us</a>
            </div>
            <div class="nav-actions">
                <?php if ($isTrader): ?>
                    <a href="<?= h(portal_url('trader/dashboard.php')) ?>" class="nav-baskets">Dashboard</a>
                    <a href="<?= h(portal_url('trader/manage-products.php')) ?>" class="nav-baskets">Products</a>
                    <a href="<?= h(portal_url('trader/orders.php')) ?>" class="nav-baskets">Orders</a>
                    <a href="<?= h(portal_url('trader/profile.php')) ?>" class="nav-baskets">Profile</a>
                    <a href="<?= h(portal_url('logout.php')) ?>" class="btn btn-signup nav-login">Logout</a>
                <?php else: ?>
                    <a href="<?= h(app_url('cart')) ?>" class="nav-baskets">Baskets</a>
                    <a href="<?= h(portal_url('login.php')) ?>" class="btn btn-signup nav-login">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
