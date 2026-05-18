@extends('layouts.app')

@section('title', 'GroceryGo - Privacy Policy')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p>Last updated: {{ now()->format('j F Y') }}</p>
            <div class="divider" role="presentation"></div>
        </div>
    </section>

    <section class="section section-light">
        <div class="container legal-document">
            <p class="legal-lead">
                This Privacy Policy explains how GroceryGO (GG-TP) collects, uses, stores, and protects personal
                information when you use our local grocery e-commerce platform. We are committed to handling your data
                responsibly in line with this policy and applicable data protection principles.
            </p>

            <h2>Information collected</h2>
            <p>We may collect the following types of information:</p>
            <ul>
                <li><strong>Account data:</strong> name, email address, password (stored in hashed form), role (customer, trader, or admin), and verification status.</li>
                <li><strong>Profile data:</strong> phone number, address, and preferences you provide in your profile.</li>
                <li><strong>Order data:</strong> basket contents, order history, collection slot choices, payment references, and order status.</li>
                <li><strong>Trader data:</strong> shop name, business details, product listings, stock, discounts, and uploaded product images.</li>
                <li><strong>Reviews and ratings:</strong> text, star ratings, and associated product or order identifiers.</li>
                <li><strong>Technical data:</strong> IP address, browser type, session identifiers, and cookies (see our <a href="{{ route('legal.cookies') }}">Cookie Notice</a>).</li>
            </ul>

            <h2>How information is used</h2>
            <ul>
                <li>To create and manage your account and authenticate you when you log in.</li>
                <li>To process orders, manage collection slots, and communicate order updates.</li>
                <li>To operate the shopping cart, checkout, and payment flow with PayPal.</li>
                <li>To display product catalogues, shop pages, reviews, and personalised content where applicable.</li>
                <li>To improve platform security, troubleshoot issues, and develop features for this academic project.</li>
            </ul>

            <h2>Email verification and communication</h2>
            <p>
                When you register, we may send a one-time verification code to your email address before your account
                is activated. We may also send password reset links, order confirmations, and service-related messages.
                You can contact us to query communications sent to your address.
            </p>

            <h2>Payment information</h2>
            <p>
                Payments are handled by PayPal. GroceryGO does <strong>not</strong> store your full payment card numbers
                or card security codes. We may store PayPal transaction IDs, order totals, and payment status returned
                from PayPal. When the system runs in <strong>PayPal Sandbox</strong> mode, payments are test transactions only.
            </p>

            <h2>Data storage and security</h2>
            <p>
                Data is stored in an Oracle database accessed via OCI8, with application logic in PHP and Laravel.
                We use industry-standard practices such as password hashing, session management, and HTTPS where
                configured. No system is completely secure; please use a strong password and keep your credentials private.
            </p>

            <h2>Sharing of information</h2>
            <p>We may share information only where necessary:</p>
            <ul>
                <li>With traders to fulfil your orders (name, contact details, order items, collection slot).</li>
                <li>With PayPal to process payments (subject to PayPal&rsquo;s privacy policy).</li>
                <li>With administrators for platform operation, fraud prevention, and support.</li>
                <li>When required by law or to protect rights, safety, or property.</li>
            </ul>
            <p>We do not sell your personal data to third parties for marketing purposes.</p>

            <h2>Reviews and public content</h2>
            <p>
                Product reviews and ratings you submit may be visible to other users on product pages. Do not include
                sensitive personal information in review text. We may moderate or remove content that breaches our policies.
            </p>

            <h2>Cookies and tracking</h2>
            <p>
                We use cookies and similar technologies for sessions, cart functionality, and preferences.
                For full details, see our <a href="{{ route('legal.cookies') }}">Cookie Notice</a>.
            </p>

            <h2>User rights</h2>
            <p>Depending on applicable law, you may have the right to:</p>
            <ul>
                <li>Access personal data we hold about you.</li>
                <li>Request correction of inaccurate data via your profile or support.</li>
                <li>Request deletion of your account, subject to legal or operational retention needs.</li>
                <li>Object to or restrict certain processing where applicable.</li>
            </ul>
            <p>To exercise these rights, contact us using the details below.</p>

            <h2>Data retention</h2>
            <p>
                We retain account and order data for as long as needed to operate the service, comply with academic
                project requirements, and resolve disputes. Verification codes and session data are kept only for
                limited periods. You may request account deletion by contacting support.
            </p>

            <h2>Children&rsquo;s privacy</h2>
            <p>
                GroceryGO is not directed at children under 16. We do not knowingly collect personal data from children.
                If you believe a child has provided us with personal information, please contact us so we can take appropriate steps.
            </p>

            <h2>Changes to privacy policy</h2>
            <p>
                We may update this Privacy Policy from time to time. The date at the top of this page will be revised
                when changes are made. We encourage you to review this page periodically.
            </p>

            <h2>Contact us</h2>
            @include('partials.legal-contact')
        </div>
    </section>
@endsection
