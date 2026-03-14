@props(['value', 'size' => 'large', 'color' => 'default'])

@php
    $formatted = $value !== null ? 'R$ ' . number_format((float)$value, 2, ',', '.') : '—';
    $sizeClass = match($size) {
        'large' => 'text-4xl font-bold tracking-tight',
        'medium' => 'text-2xl font-semibold tracking-tight',
        'small' => 'text-lg font-semibold',
        default => 'text-4xl font-bold tracking-tight',
    };
    $colorClass = match($color) {
        'green' => 'text-apple-green',
        'blue' => 'text-apple-blue',
        'red' => 'text-apple-red',
        'muted' => 'text-apple-muted',
        default => 'text-apple-text',
    };
@endphp

<span class="{{ $sizeClass }} {{ $colorClass }}">{{ $formatted }}</span>
