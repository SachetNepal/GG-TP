<header class="site-header">
    <input type="checkbox" id="site-nav-toggle" class="site-nav-checkbox">
    <div class="container navbar-wrap">
        <a href="{{ route('home') }}" class="brand" aria-label="GroceryGo home">
            <img src="{{ asset('assets/logo/GroceryGo-main.png') }}" alt="GroceryGo logo" class="brand-logo">
        </a>

        <label for="site-nav-toggle" class="menu-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <nav class="main-nav" aria-label="Primary">
            <div class="nav-primary">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('shops.index') }}">Shops</a>
                <a href="{{ route('categories') }}">Categories</a>
                <a href="{{ route('about') }}">About Us</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('cart') }}" class="nav-baskets">Baskets</a>
                @auth
                    <a href="{{ route('profile.index') }}" class="nav-baskets">Profile</a>
                    <a href="{{ route('orders.index') }}" class="nav-baskets">Orders</a>
                    <form method="post" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-signup nav-login">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-signup nav-login">Login</a>
                @endauth
            </div>
        </nav>
    </div>
</header>
