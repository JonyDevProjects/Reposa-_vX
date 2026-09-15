@props([
    'type' => 'success',
    'dismissible' => true,
    'icon' => null,
    'title' => null,
])

@php
    $typeConfig = match($type) {
        'success' => [
            'class' => 'alert-success',
            'icon' => 'bi-check-circle-fill',
        ],
        'danger', 'error' => [
            'class' => 'alert-danger',
            'icon' => 'bi-exclamation-triangle-fill',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => 'bi-exclamation-circle-fill',
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => 'bi-info-circle-fill',
        ],
        default => [
            'class' => 'alert-primary',
            'icon' => 'bi-bell-fill',
        ],
    };

    $iconClass = $icon ?: $typeConfig['icon'];
    $alertClass = $typeConfig['class'];
@endphp

<div {{ $attributes->merge(['class' => "alert {$alertClass} " . ($dismissible ? 'alert-dismissible fade show' : '') . " d-flex align-items-center rounded-3 shadow-sm border-0"]) }} role="alert">
    @if($iconClass)
        <i class="bi {{ $iconClass }} fs-5 me-3 flex-shrink-0"></i>
    @endif
    <div class="flex-grow-1">
        @if($title)
            <h6 class="alert-heading fw-bold mb-1">{{ $title }}</h6>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="{{ __('messages.common.close') ?? 'Close' }}"></button>
    @endif
</div>
