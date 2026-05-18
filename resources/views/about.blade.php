@extends('layouts.app')

@section('title', 'GroceryGo - About Us')

@section('content')
    {{-- 1. HERO / PAGE HEADER: title, divider, intro + illustration --}}
    <section class="page-hero about-page-hero">
        <div class="container">
            <div class="about-hero-heading">
                <h1>About Us</h1>
                <div class="about-title-divider" role="presentation"></div>
            </div>

            <div class="about-hero-grid">
                <div class="about-hero-copy">
                    <h2 class="about-subheading">Our story</h2>
                    <p>
                        Local traders often compete at a disadvantage against large supermarkets with bigger marketing budgets
                        and sprawling supply chains. GroceryGo exists to level the playing field by bringing trusted neighborhood
                        shops together on one friendly platform.
                    </p>
                    <p>
                        Customers can browse multiple local businesses, build a single basket, check out once, and collect
                        everything at a convenient community pickup point. When you shop with GroceryGo, you keep spending
                        closer to home and help independent businesses thrive.
                    </p>
                </div>
                <div class="about-hero-visual card about-illustration-card" aria-hidden="true">
                    <div class="about-illustration-inner"></div>
                    <p class="about-illustration-caption">Fresh groceries from the traders you already know</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. OUR MISSION --}}
    <section class="section" id="mission">
        <div class="container">
            <div class="section-heading">
                <h2>Our Mission</h2>
                <p class="text-black">We are building a marketplace that feels personal, fair, and rooted in the community.</p>
            </div>
            <article class="card about-mission-card">
                <p class="about-mission-lead">
                    GroceryGo connects neighborhood shops with people who want quality groceries without sacrificing convenience.
                    We believe strong local commerce makes streets safer, friendlier, and more resilient.
                </p>
                <h3 class="about-mission-points-title">What we stand for</h3>
                <ul class="mission-points">
                    <li><span class="mission-point-marker" aria-hidden="true"></span>Support local businesses with fair visibility and simple tools.</li>
                    <li><span class="mission-point-marker" aria-hidden="true"></span>Provide convenience to customers who want one basket and one checkout.</li>
                    <li><span class="mission-point-marker" aria-hidden="true"></span>Create fair competition so independents can compete on quality, not scale alone.</li>
                </ul>
            </article>
        </div>
    </section>

    {{-- 3. TEAM SECTION --}}
    <section class="section section-light" id="team">
        <div class="container">
            <article class="card about-team-card">
                <div class="section-heading about-team-heading">
                    <h2>Meet Our Team</h2>
                    <p class="text-black">The people working to keep GroceryGo helpful, human, and community-first.</p>
                </div>
                <div class="team-grid">
                    <div class="team-member">
                        <div class="team-avatar team-avatar--green" aria-hidden="true">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="team-avatar-svg">
                                <circle cx="48" cy="34" r="16" fill="currentColor" opacity="0.35"/>
                                <path d="M24 88c4-22 16-30 24-30s20 8 24 30" stroke="currentColor" stroke-width="6" stroke-linecap="round" opacity="0.45"/>
                            </svg>
                        </div>
                        <p class="team-label">Anushka Thapa</p>
                    </div>
                    <div class="team-member">
                        <div class="team-avatar team-avatar--orange" aria-hidden="true">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="team-avatar-svg">
                                <circle cx="48" cy="34" r="16" fill="currentColor" opacity="0.35"/>
                                <path d="M24 88c4-22 16-30 24-30s20 8 24 30" stroke="currentColor" stroke-width="6" stroke-linecap="round" opacity="0.45"/>
                            </svg>
                        </div>
                        <p class="team-label">Srijan Thapa</p>
                    </div>
                    <div class="team-member">
                        <div class="team-avatar team-avatar--green" aria-hidden="true">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="team-avatar-svg">
                                <circle cx="48" cy="34" r="16" fill="currentColor" opacity="0.35"/>
                                <path d="M24 88c4-22 16-30 24-30s20 8 24 30" stroke="currentColor" stroke-width="6" stroke-linecap="round" opacity="0.45"/>
                            </svg>
                        </div>
                        <p class="team-label">Sujan Tamrakar</p>
                    </div>
                    <div class="team-member">
                        <div class="team-avatar team-avatar--orange" aria-hidden="true">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="team-avatar-svg">
                                <circle cx="48" cy="34" r="16" fill="currentColor" opacity="0.35"/>
                                <path d="M24 88c4-22 16-30 24-30s20 8 24 30" stroke="currentColor" stroke-width="6" stroke-linecap="round" opacity="0.45"/>
                            </svg>
                        </div>
                        <p class="team-label">Saksham Kishore Kshatri</p>
                    </div>
                    <div class="team-member">
                        <div class="team-avatar team-avatar--green" aria-hidden="true">
                            <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" class="team-avatar-svg">
                                <circle cx="48" cy="34" r="16" fill="currentColor" opacity="0.35"/>
                                <path d="M24 88c4-22 16-30 24-30s20 8 24 30" stroke="currentColor" stroke-width="6" stroke-linecap="round" opacity="0.45"/>
                            </svg>
                        </div>
                        <p class="team-label">Sachet Nepal</p>
                    </div>
                </div>
            </article>
        </div>
    </section>
@endsection
