@extends('layouts.app')

@section('title', 'GroceryGo - My Profile')

@section('content')
    {{-- Page title --}}
    @include('partials.page-hero', ['title' => 'My Profile'])

    <section class="section">
        <div class="container">
            {{-- Account details card --}}
            <article class="card customer-profile-card">
                <div class="customer-profile-layout">
                    {{-- Left: profile image and actions --}}
                    <aside class="customer-profile-left">
                        <div class="customer-avatar" aria-hidden="true">
                            <span>Profile</span>
                        </div>

                        <button type="button" class="btn auth-secondary-btn profile-side-btn">
                            Upload Picture
                        </button>
                        <button type="button" class="btn auth-role-btn profile-side-btn">
                            Change Password
                        </button>
                    </aside>

                    {{-- Right: account details and action buttons --}}
                    <div class="customer-profile-right">
                        <h2>Account Details</h2>

                        <div class="profile-details-grid">
                            <div class="profile-detail-item">
                                <span>First Name</span>
                                <strong>Sachet</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Last Name</span>
                                <strong>Nepal</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Username</span>
                                <strong>sachet.ggtp</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Email</span>
                                <strong>sachet@example.com</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Phone Number</span>
                                <strong>+977 9840000000</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Address</span>
                                <strong>Kathmandu, Nepal</strong>
                            </div>
                            <div class="profile-detail-item">
                                <span>Customer ID</span>
                                <strong>CUS-001245</strong>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button type="button" class="btn btn-primary">Edit Details</button>
                            <a href="/login" class="btn auth-secondary-btn">Logout</a>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection

