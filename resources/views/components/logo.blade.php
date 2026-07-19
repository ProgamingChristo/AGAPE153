@props([
    'variant' => 'default',
    'label' => 'Agape153',
])

@php
    $sizeClass = $variant === 'compact' ? 'h-12 w-auto md:h-14' : 'h-16 w-auto';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <img class="{{ $sizeClass }}" src="{{ asset('images/agape153-logo.svg') }}" alt="{{ $label }}">
</span>
