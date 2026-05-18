@extends('layouts.app')

@section('title', 'GroceryGo - Cart')

@section('content')
    {{-- Cart page title --}}
    <section class="page-hero cart-hero">
        <div class="container">
            <h1>Your Cart</h1>
            <p>Review your items and checkout in one go.</p>
            @if (!empty($isGuest))
                <p class="text-secondary" style="margin-top:8px;">Sign in to complete checkout. Your basket is saved for this browser session.</p>
            @endif
            @if (session('status'))
                <p class="ok" style="margin-top:8px;">{{ session('status') }}</p>
            @endif
            @if ($errors->has('cart'))
                <p class="alert alert-error" style="margin-top:8px;">{{ $errors->first('cart') }}</p>
            @endif
            <div class="divider"></div>
        </div>
    </section>

    <section class="section cart-section">
        <div class="container cart-layout">
            {{-- 2. Table Layout (Product / Price / Quantity / Total) --}}
            <article class="card cart-table-card">
                <header class="cart-table-header">
                    <h2>Items</h2>
                </header>

                <div class="cart-table-wrap" role="region" aria-label="Cart items table">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $totalPrice = $total ?? 0;
                            @endphp

                            @forelse($cartItems as $item)
                                @php
                                    $name = $item['product_name'] ?? 'Item';
                                    $price = (float) ($item['unit_price'] ?? 0);
                                    $qty = (int) ($item['quantity'] ?? 1);
                                    $itemTotal = (float) ($item['line_total'] ?? ($price * $qty));
                                @endphp
                                <tr class="cart-row">
                                    <td class="cart-product">
                                        <div class="cart-product-name">{{ $name }}</div>
                                    </td>
                                    <td>
                                        <div class="cart-cell-strong">
                                            {{ \App\Support\Money::format($price) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cart-qty-controls" aria-label="Quantity controls">
                                            <form method="post" action="{{ route('cart.update') }}" class="cart-qty-form">
                                                @csrf
                                                @if (!empty($isGuest))
                                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                @else
                                                    <input type="hidden" name="basket_item_id" value="{{ $item['basket_item_id'] }}">
                                                @endif
                                                <input type="hidden" name="quantity" value="{{ max(0, $qty - 1) }}">
                                                <button type="submit" class="qty-btn" aria-label="Decrease quantity">−</button>
                                            </form>
                                            <span class="qty-display" aria-live="polite">{{ $qty }}</span>
                                            <form method="post" action="{{ route('cart.update') }}" class="cart-qty-form">
                                                @csrf
                                                @if (!empty($isGuest))
                                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                                @else
                                                    <input type="hidden" name="basket_item_id" value="{{ $item['basket_item_id'] }}">
                                                @endif
                                                <input type="hidden" name="quantity" value="{{ min(20, $qty + 1) }}">
                                                <button type="submit" class="qty-btn" aria-label="Increase quantity" @disabled($qty >= 20)>+</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cart-cell-strong cart-cell-total">
                                            {{ \App\Support\Money::format($itemTotal) }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="cart-empty">
                                        Your cart is empty.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            {{-- 3. Summary Section + 4. Note --}}
            <aside class="card cart-summary-card">
                <header class="cart-summary-header">
                    <h2>Summary</h2>
                </header>

                <div class="cart-total-line">
                    <span>Total Price</span>
                    <strong>{{ \App\Support\Money::format($totalPrice) }}</strong>
                </div>

                @auth
                    <a class="btn btn-primary cart-checkout-btn" href="{{ route('checkout.collection-slot') }}">
                        Checkout
                    </a>
                @else
                    <a class="btn btn-primary cart-checkout-btn" href="{{ route('login', ['checkout' => 1]) }}">
                        Sign in to checkout
                    </a>
                    <p class="cart-note" style="margin-top:10px;">You will be asked to sign in before payment.</p>
                @endauth

                <p class="cart-note">Maximum of 20 cart items</p>
            </aside>
        </div>
    </section>
@endsection

