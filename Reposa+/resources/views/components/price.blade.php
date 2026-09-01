@props([
    'amount' => 0,
    'oldAmount' => null,
    'currency' => '€',
    'size' => 'md',
    'splitDecimals' => false,
])

@php
    $formatted = number_format((float) $amount, 2, ',', '.');
    $parts = explode(',', $formatted);
    $integers = $parts[0] ?? '0';
    $decimals = $parts[1] ?? '00';

    $hasDiscount = !is_null($oldAmount) && (float)$oldAmount > (float)$amount;
    $oldFormatted = $hasDiscount ? number_format((float) $oldAmount, 2, ',', '.') : null;

    $sizeClasses = match($size) {
        'sm' => 'fs-6',
        'md' => 'fs-5',
        'lg' => 'fs-4',
        'xl' => 'display-6',
        default => 'fs-5',
    };
@endphp

<div {{ $attributes->merge(['class' => "d-inline-flex align-items-baseline gap-1 price-container text-primary {$sizeClasses}"]) }} aria-label="{{ $formatted }}{{ $currency }}">
    @if($hasDiscount)
        <span class="text-muted text-decoration-line-through fs-6 me-1 opacity-75">
            {{ $oldFormatted }}{{ $currency }}
        </span>
    @endif

    @if($splitDecimals)
        <span class="fw-bold price-integer lh-1">{{ $integers }}</span><span class="fw-semibold price-decimals fs-7 opacity-90">,{{ $decimals }}{{ $currency }}</span>
    @else
        <span class="fw-bold price-full lh-1">{{ $formatted }}{{ $currency }}</span>
    @endif
</div>
