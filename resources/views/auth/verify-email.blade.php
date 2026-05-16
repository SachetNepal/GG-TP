@extends('layouts.app')

@section('title', 'GroceryGo - Verify Email')

@section('content')
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                <header class="auth-header">
                    <h1>Verify your email</h1>
                    <p>Enter the 6-digit code we sent to your inbox.</p>
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

                <form class="auth-form" method="post" action="{{ route('verify-email.submit') }}">
                    @csrf
                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}" required>
                    </div>
                    <div class="field-group">
                        <label for="code">Verification code</label>
                        <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000000" required>
                    </div>
                    <button type="submit" class="btn btn-primary auth-create-btn">Verify email</button>
                </form>

                <form class="auth-form" method="post" action="{{ route('verify-email.resend') }}" style="margin-top:16px;">
                    @csrf
                    <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
                    <button type="submit" class="btn auth-secondary-btn" style="width:100%;">Resend code</button>
                </form>

                <div class="auth-divider auth-divider--text" style="margin-top:16px;">
                    <span>Already verified?</span>
                </div>
                <a href="{{ route('login') }}" class="btn auth-secondary-btn">Back to login</a>
            </article>
        </div>
    </section>
@endsection
