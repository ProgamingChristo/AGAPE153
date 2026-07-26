@props([
    'variant' => 'default',
    'label' => 'Agape153',
])

@php
    $sizeClass = match ($variant) {
        'compact' => '',
        'hero' => '',
        default => '',
    };

    $sizeStyle = match ($variant) {
        'compact' => 'width: clamp(4.75rem, 7vw, 6.25rem); height: auto;',
        'hero' => 'width: min(72vw, 28rem); height: auto;',
        default => 'width: clamp(6rem, 10vw, 8.25rem); height: auto;',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <img
        class="{{ $sizeClass }} agape-master-logo"
        src="{{ asset('images/agape153-logo.png') }}"
        alt="{{ $label }}"
        style="{{ $sizeStyle }}"
        width="462"
        height="427"
        decoding="async"
    >
</span>
