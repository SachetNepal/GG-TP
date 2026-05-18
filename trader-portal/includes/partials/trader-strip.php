<?php

declare(strict_types=1);

?>
<div class="trader-strip">
    <div class="container trader-strip-inner">
        <span class="trader-strip-label">Trader</span>
        <a href="<?= h(portal_url('trader/dashboard.php')) ?>">Dashboard</a>
        <a href="<?= h(portal_url('trader/manage-products.php')) ?>">Products</a>
        <a href="<?= h(portal_url('trader/add-product.php')) ?>">Add product</a>
        <a href="<?= h(portal_url('trader/discounts.php')) ?>">Discounts</a>
        <a href="<?= h(portal_url('trader/orders.php')) ?>">Orders</a>
        <a href="<?= h(portal_url('trader/reports.php')) ?>">Reports</a>
        <a href="<?= h(portal_url('trader/profile.php')) ?>">Profile</a>
        <a href="<?= h(portal_url('trader/settings.php')) ?>">Settings</a>
        <a href="<?= h(portal_url('logout.php')) ?>">Logout</a>
    </div>
</div>
