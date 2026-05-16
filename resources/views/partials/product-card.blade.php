@php
    $product = $product ?? [];
    $id = $product['id'] ?? 1;
    $trader = $product['trader'] ?? 'Local Trader';
    $category = $product['category'] ?? null;
    $name = $product['name'] ?? 'Product';
    $price = (float) ($product['price'] ?? 0);
    $stock = $product['stock'] ?? ['label' => 'In Stock', 'variant' => 'in'];
    $stockLabel = $stock['label'] ?? 'In Stock';
    $stockVariant = $stock['variant'] ?? 'in';
@endphp

{{-- Shared product card (used in Categories + You May Like) --}}
<article class="card product-card">
    <a href="{{ route('products.show', $id) }}" class="product-card-link" aria-label="View {{ $name }}">
        <div class="product-image-placeholder" aria-hidden="true">
            <span class="product-image-placeholder-text">Image</span>
        </div>
    </a>

    <div class="product-meta">
        <span class="product-meta-pill">{{ $trader }}</span>
        @if (!empty($category))
            <span class="product-meta-sep">|</span>
            <span class="product-meta-sub">{{ $category }}</span>
        @endif
    </div>

    <h3 class="product-name">{{ $name }}</h3>

    <div class="product-bottom-row">
        <div class="product-price">
            Rs {{ number_format($price, 0) }}
        </div>
        @include('partials.status-badge', ['label' => $stockLabel, 'variant' => $stockVariant])
    </div>
</article>

