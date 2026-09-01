@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'pill' => false,
    'circle' => false,
    'disabled' => false,
])

@php
    $variantClass = match($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary text-white',
        'outline', 'outline-primary' => 'btn-outline-primary',
        'outline-danger' => 'btn-outline-danger',
        'outline-light' => 'btn-outline-light',
        'ghost' => 'btn-link text-decoration-none',
        'danger' => 'btn-danger text-white',
        'light' => 'btn-light text-primary',
        default => 'btn-primary',
    };

    $sizeClass = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg px-4 py-3',
        default => '',
    };

    $shapeClass = '';
    if ($pill) {
        $shapeClass = 'rounded-pill';
    } elseif ($circle) {
        $shapeClass = 'rounded-circle d-inline-flex align-items-center justify-content-center';
    }

    $classes = trim("btn fw-semibold {$variantClass} {$sizeClass} {$shapeClass}");
@endphp

@if($href)
    <a href="{{ $disabled ? '#' : $href }}" 
       {{ $attributes->merge(['class' => $classes . ($disabled ? ' disabled' : '')]) }}
       @if($disabled) tabindex="-1" aria-disabled="true" @endif>
        @if($icon && $iconPosition === 'left')
            <i class="bi {{ $icon }} {{ $slot->isNotEmpty() ? 'me-2' : '' }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="bi {{ $icon }} {{ $slot->isNotEmpty() ? 'ms-2' : '' }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" 
            {{ $attributes->merge(['class' => $classes]) }}
            @if($disabled) disabled aria-disabled="true" @endif>
        @if($icon && $iconPosition === 'left')
            <i class="bi {{ $icon }} {{ $slot->isNotEmpty() ? 'me-2' : '' }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="bi {{ $icon }} {{ $slot->isNotEmpty() ? 'ms-2' : '' }}"></i>
        @endif
    </button>
@endif
