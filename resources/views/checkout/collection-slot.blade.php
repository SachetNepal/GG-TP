@extends('layouts.app')

@section('title', 'GroceryGo - Select Collection Slot')

@section('content')
    @include('partials.page-hero', ['title' => 'Select Collection Slot'])

    @php
        $selectedLocation = request('location', 'Central Pickup');
        $selectedDate = request('slot_date', request('date', 'Wednesday'));
        $selectedTime = request('slot_time', request('time', '10 AM – 1 PM'));
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
                        <span>Order total (USD)</span>
                        <strong>{{ \App\Support\Money::format((float) ($cartTotal ?? 0)) }}</strong>
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
                        <p class="slot-note" style="margin-top:14px;">
                            You will pay <strong>{{ \App\Support\Money::format((float) ($cartTotal ?? 0)) }}</strong> via PayPal.
                        </p>
                        <div id="paypal-button-container" class="paypal-button-container"></div>
                        <p id="paypal-status" class="slot-note" role="status" aria-live="polite"></p>
                        @if (config('app.debug'))
                            <button type="submit" formaction="{{ route('checkout') }}" class="btn btn-outline slot-confirm-btn" style="width:100%;margin-top:12px;font-size:14px;">
                                Dev: place order without PayPal
                            </button>
                            <input type="hidden" name="payment_method" value="mock">
                        @endif
                    @else
                        <div class="alert alert-warning" style="margin:12px 0;">
                            PayPal is not configured. Add credentials to <code>.env</code>.
                        </div>
                        <button type="submit" formaction="{{ route('checkout') }}" class="btn btn-outline slot-confirm-btn">
                            Place order (dev)
                        </button>
                        <input type="hidden" name="payment_method" value="mock">
                    @endif

                    <p class="slot-note">Maximum 20 orders per slot</p>
                </aside>
            </div>
        </form>
    </section>
@endsection

@if ($paypalConfigured ?? false)
    @push('scripts')
        <script src="{{ ($paypalSdkUrl ?? 'https://www.sandbox.paypal.com/sdk/js') }}?client-id={{ urlencode($paypalClientId) }}&currency={{ urlencode($paypalCurrency ?? 'USD') }}&intent=capture&components=buttons"></script>
        <script>
            (function () {
                const form = document.getElementById('checkout-form');
                const statusEl = document.getElementById('paypal-status');
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const createUrl = @json(route('checkout.paypal.create'));

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

                paypal.Buttons({
                    createOrder: async function () {
                        if (!validateSlot()) {
                            setStatus('Please select a collection date and time first.');
                            throw new Error('Slot required');
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
                        const payload = await response.json().catch(() => ({}));
                        if (!response.ok || !payload.id) {
                            setStatus(payload.message || 'Could not create PayPal order.');
                            throw new Error('create failed');
                        }
                        setStatus('Pay ' + (payload.amount || '') + ' ' + (payload.currency || 'USD'));
                        return payload.id;
                    },
                    onApprove: function (data) {
                        document.getElementById('paypal_order_id').value = data.orderID;
                        setStatus('Completing payment…');
                        form.submit();
                    },
                    onError: function (err) {
                        console.error('PayPal SDK error', err);
                        setStatus('PayPal error. Try again or use another browser.');
                    },
                    onCancel: function () {
                        setStatus('Payment cancelled.');
                    },
                }).render('#paypal-button-container');
            })();
        </script>
    @endpush
@endif
