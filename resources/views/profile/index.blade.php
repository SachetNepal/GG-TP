@extends('layouts.app')

@section('title', 'GroceryGo - My Profile')

@section('content')
    @include('partials.page-hero', ['title' => 'My Profile'])

    <section class="section">
        <div class="container">
            @php $user = $profile['user']; @endphp

            @if (session('status'))
                <p class="ok" style="margin-bottom:16px;">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <div class="alert alert-error" style="margin-bottom:16px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <article class="card customer-profile-card">
                <div class="customer-profile-layout">
                    <aside class="customer-profile-left">
                        <div class="customer-avatar" aria-hidden="true">
                            <span>{{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}</span>
                        </div>
                    </aside>

                    <div class="customer-profile-right">
                        <h2>Account details</h2>
                        <form method="post" action="{{ route('profile.update') }}" class="profile-edit-form">
                            @csrf
                            @method('PUT')
                            <div class="profile-details-grid">
                                <div class="profile-detail-item">
                                    <label for="first_name">First name</label>
                                    <input class="input" id="first_name" name="first_name" required
                                           value="{{ old('first_name', $user->first_name) }}">
                                </div>
                                <div class="profile-detail-item">
                                    <label for="last_name">Last name</label>
                                    <input class="input" id="last_name" name="last_name" required
                                           value="{{ old('last_name', $user->last_name) }}">
                                </div>
                                <div class="profile-detail-item">
                                    <span>Email</span>
                                    <strong>{{ $user->email }}</strong>
                                </div>
                                <div class="profile-detail-item">
                                    <label for="phone_num">Phone</label>
                                    <input class="input" id="phone_num" name="phone_num"
                                           value="{{ old('phone_num', $user->phone_num) }}">
                                </div>
                                <div class="profile-detail-item" style="grid-column:1/-1;">
                                    <label for="address">Address</label>
                                    <textarea class="input" id="address" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>

                            <div class="profile-actions" style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline">My orders</a>
                                <a href="{{ route('password.request') }}" class="btn btn-outline">Change password</a>
                            </div>
                        </form>
                        <form method="post" action="{{ route('logout') }}" style="margin-top:12px;">
                            @csrf
                            <button type="submit" class="btn auth-secondary-btn">Logout</button>
                        </form>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
