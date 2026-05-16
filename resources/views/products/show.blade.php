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

                @if ($product->reviews->isNotEmpty())
                    @php
                        $avgRating = round((float) ($product->reviews_avg_rating ?? $product->reviews->avg('rating')), 1);
                        $reviewCount = $product->reviews->count();
                        $fullStars = (int) round($avgRating);
                    @endphp
                    <p class="product-rating-summary">
                        <span class="stars-gold" aria-hidden="true">{{ str_repeat('★', $fullStars) }}{{ str_repeat('☆', 5 - $fullStars) }}</span>
                        <span class="text-secondary">{{ $avgRating }} / 5 ({{ $reviewCount }} {{ $reviewCount === 1 ? 'review' : 'reviews' }})</span>
                    </p>
                @endif

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

                <section class="reviews-section">
                    <h3 class="reviews-section-title">Reviews</h3>

                    @auth
                        @if ($userReview)
                            <p class="review-note">
                                You rated this product
                                <span class="stars-gold" aria-label="{{ $userReview->rating }} out of 5">{{ str_repeat('★', (int) $userReview->rating) }}{{ str_repeat('☆', 5 - (int) $userReview->rating) }}</span>
                            </p>
                        @elseif (auth()->user()->customer)
                            <form method="post" action="{{ route('products.reviews.store', $product->product_id) }}" class="card review-form">
                                @csrf
                                <p class="review-form-label">Your rating</p>
                                <div class="star-rating" role="radiogroup" aria-label="Your rating">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input
                                            type="radio"
                                            name="rating"
                                            id="rating-star-{{ $i }}"
                                            value="{{ $i }}"
                                            class="star-rating__input"
                                            @checked((int) old('rating') === $i)
                                            required
                                        >
                                        <label for="rating-star-{{ $i }}" class="star-rating__star" title="{{ $i }} out of 5">★</label>
                                    @endfor
                                </div>
                                @if ($errors->has('rating') || $errors->has('review'))
                                    <div class="alert alert-error" style="margin-bottom:12px;">
                                        @foreach (['rating', 'review'] as $field)
                                            @error($field)
                                                <p>{{ $message }}</p>
                                            @enderror
                                        @endforeach
                                    </div>
                                @endif

                                <div class="form-field">
                                    <label for="review_body">Your review</label>
                                    <textarea id="review_body" name="review_body" rows="4" maxlength="1000" required placeholder="Share your experience with this product…">{{ old('review_body') }}</textarea>
                                </div>
                                @error('review_body')
                                    <p class="alert alert-error" style="margin-top:8px;">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="btn btn-primary">Submit review</button>
                            </form>
                        @else
                            <p class="text-secondary review-note">Customer accounts can leave reviews after signing in.</p>
                        @endif
                    @else
                        <p class="text-secondary review-note"><a href="{{ route('login') }}">Sign in</a> to leave a rating and review.</p>
                    @endauth

                    @if ($product->reviews->isNotEmpty())
                        <div class="reviews-grid">
                            @foreach ($product->reviews as $review)
                                <article class="card review-card">
                                    <div class="review-head">
                                        <strong>{{ trim(($review->customer->user->first_name ?? '').' '.($review->customer->user->last_name ?? '')) ?: 'Customer' }}</strong>
                                        <span class="stars-gold" aria-label="{{ $review->rating }} out of 5">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</span>
                                    </div>
                                    <p class="text-secondary">{{ $review->review_body }}</p>
                                    @if ($review->review_date)
                                        <time class="review-date text-secondary" datetime="{{ $review->review_date->toDateString() }}">{{ $review->review_date->format('j M Y') }}</time>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="text-secondary">No reviews yet. Be the first to share your thoughts.</p>
                    @endif
                </section>
            </article>
        </div>
    </section>
@endsection
