@extends('layouts.app')

@section('title', 'GroceryGo - Sign Up')

@section('content')
    {{-- Center card layout: customer sign up --}}
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                {{-- 1) Page title --}}
                <header class="auth-header">
                    <h1>Sign Up</h1>
                    <p>We will email you a 6-digit code to verify your account.</p>
                </header>

                @if ($errors->any())
                    <div class="alert alert-error" style="margin-bottom:12px;">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="auth-form" method="post" action="{{ route('register') }}">
                    @csrf
                    <div class="auth-form-grid">
                        {{-- Left column --}}
                        <div class="auth-form-col">
                            <div class="field-group">
                                <label for="firstName">First Name</label>
                                <input id="firstName" name="first_name" type="text" value="{{ old('first_name') }}" required>
                            </div>

                            <div class="field-group">
                                <label for="lastName">Last Name</label>
                                <input id="lastName" name="last_name" type="text" value="{{ old('last_name') }}" required>
                            </div>

                            <div class="field-group">
                                <label for="phone">Phone Number</label>
                                <input id="phone" name="phone_num" type="tel" value="{{ old('phone_num') }}" required>
                            </div>
                        </div>

                        {{-- Right column --}}
                        <div class="auth-form-col">
                            <div class="field-group">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="field-group">
                                <label for="password">Password</label>
                                <input id="password" name="password" type="password" required>
                            </div>

                            <div class="field-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required>
                            </div>

                            <div class="field-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" rows="4" required>{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 2) Primary button --}}
                    <button type="submit" class="btn btn-primary auth-create-btn">
                        Create Account
                    </button>
                </form>

                {{-- 3) Divider / text --}}
                <div class="auth-divider auth-divider--text">
                    <span>Already have an account?</span>
                </div>

                {{-- 4) Secondary button --}}
                <a href="{{ route('login') }}" class="btn auth-secondary-btn">
                    Login
                </a>
            </article>
        </div>
    </section>
@endsection

