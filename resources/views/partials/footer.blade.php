<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand-block">
            <a href="{{ route('home') }}" class="footer-brand" aria-label="GroceryGo home">
                <img src="{{ asset('assets/logo/GroceryGo-main.png') }}" alt="GroceryGo" class="footer-logo">
            </a>
            <p class="footer-text">
                Your neighborhood marketplace for fresh groceries, local traders, and easy community pickup.
            </p>
        </div>

        <div>
            <h3>Traders</h3>
            <ul class="footer-links">
                <li><a href="{{ route('shops.index') }}">Browse local shops</a></li>
                <li><a href="{{ url('trader-portal/register.php') }}">Become a trader</a></li>
                <li><a href="{{ route('categories') }}">Shop categories</a></li>
            </ul>
        </div>

        <div>
            <h3>Legal</h3>
            <ul class="footer-links">
                <li><a href="{{ route('legal.terms') }}">Terms and Conditions</a></li>
                <li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('legal.cookies') }}">Cookie Notice</a></li>
            </ul>
        </div>

        <div>
            <h3>Know More</h3>
            <ul class="footer-links">
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="{{ route('home') }}#how-it-works">How it works</a></li>
                <li><a href="{{ route('contact') }}">Help &amp; support</a></li>
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
        <p>&copy; GroceryGO</p>
        <p class="footer-legal-links">
            <a href="{{ route('legal.terms') }}">Terms and Conditions</a>
            <span aria-hidden="true">|</span>
            <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
            <span aria-hidden="true">|</span>
            <a href="{{ route('legal.cookies') }}">Cookie Notice</a>
        </p>
    </div>
</footer>
