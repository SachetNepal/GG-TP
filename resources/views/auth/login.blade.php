@extends('layouts.app')

@section('title', 'GroceryGo - Login')

@section('content')
    {{-- Login page wrapper --}}
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                {{-- 1. Center Card Layout --}}
                <header class="auth-header">
                    <h1>Login</h1>
                    <p>Welcome back to GroceryGo. Demo: john.smith@email.com / pass123</p>
                </header>

                @if (session('status'))
                    <p class="ok" style="margin-bottom:12px;">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <p class="alert alert-error" style="margin-bottom:12px;">{{ $errors->first() }}</p>
                @endif
                <form class="auth-form" method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="field-group">
                        <label for="loginEmail">Email</label>
                        <input id="loginEmail" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>

                    <div class="field-group">
                        <label for="loginPassword">Password</label>
                        <input id="loginPassword" name="password" type="password" placeholder="Enter your password" required>
                    </div>

                    <div class="auth-options">
                        <label class="remember-toggle" for="rememberMe">
                            <input id="rememberMe" name="remember" type="checkbox">
                            <span>Remember me</span>
                        </label>
                        <a href="{{ route('contact') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    {{-- 2. Primary Button --}}
                    <button type="submit" class="btn btn-primary auth-submit">Login</button>
                </form>

                {{-- 3. Divider --}}
                <div class="auth-divider">
                    <span>Don't have an account?</span>
                </div>

                {{-- 4. Secondary Button --}}
                <a href="{{ route('register-type') }}" class="btn auth-secondary-btn">Register</a>

                {{-- 5. Role Switch --}}
                <div class="auth-role-switch">
                    <a href="/GG-TP/trader-portal/login.php" class="btn auth-role-btn">Trader</a>
                </div>
            </article>
        </div>
    </section>
@endsection
