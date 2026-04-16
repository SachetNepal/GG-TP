@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
@endphp

{{-- Reusable page header (used by customer-facing pages) --}}
<section class="page-hero">
    <div class="container">
        <h1>{{ $title }}</h1>
        @if (!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
        <div class="divider"></div>
    </div>
</section>

