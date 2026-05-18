@extends('layouts.app')

@section('title', 'GroceryGo - My orders')

@section('content')
    @include('partials.page-hero', ['title' => 'My orders'])

    <section class="section">
        <div class="container orders-page">
            @if (session('status'))
                <p class="orders-alert ok">{{ session('status') }}</p>
            @endif

            @if ($orders->isEmpty())
                <article class="card orders-empty-card">
                    <h2>No orders yet</h2>
                    <p class="muted">When you place an order, it will appear here with pickup and collection details.</p>
                    <a href="{{ route('categories') }}" class="btn btn-primary">Browse products</a>
                </article>
            @else
                @php
                    $activeCount = $orders->filter(
                        fn ($o) => ! in_array(strtolower((string) $o->status), ['completed', 'cancelled'], true)
                    )->count();
                @endphp

                <div class="orders-summary">
                    <article class="orders-stat-card">
                        <span class="orders-stat-label">Total orders</span>
                        <strong class="orders-stat-value">{{ $orders->count() }}</strong>
                    </article>
                    <article class="orders-stat-card">
                        <span class="orders-stat-label">Active</span>
                        <strong class="orders-stat-value">{{ $activeCount }}</strong>
                    </article>
                </div>

                <section class="orders-panel card" aria-label="Orders table">
                    <header class="orders-panel-head">
                        <h2>Order history</h2>
                        <p class="muted">{{ $orders->count() }} order{{ $orders->count() === 1 ? '' : 's' }}</p>
                    </header>

                    <div class="table-scroll orders-table-wrap">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Placed</th>
                                    <th>Pickup location</th>
                                    <th>Collection date</th>
                                    <th>Collection time</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @include('orders.partials.row', ['order' => $order, 'layout' => 'table'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="orders-card-list" aria-label="Orders cards">
                    @foreach ($orders as $order)
                        @include('orders.partials.row', ['order' => $order, 'layout' => 'card'])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
