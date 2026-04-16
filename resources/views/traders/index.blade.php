@extends('layouts.app')

@section('title', 'GroceryGo - Local Traders')

@section('content')
    {{-- Hero / banner --}}
    <section class="traders-hero">
        <div class="traders-hero-overlay">
            <div class="container traders-hero-content">
                <h1>Local Traders</h1>
                <p class="traders-hero-subtitle">Since 1950</p>

                <form class="hero-search traders-search" action="/shops" method="get">
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

            @php
                $traders = [
                    ['name' => 'Butcher', 'tagline' => 'Quality meats cut fresh daily.'],
                    ['name' => 'Greengrocer', 'tagline' => 'Seasonal fruits and vegetables.'],
                    ['name' => 'Fishmonger', 'tagline' => 'Fresh fish sourced responsibly.'],
                    ['name' => 'Bakery', 'tagline' => 'Baked every morning.'],
                    ['name' => 'Delicatessen', 'tagline' => 'Specialty groceries and oils.'],
                ];
            @endphp

            <div class="traders-cards-grid">
                @foreach($traders as $trader)
                    <article class="card trader-list-card">
                        <div class="trader-list-image" aria-hidden="true">
                            <span>Image</span>
                        </div>
                        <h3>{{ $trader['name'] }}</h3>
                        <p>{{ $trader['tagline'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection

