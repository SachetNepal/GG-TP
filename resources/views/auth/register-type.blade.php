@extends('layouts.app')

@section('title', 'GroceryGo - Register Type')

@section('content')
    {{-- Center card layout: Register as Customer or Trader --}}
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                <header class="auth-header">
                    <h1>Register as:</h1>
                    <p>Choose the account type that matches how you want to use GroceryGo.</p>
                </header>

                <div class="auth-choice-grid">
                    <a class="auth-choice-card" href="{{ route('register', ['type' => 'customer']) }}">
                        <h2>Register as Customer</h2>
                        <p>Shop local stores and collect your orders.</p>
                    </a>

                    <a class="auth-choice-card auth-choice-card--orange" href="{{ url('trader-portal/register.php') }}">
                        <h2>Register as Trader</h2>
                        <p>List your products and manage your shop.</p>
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection

