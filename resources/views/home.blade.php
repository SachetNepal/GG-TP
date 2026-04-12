@extends('layouts.app')

@section('title', 'GroceryGo - Home')

@section('content')
    <section class="hero-section">
        <div class="hero-overlay">
            <div class="container hero-content">
                <h1>Your Local Shops Online</h1>
                <p>Browse, combine, and collect your items from your favorite local markets with ease.</p>
                <form class="hero-search" action="#" method="get">
                    <input type="text" placeholder="Search products, stores, or categories">
                    <button type="submit" class="btn btn-search">Search</button>
                </form>
            </div>
        </div>
    </section>

    <section class="section" id="about">
        <div class="container">
            <div class="section-heading">
                <h2>Built for Community Shopping</h2>
                <p class="text-black">Everything you need for simple, modern local grocery shopping.</p>
            </div>
            <article class="card feature-panel">
                <div class="feature-grid">
                    <article class="feature-card">
                    <div class="icon-wrap">
                        <img src="/assets/icons/simple-shopping.png" alt="Simple shopping icon" class="feature-icon-image">
                    </div>
                    <h3>Simple Shopping</h3>
                    <p>Add items from multiple local stores to one cart and check out in one go.</p>
                    </article>
                    <article class="feature-card">
                    <div class="icon-wrap">
                        <img src="/assets/icons/local-traders.png" alt="Local traders icon" class="feature-icon-image">
                    </div>
                    <h3>Local Traders</h3>
                    <p>Support your neighborhood markets and small businesses.</p>
                    </article>
                    <article class="feature-card">
                    <div class="icon-wrap">
                        <img src="/assets/icons/easy-pickup.png" alt="Easy pickup icon" class="feature-icon-image">
                    </div>
                    <h3>Easy Pickup</h3>
                    <p>Schedule a convenient time to collect your order in a central location.</p>
                    </article>
                </div>
            </article>
        </div>
    </section>

    <section class="section section-light">
        <div class="container">
            <div class="section-heading">
                <h2>Fresh From Five Local Shops</h2>
            </div>
            <div class="trader-grid">
                <article class="card trader-card">
                    <div class="trader-icon">
                        <img src="/assets/icons/butcher.png" alt="Butcher icon" class="trader-icon-image">
                    </div>
                    <h3>Butcher</h3>
                </article>
                <article class="card trader-card">
                    <div class="trader-icon">
                        <img src="/assets/icons/bakery.png" alt="Bakery icon" class="trader-icon-image">
                    </div>
                    <h3>Bakery</h3>
                </article>
                <article class="card trader-card">
                    <div class="trader-icon">
                        <img src="/assets/icons/greengrocer.png" alt="Greengrocer icon" class="trader-icon-image">
                    </div>
                    <h3>Greengrocer</h3>
                </article>
                <article class="card trader-card">
                    <div class="trader-icon">
                        <img src="/assets/icons/fishmonger.png" alt="Fishmonger icon" class="trader-icon-image">
                    </div>
                    <h3>Fishmonger</h3>
                </article>
                <article class="card trader-card">
                    <div class="trader-icon">
                        <img src="/assets/icons/delicatessen.png" alt="Delicatessen icon" class="trader-icon-image">
                    </div>
                    <h3>Delicatessen</h3>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="how-it-works">
        <div class="container">
            <div class="section-heading">
                <h2>How It Works</h2>
                <p class="text-black">Shop local in a few simple steps.</p>
            </div>
            <div class="steps-grid">
                <article class="card step-card">
                    Browse stores and products
                </article>
                <article class="card step-card">
                    Add everything to one basket
                </article>
                <article class="card step-card">
                    Choose your pickup window
                </article>
                <article class="card step-card">
                    Collect and enjoy fresh groceries
                </article>
            </div>
        </div>
    </section>
@endsection
