<?php

declare(strict_types=1);

$me = $me ?? auth_user();
$isTraderPage = ($traderLayout ?? false) && $me && ($me['trader_id'] ?? '') !== '';

?>
<footer class="site-footer<?= $isTraderPage ? ' site-footer--trader' : '' ?>">
    <?php if ($isTraderPage): ?>
        <div class="container footer-trader-simple">
            <p class="footer-text">&copy; <?= date('Y') ?> GroceryGo &middot; Trader portal</p>
        </div>
    <?php else: ?>
    <div class="container footer-grid">
        <div class="footer-brand-block">
            <a href="<?= h(app_url()) ?>" class="footer-brand" aria-label="GroceryGo home">
                <img src="<?= h(app_url('assets/logo/GroceryGo-main.png')) ?>" alt="GroceryGo" class="footer-logo">
            </a>
            <p class="footer-text">
                Your neighborhood marketplace for fresh groceries, local traders, and easy community pickup.
            </p>
        </div>

        <div>
            <h3>Traders</h3>
            <ul class="footer-links">
                <li><a href="<?= h(portal_url('register.php')) ?>">Become a trader</a></li>
                <li><a href="<?= h(portal_url('login.php')) ?>">Trader login</a></li>
            </ul>
        </div>

        <div>
            <h3>Contact</h3>
            <ul class="footer-links">
                <li><a href="mailto:support.aim@tbc.edu.np">support.aim@tbc.edu.np</a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> GroceryGo. All rights reserved.</p>
    </div>
    <?php endif; ?>
</footer>
