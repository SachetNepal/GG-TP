@extends('layouts.app')

@section('title', 'GroceryGo - Product Details')

@section('content')
    {{-- Page title --}}
    @include('partials.page-hero', ['title' => 'Product Details'])

    <section class="section">
        <div class="container product-details-layout">
            {{-- Left: product image --}}
            <article class="card product-details-media">
                <div class="product-image-large" aria-hidden="true">
                    <span>Product Image</span>
                </div>
            </article>

            {{-- Right: product details --}}
            <article class="product-details-panel">
                @php
                    $product = $product ?? [
                        'id' => 1,
                        'trader' => 'Greengrocer',
                        'name' => 'Fresh Tomatoes',
                        'price' => 120,
                        'stock' => ['label' => 'In Stock', 'variant' => 'in'],
                        'description' => 'Fresh, hand-picked tomatoes with natural flavor and great shelf life.',
                    ];
                    $reviews = $reviews ?? [
                        ['name' => 'Customer A', 'rating' => 5, 'text' => 'Very fresh and great taste.'],
                        ['name' => 'Customer B', 'rating' => 4, 'text' => 'Good quality and quick pickup.'],
                        ['name' => 'Customer C', 'rating' => 5, 'text' => 'Will buy again.'],
                    ];
                    $related = $related ?? [
                        ['id' => 2, 'trader' => 'Greengrocer', 'category' => 'Vegetables', 'name' => 'Green Peppers', 'price' => 140, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                        ['id' => 3, 'trader' => 'Bakery', 'category' => 'Bakery', 'name' => 'Whole Wheat Bread', 'price' => 180, 'stock' => ['label' => 'Low Stock', 'variant' => 'low']],
                        ['id' => 4, 'trader' => 'Butcher', 'category' => 'Meat', 'name' => 'Chicken Breast', 'price' => 520, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                        ['id' => 5, 'trader' => 'Delicatessen', 'category' => 'Groceries', 'name' => 'Olive Oil', 'price' => 540, 'stock' => ['label' => 'Out of Stock', 'variant' => 'out']],
                    ];
                @endphp

                <div class="product-shop">
                    <span class="product-meta-pill">Trader: {{ $product['trader'] }}</span>
                </div>

                <h2 class="product-details-name">{{ $product['name'] }}</h2>

                <div class="product-details-price-row">
                    <div class="product-price-large">
                        Rs {{ number_format((float) $product['price'], 0) }}
                    </div>
                    @include('partials.status-badge', [
                        'label' => $product['stock']['label'] ?? 'In Stock',
                        'variant' => $product['stock']['variant'] ?? 'in',
                    ])
                </div>

                <div class="product-details-description">
                    <h3>Description</h3>
                    <p class="text-secondary">{{ $product['description'] }}</p>
                </div>

                {{-- Quantity selector + Add to Basket --}}
                <div class="product-qty-add">
                    <div class="qty-field">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" class="qty-input" type="number" value="1" min="1" max="20">
                    </div>

                    <button type="button" class="btn btn-primary product-add-btn">
                        Add to Basket
                    </button>
                </div>

                {{-- Reviews area --}}
                <section class="reviews-section">
                    <h3>Reviews</h3>
                    <div class="reviews-grid">
                        @foreach($reviews as $review)
                            <article class="card review-card">
                                <div class="review-head">
                                    <strong>{{ $review['name'] }}</strong>
                                    <span class="review-stars" aria-label="{{ $review['rating'] }} star rating">
                                        @for($i = 0; $i < $review['rating']; $i++)
                                            ★
                                        @endfor
                                    </span>
                                </div>
                                <p class="text-secondary">{{ $review['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            </article>
        </div>
    </section>

    {{-- You May Like --}}
    <section class="section section-light">
        <div class="container">
            <div class="section-heading">
                <h2>You May Like</h2>
            </div>
            <div class="product-grid">
                @foreach($related as $p)
                    @include('partials.product-card', ['product' => $p])
                @endforeach
            </div>
        </div>
    </section>
@endsection

