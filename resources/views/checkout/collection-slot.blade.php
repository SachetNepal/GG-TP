@extends('layouts.app')

@section('title', 'GroceryGo - Select Collection Slot')

@section('content')
    {{-- Page title --}}
    @include('partials.page-hero', ['title' => 'Select Collection Slot'])

    @php
        $selectedLocation = request('location', 'Central Pickup');
        $selectedDate = request('date', 'Wednesday');
        $selectedTime = request('time', '10 AM – 1 PM');
    @endphp

    <section class="section">
        {{-- One form for location/date/time selection (no inline JS) --}}
        <form method="get" action="/checkout/collection-slot" class="slot-form">
            <div class="container slot-layout">
                {{-- Cards on the left --}}
                <div class="slot-left">
                    <section class="card slot-card">
                        <h2>Pickup Location</h2>

                        {{-- 1) Location options --}}
                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Choose your location</legend>

                                <div class="slot-options">
                                    @php
                                        $locations = ['Central Pickup', 'North Pickup', 'East Pickup'];
                                    @endphp
                                    @foreach($locations as $loc)
                                        <label class="slot-option">
                                            <input type="radio" name="location" value="{{ $loc }}" {{ $selectedLocation === $loc ? 'checked' : '' }}>
                                            <span>{{ $loc }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        {{-- 2) Date card --}}
                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Select Date</legend>

                                <div class="slot-options">
                                    @php
                                        $dates = ['Wednesday', 'Thursday', 'Friday'];
                                    @endphp
                                    @foreach($dates as $d)
                                        <label class="slot-option">
                                            <input type="radio" name="date" value="{{ $d }}" {{ $selectedDate === $d ? 'checked' : '' }}>
                                            <span>{{ $d }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        {{-- 3) Time slot card --}}
                        <div class="slot-field">
                            <fieldset>
                                <legend class="slot-legend">Select Time Slot</legend>

                                @php
                                    $times = ['10 AM – 1 PM', '1 PM – 4 PM', '4 PM – 7 PM'];
                                @endphp

                                <div class="slot-options">
                                    @foreach($times as $t)
                                        <label class="slot-option">
                                            <input type="radio" name="time" value="{{ $t }}" {{ $selectedTime === $t ? 'checked' : '' }}>
                                            <span>{{ $t }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    </section>
                </div>

                {{-- Summary card --}}
                <aside class="card slot-summary">
                    <h2>Summary</h2>

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

                    {{-- Confirm Slot button --}}
                    <button type="submit" class="btn btn-primary slot-confirm-btn">
                        Confirm Slot
                    </button>

                    <p class="slot-note">Maximum 20 orders per slot</p>
                </aside>
            </div>
        </form>
    </section>
@endsection

