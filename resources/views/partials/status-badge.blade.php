@php
    $label = $label ?? 'In Stock';
    $variant = $variant ?? 'in'; // allowed: in, low, out
    $variant = strtolower(str_replace(' ', '-', $variant));
@endphp

<span class="status-badge status-{{ $variant }}">{{ $label }}</span>

