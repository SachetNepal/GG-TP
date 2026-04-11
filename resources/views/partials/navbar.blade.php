<header class="site-header">
    <div class="container navbar-wrap">
        <a href="{{ route('home') }}" class="brand" aria-label="GroceryGo home">
            <img src="{{ asset('assets/logo/GroceryGo-main.png') }}" alt="GroceryGo logo" class="brand-logo">
        </a>

        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" data-menu-toggle>
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" data-main-nav>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="#about">About</a>
            <a href="#how-it-works">Shop Now</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            <a href="#" class="btn btn-signup">Sign Up</a>
        </nav>
    </div>
</header>
