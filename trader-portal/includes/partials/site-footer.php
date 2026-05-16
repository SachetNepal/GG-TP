<?php

declare(strict_types=1);

?>
<footer class="site-footer">
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
                <li><a href="<?= h(app_url('shops')) ?>">Browse local shops</a></li>
                <li><a href="<?= h(portal_url('register.php')) ?>">Become a trader</a></li>
                <li><a href="<?= h(app_url('categories')) ?>">Shop categories</a></li>
            </ul>
        </div>

        <div>
            <h3>Privacy</h3>
            <ul class="footer-links">
                <li><a href="<?= h(app_url('about')) ?>">Privacy Policy</a></li>
                <li><a href="<?= h(app_url('about')) ?>">Terms and Conditions</a></li>
                <li><a href="<?= h(app_url('about')) ?>">Cookie notice</a></li>
            </ul>
        </div>

        <div>
            <h3>Know More</h3>
            <ul class="footer-links">
                <li><a href="<?= h(app_url('about')) ?>">About Us</a></li>
                <li><a href="<?= h(app_url()) ?>#how-it-works">How it works</a></li>
                <li><a href="<?= h(app_url('contact')) ?>">Help &amp; support</a></li>
            </ul>
        </div>

        <div>
            <h3>Contact Us</h3>
            <ul class="footer-links">
                <li><a href="mailto:support.aim@tbc.edu.np">support.aim@tbc.edu.np</a></li>
                <li><a href="tel:9840000000">9840******</a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2026 GroceryGo. All rights reserved.</p>
    </div>
</footer>
