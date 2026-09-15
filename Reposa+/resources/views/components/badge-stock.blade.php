@props([
    'stock' => 0,
    'lowStockThreshold' => 5,
    'showIcon' => true,
    'size' => 'md',
])

@php
    $stock = (int) $stock;
    $isAvailable = $stock > $lowStockThreshold;
    $isLow = $stock > 0 && $stock <= $lowStockThreshold;
    $isOut = $stock <= 0;

    $sizeClasses = match($size) {
        'sm' => 'px-2 py-1 fs-7',
        'lg' => 'px-4 py-2 fs-6',
        default => 'px-3 py-2',
    };

    if ($isAvailable) {
        $badgeClass = 'bg-success text-white';
        $iconClass = 'bi-check-circle-fill';
        $label = __('messages.product.in_stock') ?: 'En stock';
    } elseif ($isLow) {
        $badgeClass = 'bg-warning text-dark border border-warning-subtle';
        $iconClass = 'bi-box-seam-fill';
        $label = __('messages.catalog.show.last_units', ['count' => $stock]) ?: ($stock . ' disponibles');
    } else {
        $badgeClass = 'bg-danger text-white';
        $iconClass = 'bi-x-circle-fill';
        $label = __('messages.product.out_of_stock') ?: 'Agotado';
    }
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-1 {$badgeClass} {$sizeClasses}"]) }} role="status" aria-label="{{ $label }}">
    @if($showIcon)
        <i class="bi {{ $iconClass }} me-1"></i>
    @endif
    <span>{{ $label }}</span>
</span>
