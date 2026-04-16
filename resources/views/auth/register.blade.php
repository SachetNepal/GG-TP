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
                    <p>Email verification required after registration</p>
                </header>

                {{-- Register form (UI only until backend/auth is wired) --}}
                <form class="auth-form" method="get" action="/register">
                    <div class="auth-form-grid">
                        {{-- Left column --}}
                        <div class="auth-form-col">
                            <div class="field-group">
                                <label for="firstName">First Name</label>
                                <input id="firstName" name="firstName" type="text" required>
                            </div>

                            <div class="field-group">
                                <label for="lastName">Last Name</label>
                                <input id="lastName" name="lastName" type="text" required>
                            </div>

                            <div class="field-group">
                                <label for="username">Username</label>
                                <input id="username" name="username" type="text" required>
                            </div>

                            <div class="field-group">
                                <label for="phone">Phone Number</label>
                                <input id="phone" name="phone" type="tel" required>
                            </div>
                        </div>

                        {{-- Right column --}}
                        <div class="auth-form-col">
                            <div class="field-group">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" required>
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
                                <textarea id="address" name="address" rows="4" required></textarea>
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
                <a href="/login" class="btn auth-secondary-btn">
                    Login
                </a>
            </article>
        </div>
    </section>
@endsection

