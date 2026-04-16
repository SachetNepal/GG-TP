@extends('layouts.app')

@section('title', 'GroceryGo - Contact Us')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Questions, support, or partnership requests? We are here to help.</p>
            <div class="divider"></div>
        </div>
    </section>

    <section class="section">
        <div class="container contact-layout">
            <aside class="contact-info-grid">
                <article class="card contact-info-card">
                    <h3>📧 Support Email</h3>
                    <p>support.aim@tbc.edu.np</p>
                </article>
                <article class="card contact-info-card">
                    <h3>📞 Phone Number</h3>
                    <p>+977 9840000000</p>
                </article>
                <article class="card contact-info-card">
                    <h3>📍 Address</h3>
                    <p>Trade Tower, Kathmandu, Nepal</p>
                </article>
                <article class="card contact-info-card">
                    <h3>🕘 Working Hours</h3>
                    <p>Mon - Sat: 9:00 - 18:00</p>
                    <p>Sun: Closed</p>
                </article>
            </aside>

            <div class="card form-card">
                {{-- Static: browser HTML5 validation only; GET keeps this front-end only (no POST handler) --}}
                <form class="contact-form-static" method="get" action="/contact">
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="firstName">First Name</label>
                            <input id="firstName" name="firstName" type="text" required minlength="2">
                        </div>
                        <div class="field-group">
                            <label for="lastName">Last Name</label>
                            <input id="lastName" name="lastName" type="text" required minlength="2">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" required>
                    </div>

                    <div class="field-group">
                        <label for="mobile">Mobile Number</label>
                        <input id="mobile" name="mobile" type="tel" required pattern="[0-9+\s()-]{7,}">
                    </div>

                    <div class="field-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required minlength="10"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Message</button>
                </form>
            </div>
        </div>
    </section>
@endsection
