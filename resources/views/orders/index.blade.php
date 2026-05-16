@extends('layouts.app')

@section('title', 'GroceryGo - My orders')

@section('content')
    @include('partials.page-hero', ['title' => 'My orders'])

    <section class="section">
        <div class="container">
            @if (session('status'))
                <p class="ok" style="margin-bottom:16px;">{{ session('status') }}</p>
            @endif

            @if ($orders->isEmpty())
                <p class="muted">No orders yet. <a href="{{ route('categories') }}">Browse products</a>.</p>
            @else
                <div class="table-scroll">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->order_date?->format('d M Y H:i') ?? $order->order_date }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>£{{ number_format((float) $order->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
