@props([
    'variant' => 'cards',
])

@if($variant === 'cards')
    <!-- 4 Pillars of Rest & Trust (Full Grid) -->
    <div {{ $attributes->merge(['class' => 'trust-pillars-grid']) }}>
        <div class="row g-4">
            <!-- Pillar 1: 100 Nights Trial -->
            <div class="col-md-6 col-lg-3">
                <div class="trust-card h-100 p-4">
                    <div class="trust-icon-box bg-indigo-subtle text-primary mb-3">
                        <i class="bi bi-moon-stars-fill fs-4"></i>
                    </div>
                    <span class="badge trust-badge-pill mb-2">
                        <i class="bi bi-shield-check me-1 text-success"></i>{{ __('messages.home.trust.sleep_guarantee_badge') }}
                    </span>
                    <h3 class="h5 fw-bold text-navy mb-2">{{ __('messages.home.trust.guarantee_title') }}</h3>
                    <p class="text-muted small mb-0">{{ __('messages.home.trust.guarantee_desc') }}</p>
                </div>
            </div>

            <!-- Pillar 2: Free 24/48h Shipping -->
            <div class="col-md-6 col-lg-3">
                <div class="trust-card h-100 p-4">
                    <div class="trust-icon-box bg-indigo-subtle text-primary mb-3">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <span class="badge trust-badge-pill mb-2">
                        <i class="bi bi-lightning-charge-fill me-1 text-warning"></i>24/48h Express
                    </span>
                    <h3 class="h5 fw-bold text-navy mb-2">{{ __('messages.home.trust.shipping_title') }}</h3>
                    <p class="text-muted small mb-0">{{ __('messages.home.trust.shipping_desc') }}</p>
                </div>
            </div>

            <!-- Pillar 3: Stripe Secure Payment -->
            <div class="col-md-6 col-lg-3">
                <div class="trust-card h-100 p-4">
                    <div class="trust-icon-box bg-indigo-subtle text-primary mb-3">
                        <i class="bi bi-shield-lock-fill fs-4"></i>
                    </div>
                    <span class="badge trust-badge-pill mb-2">
                        <i class="bi bi-lock-fill me-1 text-primary"></i>SSL 256-bit
                    </span>
                    <h3 class="h5 fw-bold text-navy mb-2">{{ __('messages.home.trust.stripe_title') }}</h3>
                    <p class="text-muted small mb-3">{{ __('messages.home.trust.stripe_desc') }}</p>
                    <div class="d-flex align-items-center gap-2 mt-auto pt-2 border-top border-light-subtle">
                        <span class="stripe-badge-text">Powered by</span>
                        <svg class="stripe-wordmark-svg" viewBox="0 0 60 25" width="48" height="20" fill="currentColor" aria-label="Stripe">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M59.64 14.28c0-4.48-2.18-8-6.44-8-4.28 0-6.84 3.54-6.84 8 0 5.28 3.12 7.94 7.54 7.94 2.16 0 3.8-.5 5.04-1.28v-3.32c-1.24.7-2.68 1.1-4.42 1.1-1.78 0-3.34-.64-3.52-2.5h8.6c.02-.32.04-1.4.04-1.94zm-8.6-1.54c0-1.78 1.08-2.48 2.14-2.48 1.04 0 2.06.7 2.06 2.48h-4.2zm-9.36-6.46c-1.72 0-2.82.8-3.4 1.38l-.22-1.1h-4.04v19.64l4.64-.98.02-4.52c.6.5 1.54 1.18 3.02 1.18 3.86 0 6.64-3.08 6.64-7.84-.02-4.88-2.84-7.76-6.66-7.76zm-1.42 12.18c-1.14 0-1.88-.44-2.34-.94l-.04-6.52c.48-.56 1.24-.98 2.38-.98 1.84 0 3.04 1.78 3.04 4.22 0 2.46-1.18 4.22-3.04 4.22zm-12.72-9.66h-4.58l-.02 9.78c0 2.06 1.54 2.86 3.32 2.86.96 0 1.68-.16 2.14-.42v-3.42c-.44.18-.94.26-1.54.26-.64 0-.92-.32-.92-.98v-8.08zm0-3.76l-4.58.98v3.22h4.58V5.04zm-8.48 5.76l-.26-1.12h-3.98v12.54h4.64v-7.98c1.08-1.4 2.92-1.16 3.52-.94V8.98c-.64-.26-2.84-.66-3.92 1.82zm-7.66 1.9c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32zm-14.7 0c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32z"/>
                        </svg>
                        <div class="d-flex align-items-center gap-1 ms-auto">
                            <span class="badge bg-light text-secondary border px-1 py-0 small" style="font-size: 0.65rem;">VISA</span>
                            <span class="badge bg-light text-secondary border px-1 py-0 small" style="font-size: 0.65rem;">MC</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pillar 4: Ergonomic Health Certified -->
            <div class="col-md-6 col-lg-3">
                <div class="trust-card h-100 p-4">
                    <div class="trust-icon-box bg-indigo-subtle text-primary mb-3">
                        <i class="bi bi-heart-pulse-fill fs-4"></i>
                    </div>
                    <span class="badge trust-badge-pill mb-2">
                        <i class="bi bi-patch-check-fill me-1 text-primary"></i>OEKO-TEX
                    </span>
                    <h3 class="h5 fw-bold text-navy mb-2">{{ __('messages.home.trust.health_title') }}</h3>
                    <p class="text-muted small mb-0">{{ __('messages.home.trust.health_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

@elseif($variant === 'compact')
    <!-- Compact Trust Strip (Product Detail Page) -->
    <div {{ $attributes->merge(['class' => 'trust-compact-box p-3 rounded-4 bg-white border border-light-subtle shadow-sm']) }}>
        <div class="row g-3 text-center text-sm-start align-items-center">
            <div class="col-sm-4 border-end-sm">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-moon-stars text-primary fs-4 flex-shrink-0"></i>
                    <div>
                        <span class="d-block fw-bold text-navy small">100 Noches</span>
                        <span class="text-muted" style="font-size: 0.75rem;">Prueba en casa</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 border-end-sm">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-truck text-primary fs-4 flex-shrink-0"></i>
                    <div>
                        <span class="d-block fw-bold text-navy small">Envío Gratis</span>
                        <span class="text-muted" style="font-size: 0.75rem;">24/48h península</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-shield-lock text-primary fs-4 flex-shrink-0"></i>
                    <div>
                        <span class="d-block fw-bold text-navy small">Pago Stripe</span>
                        <span class="text-muted" style="font-size: 0.75rem;">SSL 256-bit seguro</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@elseif($variant === 'checkout')
    <!-- Checkout / Cart Trust Assurance Box -->
    <div {{ $attributes->merge(['class' => 'trust-checkout-seal p-3 mt-3 rounded-3 text-center']) }}>
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <i class="bi bi-shield-check text-success fs-5"></i>
            <span class="fw-bold text-navy small">{{ __('messages.home.trust.secure_checkout') }}</span>
        </div>
        <div class="d-inline-flex align-items-center justify-content-center gap-2 px-3 py-1 bg-white rounded-pill border border-light-subtle shadow-2xs mb-2">
            <svg class="stripe-wordmark-svg" viewBox="0 0 60 25" width="44" height="18" fill="#635BFF" aria-label="Stripe">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M59.64 14.28c0-4.48-2.18-8-6.44-8-4.28 0-6.84 3.54-6.84 8 0 5.28 3.12 7.94 7.54 7.94 2.16 0 3.8-.5 5.04-1.28v-3.32c-1.24.7-2.68 1.1-4.42 1.1-1.78 0-3.34-.64-3.52-2.5h8.6c.02-.32.04-1.4.04-1.94zm-8.6-1.54c0-1.78 1.08-2.48 2.14-2.48 1.04 0 2.06.7 2.06 2.48h-4.2zm-9.36-6.46c-1.72 0-2.82.8-3.4 1.38l-.22-1.1h-4.04v19.64l4.64-.98.02-4.52c.6.5 1.54 1.18 3.02 1.18 3.86 0 6.64-3.08 6.64-7.84-.02-4.88-2.84-7.76-6.66-7.76zm-1.42 12.18c-1.14 0-1.88-.44-2.34-.94l-.04-6.52c.48-.56 1.24-.98 2.38-.98 1.84 0 3.04 1.78 3.04 4.22 0 2.46-1.18 4.22-3.04 4.22zm-12.72-9.66h-4.58l-.02 9.78c0 2.06 1.54 2.86 3.32 2.86.96 0 1.68-.16 2.14-.42v-3.42c-.44.18-.94.26-1.54.26-.64 0-.92-.32-.92-.98v-8.08zm0-3.76l-4.58.98v3.22h4.58V5.04zm-8.48 5.76l-.26-1.12h-3.98v12.54h4.64v-7.98c1.08-1.4 2.92-1.16 3.52-.94V8.98c-.64-.26-2.84-.66-3.92 1.82zm-7.66 1.9c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32zm-14.7 0c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32z"/>
            </svg>
            <span class="text-muted" style="font-size: 0.7rem;">•</span>
            <span class="text-muted fw-semibold" style="font-size: 0.7rem;">SSL 256-bit</span>
            <span class="text-muted" style="font-size: 0.7rem;">•</span>
            <span class="badge bg-light text-dark border px-1 py-0" style="font-size: 0.65rem;">Visa</span>
            <span class="badge bg-light text-dark border px-1 py-0" style="font-size: 0.65rem;">Mastercard</span>
        </div>
        <p class="text-muted mb-0" style="font-size: 0.75rem;">
            <i class="bi bi-shield-check text-success me-1"></i>100 noches de prueba con devolución gratuita garantizada
        </p>
    </div>

@elseif($variant === 'footer')
    <!-- Footer Trust & Payment Bar -->
    <div {{ $attributes->merge(['class' => 'footer-trust-strip d-flex flex-wrap align-items-center justify-content-center justify-content-md-end gap-3 text-white-50']) }}>
        <div class="d-inline-flex align-items-center gap-2">
            <i class="bi bi-shield-lock-fill text-white-50 small"></i>
            <span class="small" style="font-size: 0.75rem;">Pago Seguro Stripe SSL</span>
        </div>
        <div class="d-inline-flex align-items-center gap-2">
            <svg class="stripe-footer-svg text-white" viewBox="0 0 60 25" width="40" height="17" fill="rgba(255,255,255,0.75)" aria-label="Stripe">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M59.64 14.28c0-4.48-2.18-8-6.44-8-4.28 0-6.84 3.54-6.84 8 0 5.28 3.12 7.94 7.54 7.94 2.16 0 3.8-.5 5.04-1.28v-3.32c-1.24.7-2.68 1.1-4.42 1.1-1.78 0-3.34-.64-3.52-2.5h8.6c.02-.32.04-1.4.04-1.94zm-8.6-1.54c0-1.78 1.08-2.48 2.14-2.48 1.04 0 2.06.7 2.06 2.48h-4.2zm-9.36-6.46c-1.72 0-2.82.8-3.4 1.38l-.22-1.1h-4.04v19.64l4.64-.98.02-4.52c.6.5 1.54 1.18 3.02 1.18 3.86 0 6.64-3.08 6.64-7.84-.02-4.88-2.84-7.76-6.66-7.76zm-1.42 12.18c-1.14 0-1.88-.44-2.34-.94l-.04-6.52c.48-.56 1.24-.98 2.38-.98 1.84 0 3.04 1.78 3.04 4.22 0 2.46-1.18 4.22-3.04 4.22zm-12.72-9.66h-4.58l-.02 9.78c0 2.06 1.54 2.86 3.32 2.86.96 0 1.68-.16 2.14-.42v-3.42c-.44.18-.94.26-1.54.26-.64 0-.92-.32-.92-.98v-8.08zm0-3.76l-4.58.98v3.22h4.58V5.04zm-8.48 5.76l-.26-1.12h-3.98v12.54h4.64v-7.98c1.08-1.4 2.92-1.16 3.52-.94V8.98c-.64-.26-2.84-.66-3.92 1.82zm-7.66 1.9c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32zm-14.7 0c-.84-.44-2.26-.88-3.58-.88-1.94 0-3.26.96-3.26 2.58 0 2.5 3.66 2.1 3.66 3.18 0 .42-.38.68-1.14.68-1.28 0-2.92-.54-4.2-1.28l-.02 3.64c1.38.62 2.94.94 4.34.94 2.22 0 3.74-1.06 3.74-2.76 0-2.66-3.68-2.22-3.68-3.24 0-.34.3-.58.96-.58 1.1 0 2.42.42 3.44.96l-.26-3.32z"/>
            </svg>
            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-2 py-0 small" style="font-size: 0.65rem;">VISA</span>
            <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-2 py-0 small" style="font-size: 0.65rem;">Mastercard</span>
        </div>
    </div>
@endif
