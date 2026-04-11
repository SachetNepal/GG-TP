<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a href="{{ route('home') }}" class="footer-brand" aria-label="GroceryGo home">
                <img src="{{ asset('assets/logo/GroceryGo-main.png') }}" alt="GroceryGo logo" class="footer-logo">
                <span>GroceryGo</span>
            </a>
            <p class="footer-text">
                Your neighborhood marketplace for fresh groceries, local traders, and easy community pickup.
            </p>
        </div>

        <div>
            <h3>Useful Links</h3>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="#">Shop</a></li>
                <li><a href="#">Categories</a></li>
            </ul>
        </div>

        <div>
            <h3>Policies</h3>
            <ul class="footer-links">
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms and Conditions</a></li>
                <li><a href="#">About Us</a></li>
            </ul>
        </div>

        <div>
            <h3>Contact</h3>
            <ul class="footer-links">
                <li><a href="mailto:support@multitrader.com">support.aim@tbc.edu.np</a></li>
                <li><a href="tel:02012345678">9840******</a></li>
                <li><a href="#">Facebook</a> | <a href="#">Instagram</a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; {{ date('Y') }} GroceryGo. All rights reserved.</p>
    </div>
</footer>
