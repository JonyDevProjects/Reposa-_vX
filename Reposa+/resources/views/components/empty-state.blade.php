@props([
    'icon' => 'bi-moon-stars',
    'title' => '',
    'description' => '',
    'highlight' => null,
    'actionUrl' => null,
    'actionText' => null,
    'actionIcon' => 'bi-arrow-right',
    'secondaryUrl' => null,
    'secondaryText' => null,
    'secondaryIcon' => null,
    'variant' => 'default',
])

<div {{ $attributes->merge(['class' => 'empty-state-card text-center py-5 px-3']) }}>
    <!-- Ambient Icon Aura -->
    <div class="empty-state-icon-aura mx-auto mb-4 d-inline-flex align-items-center justify-content-center">
        <i class="bi {{ $icon }} empty-state-icon text-primary"></i>
    </div>

    @if($highlight)
        <div class="mb-3">
            <span class="badge bg-indigo-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold small">
                <i class="bi bi-patch-check-fill me-1 text-primary"></i>{{ $highlight }}
            </span>
        </div>
    @endif

    <h3 class="h4 fw-bold text-navy mb-2">{{ $title }}</h3>

    @if($description)
        <p class="text-muted mx-auto mb-4 empty-state-desc">{{ $description }}</p>
    @endif

    @if($actionUrl || $secondaryUrl)
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 mb-2">
            @if($actionUrl && $actionText)
                <x-button :href="$actionUrl" size="md" :pill="true" class="btn-cta-bold text-decoration-none px-4" :icon="$actionIcon" iconPosition="right">
                    {{ $actionText }}
                </x-button>
            @endif

            @if($secondaryUrl && $secondaryText)
                <a href="{{ $secondaryUrl }}" class="btn btn-outline-secondary btn-md rounded-pill px-4 fw-semibold text-decoration-none d-inline-flex align-items-center gap-2">
                    @if($secondaryIcon)
                        <i class="bi {{ $secondaryIcon }}"></i>
                    @endif
                    {{ $secondaryText }}
                </a>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
