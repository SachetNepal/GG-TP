<?php

declare(strict_types=1);

?>
<header class="site-header">
    <input type="checkbox" id="site-nav-toggle" class="site-nav-checkbox">
    <div class="container navbar-wrap">
        <a href="/" class="brand" aria-label="GroceryGo home">
            <img src="/assets/logo/GroceryGo-main.png" alt="GroceryGo logo" class="brand-logo">
        </a>

        <label for="site-nav-toggle" class="menu-toggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </label>

        <nav class="main-nav" aria-label="Primary">
            <div class="nav-primary">
                <a href="/">Home</a>
                <a href="/shops">Shops</a>
                <a href="/categories">Categories</a>
                <a href="/about">About Us</a>
            </div>
            <div class="nav-actions">
                <a href="/cart" class="nav-baskets">Baskets</a>
                <a href="/login" class="btn btn-signup nav-login">Login</a>
            </div>
        </nav>
    </div>
</header>
