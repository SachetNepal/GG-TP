@extends('layouts.app')

@section('title', 'GroceryGo - Local Traders')

@section('content')
    {{-- Hero / banner --}}
    <section class="traders-hero">
        <div class="traders-hero-overlay">
            <div class="container traders-hero-content">
                <h1>Local Traders</h1>
                <p class="traders-hero-subtitle">Since 1950</p>

                <form class="hero-search traders-search" action="{{ route('categories') }}" method="get">
                    <input type="text" name="q" placeholder="Search traders or products">
                    <button type="submit" class="btn btn-search">Search</button>
                </form>
            </div>
        </div>
    </section>

    {{-- Feature cards --}}
    <section class="section">
        <div class="container">
            <div class="traders-feature-grid">
                <article class="card traders-feature-card">
                    <h3>Simple Shopping</h3>
                    <p>Browse and order from local shops in one place.</p>
                </article>
                <article class="card traders-feature-card">
                    <h3>Local Traders</h3>
                    <p>Support neighborhood businesses and fresh produce.</p>
                </article>
                <article class="card traders-feature-card">
                    <h3>Easy Picks</h3>
                    <p>Collect your complete order at your selected slot.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- Traders section --}}
    <section class="section section-light">
        <div class="container">
            <div class="section-heading">
                <h2>Our Traders</h2>
                <p class="text-black">Trusted local shops serving the community.</p>
            </div>

            <div class="traders-cards-grid">
                @forelse($traders as $shop)
                    <article class="card trader-list-card">
                        <div class="trader-list-image" aria-hidden="true">
                            <span>Shop</span>
                        </div>
                        <h3>{{ $shop->shop_name }}</h3>
                        <p>{{ $shop->location ?? 'Local trader' }}</p>
                        <a href="{{ route('categories', ['shop_id' => $shop->shop_id]) }}" class="btn btn-outline" style="margin-top:12px;">View products</a>
                    </article>
                @empty
                    <p class="muted">No shops listed yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection

