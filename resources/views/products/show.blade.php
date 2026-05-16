@extends('layouts.app')

@section('title', 'GroceryGo - ' . ($product->product_name ?? 'Product'))

@section('content')
    @include('partials.page-hero', ['title' => 'Product Details'])

    <section class="section">
        <div class="container product-details-layout">
            <article class="card product-details-media">
                <div class="product-image-large" aria-hidden="true">
                    <span>Product Image</span>
                </div>
            </article>

            <article class="product-details-panel">
                @php
                    $stock = (int) ($product->product_in_stock ?? 0);
                    $stockLabel = $stock <= 0 ? 'Out of Stock' : ($stock <= 5 ? 'Low Stock' : 'In Stock');
                    $stockVariant = $stock <= 0 ? 'out' : ($stock <= 5 ? 'low' : 'in');
                @endphp

                <div class="product-shop">
                    <span class="product-meta-pill">Trader: {{ $product->shop->shop_name ?? 'Shop' }}</span>
                </div>

                <h2 class="product-details-name">{{ $product->product_name }}</h2>

                <div class="product-details-price-row">
                    <div class="product-price-large">£{{ number_format((float) $product->price, 2) }}</div>
                    @include('partials.status-badge', ['label' => $stockLabel, 'variant' => $stockVariant])
                </div>

                <div class="product-details-description">
                    <h3>Description</h3>
                    <p class="text-secondary">{{ $product->description }}</p>
                </div>

                @if (session('status'))
                    <p class="ok" style="margin-bottom:12px;">{{ session('status') }}</p>
                @endif

                @auth
                    <form method="post" action="{{ route('cart.add') }}" class="product-qty-add">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <div class="qty-field">
                            <label for="quantity">Quantity</label>
                            <input id="quantity" class="qty-input" type="number" name="quantity" value="1" min="1" max="20">
                        </div>
                        <button type="submit" class="btn btn-primary product-add-btn">Add to Basket</button>
                    </form>
                @else
                    <p><a href="{{ route('login') }}">Sign in</a> to add to basket.</p>
                @endauth

                @if ($product->reviews && $product->reviews->isNotEmpty())
                <section class="reviews-section">
                    <h3>Reviews</h3>
                    <div class="reviews-grid">
                        @foreach ($product->reviews as $review)
                            <article class="card review-card">
                                <div class="review-head">
                                    <strong>{{ $review->customer->user->first_name ?? 'Customer' }}</strong>
                                    <span class="review-stars">{{ str_repeat('★', (int) $review->rating) }}</span>
                                </div>
                                <p class="text-secondary">{{ $review->review_text }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
                @endif
            </article>
        </div>
    </section>
@endsection
