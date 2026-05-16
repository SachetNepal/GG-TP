@extends('layouts.app')

@section('title', 'GroceryGo - Select Collection Slot')

@section('content')
    @include('partials.page-hero', ['title' => 'Select Collection Slot'])

    @php
        $selectedLocation = request('location', 'Central Pickup');
        $selectedDate = request('slot_date', request('date', 'Wednesday'));
        $selectedTime = request('slot_time', request('time', '10 AM – 1 PM'));
        $currency = $paypalCurrency ?? 'USD';
        $symbol = $currency === 'GBP' ? '£' : ($currency === 'USD' ? '$' : $currency.' ');
        $sandboxTest = isset($paypalSandboxTestAmount) && $paypalSandboxTestAmount !== '' && $paypalSandboxTestAmount !== null
            ? (float) $paypalSandboxTestAmount
            : null;
    @endphp

    <section class="section">
        @if ($errors->any())
            <div class="container" style="margin-bottom:16px;">
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form id="checkout-form" method="post" action="{{ route('checkout.paypal.capture') }}" class="slot-form">
            @csrf
            <input type="hidden" name="paypal_order_id" id="paypal_order_id" value="">

            <div class="container slot-layout">
                <div class="slot-left">
                    <section class="card slot-card">
                        <h2>Pickup Location</h2>

                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Choose your location</legend>
                                <div class="slot-options">
                                    @foreach (['Central Pickup', 'North Pickup', 'East Pickup'] as $loc)
                                        <label class="slot-option">
                                            <input type="radio" name="location" value="{{ $loc }}" {{ $selectedLocation === $loc ? 'checked' : '' }}>
                                            <span>{{ $loc }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Select Date</legend>
                                <div class="slot-options">
                                    @foreach (['Wednesday', 'Thursday', 'Friday'] as $d)
                                        <label class="slot-option">
                                            <input type="radio" name="slot_date" value="{{ $d }}" {{ $selectedDate === $d ? 'checked' : '' }} required>
                                            <span>{{ $d }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Select Time Slot</legend>
                                <div class="slot-options">
                                    @foreach (['10 AM – 1 PM', '1 PM – 4 PM', '4 PM – 7 PM'] as $t)
                                        <label class="slot-option">
                                            <input type="radio" name="slot_time" value="{{ $t }}" {{ $selectedTime === $t ? 'checked' : '' }} required>
                                            <span>{{ $t }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </section>
                </div>

                <aside class="card slot-summary">
                    <h2>Summary</h2>

                    <div class="slot-summary-row">
                        <span>Order total</span>
                        <strong>{{ $symbol }}{{ number_format((float) ($cartTotal ?? 0), 2) }}</strong>
                    </div>
                    <div class="slot-summary-row">
                        <span>Selected location</span>
                        <strong>{{ $selectedLocation }}</strong>
                    </div>
                    <div class="slot-summary-row">
                        <span>Selected date</span>
                        <strong>{{ $selectedDate }}</strong>
                    </div>
                    <div class="slot-summary-row">
                        <span>Selected time</span>
                        <strong>{{ $selectedTime }}</strong>
                    </div>

                    @if ($paypalConfigured ?? false)
                        <p class="slot-note" style="margin-top:12px;">
                            Pay with PayPal Sandbox ({{ $paypalCurrency ?? 'USD' }}).
                            @if ($sandboxTest !== null)
                                Test charge: {{ $symbol }}{{ number_format($sandboxTest, 2) }}.
                            @endif
                        </p>
                        @if ($paypalUseRedirect ?? false)
                            <div class="alert alert-warning" style="margin:10px 0; font-size:0.9rem;">
                                <strong>Sandbox checkout (USD $10.00)</strong>
                                <ul style="margin:8px 0 0 18px; padding:0;">
                                    <li>Pay with Personal buyer only: <code>sb-rie4p49538423@personal.example.com</code></li>
                                    <li>Do <strong>not</strong> use business <code>sb-u0hnt51189721@business.example.com</code></li>
                                    <li>Use PayPal balance in <strong>USD</strong> if offered</li>
                                </ul>
                            </div>
                            <p class="slot-note">
                                You will be sent to PayPal's website (full page, not a popup).
                            </p>
                            <button
                                type="submit"
                                formaction="{{ route('checkout.paypal.redirect') }}"
                                class="btn btn-primary slot-confirm-btn"
                                style="width:100%; margin-top:8px; background:#0070ba; border-color:#0070ba;"
                            >
                                Continue to PayPal
                            </button>
                            <button
                                type="submit"
                                formaction="{{ route('checkout') }}"
                                class="btn btn-outline slot-confirm-btn"
                                style="width:100%; margin-top:8px;"
                            >
                                Skip PayPal (save order locally)
                            </button>
                            <input type="hidden" name="payment_method" value="mock">
                        @else
                            <div id="paypal-button-container" class="paypal-button-container"></div>
                            <p id="paypal-status" class="slot-note" role="status" aria-live="polite"></p>
                        @endif
                    @else
                        <div class="alert alert-warning" style="margin:12px 0;">
                            PayPal sandbox is not configured. Add <code>PAYPAL_CLIENT_ID</code> and
                            <code>PAYPAL_CLIENT_SECRET</code> to your <code>.env</code> file.
                        </div>
                        <button type="submit" formaction="{{ route('checkout') }}" class="btn btn-outline slot-confirm-btn">
                            Place order without PayPal (dev)
                        </button>
                        <input type="hidden" name="payment_method" value="mock">
                    @endif

                    <p class="slot-note">Maximum 20 orders per slot</p>
                </aside>
            </div>
        </form>
    </section>
@endsection

@if (($paypalConfigured ?? false) && !($paypalUseRedirect ?? false))
    @push('scripts')
        <script src="{{ ($paypalSdkUrl ?? 'https://www.sandbox.paypal.com/sdk/js') }}?client-id={{ urlencode($paypalClientId) }}&currency=USD&intent=capture&components=buttons&disable-funding=paylater"></script>
        <script>
            (function () {
                const form = document.getElementById('checkout-form');
                const statusEl = document.getElementById('paypal-status');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const createUrl = @json(route('checkout.paypal.create'));
                const useClientOrder = @json($paypalSandbox ?? false);
                const chargeAmount = @json($paypalChargeAmount ?? '10.00');
                const chargeCurrency = @json($paypalCurrency ?? 'USD');

                function setStatus(msg) {
                    if (statusEl) statusEl.textContent = msg || '';
                }

                function validateSlot() {
                    return form.querySelector('input[name="slot_date"]:checked')
                        && form.querySelector('input[name="slot_time"]:checked');
                }

                if (typeof paypal === 'undefined') {
                    setStatus('PayPal could not load. Check your connection or ad blocker.');
                    return;
                }

                const fundingSource = paypal.FUNDING && paypal.FUNDING.PAYPAL
                    ? paypal.FUNDING.PAYPAL
                    : undefined;

                const buttonConfig = {
                    style: { layout: 'vertical', color: 'gold', shape: 'rect', label: 'paypal' },
                    createOrder: async function (data, actions) {
                        if (!validateSlot()) {
                            setStatus('Please select a collection date and time first.');
                            throw new Error('Slot required');
                        }

                        if (useClientOrder && actions && actions.order) {
                            setStatus('Opening PayPal (' + chargeAmount + ' ' + chargeCurrency + ')…');
                            return actions.order.create({
                                intent: 'CAPTURE',
                                purchase_units: [{
                                    description: 'GroceryGo order',
                                    amount: {
                                        currency_code: chargeCurrency,
                                        value: chargeAmount,
                                    },
                                }],
                            });
                        }

                        setStatus('Connecting to PayPal…');
                        const response = await fetch(createUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                        let payload = {};
                        try {
                            payload = await response.json();
                        } catch (e) {
                            setStatus('Server did not return JSON. Check create-paypal-order / checkout route.');
                            throw e;
                        }
                        if (!response.ok || !payload.id) {
                            const detail = payload.error ? ' ' + payload.error : '';
                            setStatus((payload.message || 'Could not create PayPal order.') + detail);
                            console.error('PayPal create-order failed', response.status, payload);
                            throw new Error('create failed');
                        }
                        setStatus('Order ' + payload.id + ' (' + (payload.amount || '?') + ' ' + (payload.currency || 'USD') + ')');
                        return payload.id;
                    },
                    onApprove: function (data) {
                        document.getElementById('paypal_order_id').value = data.orderID;
                        setStatus('Completing payment…');
                        form.submit();
                    },
                    onError: function (err) {
                        console.error('PayPal SDK error', err);
                        setStatus('PayPal error — use Chrome/Edge (not Brave) and a personal sandbox buyer.');
                    },
                    onCancel: function () {
                        setStatus('Payment cancelled.');
                    },
                };

                if (fundingSource) {
                    buttonConfig.fundingSource = fundingSource;
                }

                paypal.Buttons(buttonConfig).render('#paypal-button-container');
            })();
        </script>
    @endpush
@endif
