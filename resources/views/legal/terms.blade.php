@extends('layouts.app')

@section('title', 'GroceryGo - Terms and Conditions')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Terms and Conditions</h1>
            <p>Last updated: {{ now()->format('j F Y') }}</p>
            <div class="divider" role="presentation"></div>
        </div>
    </section>

    <section class="section section-light">
        <div class="container legal-document">
            <p class="legal-lead">
                These Terms and Conditions (&ldquo;Terms&rdquo;) govern your use of GroceryGO (also referred to as GG-TP),
                a local grocery e-commerce platform operated as an academic project. By accessing or using the website,
                you agree to these Terms. If you do not agree, please do not use the platform.
            </p>

            <h2>About GroceryGO</h2>
            <p>
                GroceryGO connects customers with independent local traders (butchers, bakeries, greengrocers, fishmongers,
                delicatessens, and similar shops). Customers can browse products, add items to a basket, place orders,
                select collection slots, and pay online. Traders manage products, stock, discounts, and orders through
                a separate trader portal. Administrative functions may be provided for platform oversight.
            </p>

            <h2>User accounts</h2>
            <p>
                You may need an account to checkout, manage orders, leave reviews, or access trader or admin tools.
                You must provide accurate registration information and keep your login credentials secure.
                Email verification using a one-time code may be required before your account is fully activated.
                You are responsible for all activity under your account.
            </p>

            <h2>Customer responsibilities</h2>
            <ul>
                <li>Provide accurate contact, delivery or collection, and payment-related information at checkout.</li>
                <li>Arrive on time for your selected collection slot where collection is offered.</li>
                <li>Inspect orders at pickup and report issues promptly via our support channels.</li>
                <li>Use reviews and ratings honestly and respectfully; do not post unlawful or abusive content.</li>
                <li>Comply with applicable laws when using the platform.</li>
            </ul>

            <h2>Trader responsibilities</h2>
            <ul>
                <li>List products accurately, including descriptions, prices, availability, and images you upload.</li>
                <li>Maintain reasonable stock levels and fulfil orders placed through GroceryGO.</li>
                <li>Keep trader account credentials confidential and use the portal only for your authorised shop.</li>
                <li>Respond to order and customer enquiries in a timely and professional manner.</li>
                <li>Ensure product listings comply with food safety and trading standards applicable in your jurisdiction.</li>
            </ul>

            <h2>Orders and collection slots</h2>
            <p>
                When you place an order, you select a collection date, time window, and pickup location where offered.
                Collection slots may have capacity limits (for example, a maximum number of orders per slot).
                Order confirmation depends on successful payment and product availability.
                We and our traders may cancel or adjust orders if items are unavailable, with reasonable notice where possible.
            </p>

            <h2>Payments</h2>
            <p>
                Payments are processed through PayPal. When the platform is configured for testing or development,
                <strong>PayPal Sandbox</strong> may be used — transactions in that mode are test payments only and do not
                represent real money transfers. GroceryGO does <strong>not</strong> store your full payment card details;
                card and account data are handled by PayPal under their own terms and privacy policy.
            </p>

            <h2>Cancellations and refunds</h2>
            <p>
                Customers may cancel eligible orders through the website before fulfilment, subject to order status.
                Refund policies may vary by trader and product type. For payment issues in live (non-sandbox) mode,
                contact support with your order reference. Sandbox test payments are not refundable as no real funds are charged.
            </p>

            <h2>Product information</h2>
            <p>
                Product descriptions, images, prices, and stock are supplied by traders. While we aim for accuracy,
                GroceryGO does not guarantee that all information is complete or error-free. Allergen and dietary
                information should be confirmed with the trader where important to you.
            </p>

            <h2>Reviews and ratings</h2>
            <p>
                Verified customers may submit reviews and star ratings for products. We may remove content that is
                misleading, offensive, or violates these Terms. Reviews reflect user opinions, not those of GroceryGO.
            </p>

            <h2>Intellectual property</h2>
            <p>
                The GroceryGO name, logo, website design, and platform software are protected by intellectual property laws.
                Traders retain rights in their own product images and descriptions but grant GroceryGO a licence to display
                them on the platform for the purpose of operating the service.
            </p>

            <h2>Limitation of liability</h2>
            <p>
                GroceryGO is provided as a student project and local e-commerce demonstration. To the fullest extent
                permitted by law, we are not liable for indirect, incidental, or consequential losses arising from use of
                the platform, trader actions, third-party payment services, or technical interruptions. Our total
                liability for any claim relating to the service is limited to the amount you paid for the relevant order
                in live (non-sandbox) mode, if any.
            </p>

            <h2>Changes to terms</h2>
            <p>
                We may update these Terms from time to time. The &ldquo;Last updated&rdquo; date at the top of this page
                will change when we do. Continued use of GroceryGO after changes constitutes acceptance of the revised Terms.
            </p>

            <h2>Contact us</h2>
            @include('partials.legal-contact')
        </div>
    </section>
@endsection
