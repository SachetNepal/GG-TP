@extends('layouts.app')

@section('title', 'GroceryGo - Categories')

@section('content')
    {{-- Page title --}}
    @include('partials.page-hero', ['title' => 'Categories'])

    {{-- Categories + Filters --}}
    <section class="section">
        <div class="container categories-layout">
            {{-- Left filter sidebar --}}
            <aside class="card filters-sidebar">
                <h2 class="filters-title">Filters</h2>

                <div class="filters-block">
                    <h3>Categories</h3>
                    @php
                        $categoryOptions = ['Vegetables', 'Bakery', 'Dairy', 'Meat', 'Groceries'];
                    @endphp
                    <div class="filters-options">
                        @foreach($categoryOptions as $opt)
                            <label class="filter-option">
                                <input type="checkbox" name="categories[]" value="{{ $opt }}">
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="filters-block">
                    <h3>Trader / Shop</h3>
                    @php
                        $shopOptions = ['Butcher', 'Greengrocer', 'Bakery', 'Fishmonger', 'Delicatessen'];
                    @endphp
                    <div class="filters-options">
                        @foreach($shopOptions as $opt)
                            <label class="filter-option">
                                <input type="checkbox" name="shops[]" value="{{ $opt }}">
                                <span>{{ $opt }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="btn btn-primary filters-apply-btn">Apply Filters</button>
            </aside>

            {{-- Right product grid --}}
            <div class="categories-main">
                <div class="product-grid">
                    @php
                        $products = [
                            ['id' => 1, 'trader' => 'Greengrocer', 'category' => 'Vegetables', 'name' => 'Fresh Tomatoes', 'price' => 120, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                            ['id' => 2, 'trader' => 'Bakery', 'category' => 'Bakery', 'name' => 'Sourdough Bread', 'price' => 220, 'stock' => ['label' => 'Low Stock', 'variant' => 'low']],
                            ['id' => 3, 'trader' => 'Butcher', 'category' => 'Meat', 'name' => 'Chicken Breast', 'price' => 520, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                            ['id' => 4, 'trader' => 'Delicatessen', 'category' => 'Groceries', 'name' => 'Olive Oil', 'price' => 540, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                            ['id' => 5, 'trader' => 'Fishmonger', 'category' => 'Groceries', 'name' => 'Fresh Salmon', 'price' => 980, 'stock' => ['label' => 'Out of Stock', 'variant' => 'out']],
                            ['id' => 6, 'trader' => 'Greengrocer', 'category' => 'Dairy', 'name' => 'Farm Eggs (10pc)', 'price' => 160, 'stock' => ['label' => 'In Stock', 'variant' => 'in']],
                        ];
                    @endphp

                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

