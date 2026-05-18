@extends('layouts.app')

@section('title', 'GroceryGo - Forgot password')

@section('content')
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                <header class="auth-header">
                    <h1>Forgot password</h1>
                    <p>Enter your email and we will send a reset link if you have a customer account.</p>
                </header>

                @if (session('status'))
                    <p class="ok" style="margin-bottom:12px;">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error" style="margin-bottom:12px;">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="auth-form" method="post" action="{{ route('password.email') }}">
                    @csrf
                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit">Send reset link</button>
                </form>

                <p style="margin-top:16px;text-align:center;">
                    <a href="{{ route('login') }}">Back to login</a>
                </p>
            </article>
        </div>
    </section>
@endsection
