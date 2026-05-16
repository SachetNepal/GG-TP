@extends('layouts.app')

@section('title', 'GroceryGo - My Profile')

@section('content')
    @include('partials.page-hero', ['title' => 'My Profile'])

    <section class="section">
        <div class="container">
            @php $user = $profile['user']; @endphp
            <article class="card customer-profile-card">
                <div class="customer-profile-layout">
                    <aside class="customer-profile-left">
                        <div class="customer-avatar" aria-hidden="true">
                            <span>{{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}</span>
                        </div>
                    </aside>

                    <div class="customer-profile-right">
                        <h2>Account Details</h2>
                        <div class="profile-details-grid">
                            <div class="profile-detail-item">
                                <span>First Name</span>
                                <strong>{{ $user->first_name }}</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Last Name</span>
                                <strong>{{ $user->last_name }}</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Email</span>
                                <strong>{{ $user->email }}</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Phone</span>
                                <strong>{{ $user->phone_num ?? '—' }}</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Role</span>
                                <strong>{{ ucfirst($user->role) }}</strong>
                            </div>
                        </div>

                        <div class="profile-actions" style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
                            <a href="{{ route('orders.index') }}" class="btn btn-primary">My orders</a>
                            <form method="post" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn auth-secondary-btn">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
