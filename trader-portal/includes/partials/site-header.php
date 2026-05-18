<?php

declare(strict_types=1);

$me = $me ?? auth_user();
$isTrader = $me && ($me['trader_id'] ?? '') !== '';
$brandUrl = $isTrader ? portal_url('trader/dashboard.php') : app_url();

$authNavActive = $authNavActive ?? basename($_SERVER['PHP_SELF'] ?? '');
$onRegisterPage = $authNavActive === 'register.php';
$loginNavClass = $onRegisterPage ? 'btn btn-outline nav-login' : 'btn btn-signup nav-login';
$registerNavClass = $onRegisterPage ? 'btn btn-signup nav-login' : 'btn btn-outline nav-login';

$traderNavPage = basename($_SERVER['PHP_SELF'] ?? '');
$traderNavActive = static function (string ...$pages) use ($traderNavPage): string {
    return in_array($traderNavPage, $pages, true) ? 'active' : '';
};
$traderNavCurrent = static function (string ...$pages) use ($traderNavPage): string {
    return in_array($traderNavPage, $pages, true) ? ' aria-current="page"' : '';
};

?>
<header class="site-header">
    <input type="checkbox" id="site-nav-toggle" class="site-nav-checkbox">
    <div class="container navbar-wrap">
        <a href="<?= h($brandUrl) ?>" class="brand" aria-label="<?= $isTrader ? 'Trader dashboard' : 'GroceryGo home' ?>">
            <img src="<?= h(app_url('assets/logo/GroceryGo-main.png')) ?>" alt="GroceryGo" class="brand-logo">
        </a>

        <label for="site-nav-toggle" class="menu-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <nav class="main-nav" aria-label="Primary">
            <?php if ($isTrader): ?>
                <div class="nav-primary nav-primary--trader">
                    <a href="<?= h(portal_url('trader/dashboard.php')) ?>" class="<?= h($traderNavActive('dashboard.php')) ?>"<?= $traderNavCurrent('dashboard.php') ?>>Dashboard</a>
                    <a href="<?= h(portal_url('trader/manage-products.php')) ?>" class="<?= h($traderNavActive('manage-products.php', 'add-product.php', 'edit-product.php', 'product-reviews.php')) ?>"<?= $traderNavCurrent('manage-products.php', 'add-product.php', 'edit-product.php', 'product-reviews.php') ?>>Products</a>
                    <a href="<?= h(portal_url('trader/discounts.php')) ?>" class="<?= h($traderNavActive('discounts.php')) ?>"<?= $traderNavCurrent('discounts.php') ?>>Discounts</a>
                    <a href="<?= h(portal_url('trader/orders.php')) ?>" class="<?= h($traderNavActive('orders.php', 'order-details.php')) ?>"<?= $traderNavCurrent('orders.php', 'order-details.php') ?>>Orders</a>
                    <a href="<?= h(portal_url('trader/reports.php')) ?>" class="<?= h($traderNavActive('reports.php')) ?>"<?= $traderNavCurrent('reports.php') ?>>Reports</a>
                    <a href="<?= h(portal_url('trader/profile.php')) ?>" class="<?= h($traderNavActive('profile.php', 'settings.php')) ?>"<?= $traderNavCurrent('profile.php', 'settings.php') ?>>Profile</a>
                </div>
                <div class="nav-actions">
                    <a href="<?= h(portal_url('logout.php')) ?>" class="btn btn-signup nav-login">Logout</a>
                </div>
            <?php else: ?>
                <div class="nav-actions nav-actions--auth">
                    <a href="<?= h(app_url()) ?>" class="nav-home-link">Home</a>
                    <a href="<?= h(portal_url('login.php')) ?>" class="<?= h($loginNavClass) ?>"<?= $onRegisterPage ? '' : ' aria-current="page"' ?>>Login</a>
                    <a href="<?= h(portal_url('register.php')) ?>" class="<?= h($registerNavClass) ?>"<?= $onRegisterPage ? ' aria-current="page"' : '' ?>>Register</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>
