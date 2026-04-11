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
                    <p>support@multitrader.com</p>
                </article>
                <article class="card contact-info-card">
                    <h3>📞 Phone Number</h3>
                    <p>020 1234 5678</p>
                </article>
                <article class="card contact-info-card">
                    <h3>📍 Address</h3>
                    <p>Platform House</p>
                </article>
                <article class="card contact-info-card">
                    <h3>🕘 Working Hours</h3>
                    <p>Mon - Sat: 9:00 - 18:00</p>
                    <p>Sun: Closed</p>
                </article>
            </aside>

            <div class="card form-card">
                <form id="contactForm" novalidate>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="firstName">First Name</label>
                            <input id="firstName" name="firstName" type="text" required>
                            <small class="error-text" data-error-for="firstName"></small>
                        </div>
                        <div class="field-group">
                            <label for="lastName">Last Name</label>
                            <input id="lastName" name="lastName" type="text" required>
                            <small class="error-text" data-error-for="lastName"></small>
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" required>
                        <small class="error-text" data-error-for="email"></small>
                    </div>

                    <div class="field-group">
                        <label for="mobile">Mobile Number</label>
                        <input id="mobile" name="mobile" type="tel" required>
                        <small class="error-text" data-error-for="mobile"></small>
                    </div>

                    <div class="field-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                        <small class="error-text" data-error-for="message"></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Message</button>
                    <p id="formSuccess" class="success-text" aria-live="polite"></p>
                </form>
            </div>
        </div>
    </section>
@endsection
