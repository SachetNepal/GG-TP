@extends('layouts.app')

@section('title', 'GroceryGo - Cart')

@section('content')
    {{-- Cart page title --}}
    <section class="page-hero cart-hero">
        <div class="container">
            <h1>Your Cart</h1>
            <p>Review your items and checkout in one go.</p>
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
                                // Replace these placeholders with your real cart data when wiring backend.
                                $cartItems = $cartItems ?? [
                                    ['name' => 'Fresh Tomatoes', 'price' => 120, 'quantity' => 2],
                                    ['name' => 'Olive Oil', 'price' => 540, 'quantity' => 1],
                                ];
                                $totalPrice = $totalPrice ?? array_sum(array_map(function ($i) {
                                    return ($i['price'] ?? 0) * ($i['quantity'] ?? 0);
                                }, $cartItems));
                            @endphp

                            @forelse($cartItems as $item)
                                @php
                                    $name = $item['name'] ?? 'Item';
                                    $price = (float) ($item['price'] ?? 0);
                                    $qty = (int) ($item['quantity'] ?? 1);
                                    $itemTotal = $price * $qty;
                                @endphp
                                <tr class="cart-row">
                                    <td class="cart-product">
                                        <div class="cart-product-name">{{ $name }}</div>
                                    </td>
                                    <td>
                                        <div class="cart-cell-strong">
                                            Rs {{ number_format($price, 0) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cart-qty-controls" aria-label="Quantity controls (UI only)">
                                            {{-- UI only: buttons won't change value without JS/back-end wiring --}}
                                            <button type="button" class="qty-btn" aria-label="Decrease quantity">-</button>
                                            <input class="qty-input" type="number" value="{{ $qty }}" min="1" readonly>
                                            <button type="button" class="qty-btn" aria-label="Increase quantity">+</button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cart-cell-strong cart-cell-total">
                                            Rs {{ number_format($itemTotal, 0) }}
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
                    <strong>Rs {{ number_format($totalPrice, 0) }}</strong>
                </div>

                <button class="btn btn-primary cart-checkout-btn" type="button">
                    Checkout
                </button>

                <p class="cart-note">Maximum of 20 cart items</p>
            </aside>
        </div>
    </section>
@endsection

