@props([
    'variant' => 'default',
    'label' => 'Agape153',
])

@php
    $sizeClass = match ($variant) {
        'compact' => 'h-14 w-auto md:h-16',
        'hero' => 'h-auto w-[min(70vw,24rem)] sm:w-[28rem]',
        default => 'h-20 w-auto',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <img class="{{ $sizeClass }} agape-master-logo" src="{{ asset('images/agape153-logo.svg') }}" alt="{{ $label }}">
</span>
