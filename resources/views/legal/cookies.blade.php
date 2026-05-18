@extends('layouts.app')

@section('title', 'GroceryGo - Cookie Notice')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Cookie Notice</h1>
            <p>Last updated: {{ now()->format('j F Y') }}</p>
            <div class="divider" role="presentation"></div>
        </div>
    </section>

    <section class="section section-light">
        <div class="container legal-document">
            <p class="legal-lead">
                This Cookie Notice explains how GroceryGO (GG-TP) uses cookies and similar technologies when you visit
                our grocery e-commerce website. It should be read together with our
                <a href="{{ route('legal.privacy') }}">Privacy Policy</a>.
            </p>

            <h2>What cookies are</h2>
            <p>
                Cookies are small text files placed on your device when you visit a website. They help the site remember
                your preferences, keep you logged in, and understand how the site is used. Similar technologies include
                local storage and session storage used by your browser.
            </p>

            <h2>Why GroceryGO uses cookies</h2>
            <p>We use cookies to:</p>
            <ul>
                <li>Keep you signed in securely after login.</li>
                <li>Maintain your shopping cart (including for guest users where enabled).</li>
                <li>Remember choices such as collection preferences during checkout.</li>
                <li>Support email verification and password reset flows.</li>
                <li>Enable payment completion when you use PayPal at checkout.</li>
                <li>Improve reliability and security of the platform.</li>
            </ul>

            <h2>Essential cookies</h2>
            <p>
                These cookies are necessary for the website to function. They include session cookies that identify your
                browsing session, security tokens (CSRF protection), and cookies required for authenticated areas such as
                checkout, orders, and your profile. Without these cookies, core features will not work properly.
            </p>

            <h2>Functional cookies</h2>
            <p>
                Functional cookies remember settings you choose — for example, items in your cart, recently viewed products,
                or UI preferences — so you do not have to re-enter them on every visit.
            </p>

            <h2>Analytics cookies</h2>
            <p>
                Where enabled for this project, analytics cookies help us understand how visitors use pages such as
                categories, product detail, and checkout. This information is aggregated and used to improve the student
                project experience. We do not use analytics cookies to sell your data.
            </p>

            <h2>Third-party cookies such as PayPal</h2>
            <p>
                When you pay with PayPal, PayPal may set its own cookies or use similar technologies on their domain
                to process payments, prevent fraud, and remember your PayPal session. When GroceryGO runs in
                <strong>PayPal Sandbox</strong> mode, these are test-environment cookies and do not involve real charges.
                GroceryGO does not store full payment card details; payment data is handled by PayPal under their policies.
            </p>

            <h2>Managing cookies</h2>
            <p>
                Most browsers let you block or delete cookies through settings. Blocking all cookies may prevent login,
                cart, checkout, and other features from working. Refer to your browser&rsquo;s help documentation for
                instructions on managing cookies.
            </p>

            <h2>Consent</h2>
            <p>
                By continuing to use GroceryGO after seeing information about cookies (for example, in this notice or a
                site banner where shown), you consent to our use of cookies as described here, except where your browser
                or applicable law requires explicit consent for non-essential cookies.
            </p>

            <h2>Updates</h2>
            <p>
                We may update this Cookie Notice when we add features or change how cookies are used. The &ldquo;Last updated&rdquo;
                date at the top will reflect the latest version. Significant changes may also be mentioned on the website or
                in our Privacy Policy.
            </p>

            <h2>Contact us</h2>
            @include('partials.legal-contact')
            <p class="legal-related">
                See also: <a href="{{ route('legal.terms') }}">Terms and Conditions</a> ·
                <a href="{{ route('legal.privacy') }}">Privacy Policy</a>
            </p>
        </div>
    </section>
@endsection
