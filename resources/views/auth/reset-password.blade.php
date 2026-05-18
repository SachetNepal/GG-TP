@extends('layouts.app')

@section('title', 'GroceryGo - Reset password')

@section('content')
    <section class="section section-light auth-section">
        <div class="container auth-container">
            <article class="card auth-card">
                <header class="auth-header">
                    <h1>Reset password</h1>
                    <p>Choose a new password for your account.</p>
                </header>

                @if ($errors->any())
                    <div class="alert alert-error" style="margin-bottom:12px;">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="auth-form" method="post" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ old('token', $token) }}">
                    <div class="field-group">
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password" required minlength="8">
                    </div>
                    <div class="field-group">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8">
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit">Update password</button>
                </form>

                <p style="margin-top:16px;text-align:center;">
                    <a href="{{ route('login') }}">Back to login</a>
                </p>
            </article>
        </div>
    </section>
@endsection
