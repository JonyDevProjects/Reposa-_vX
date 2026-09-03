@extends('layouts.app')

@section('title', __('messages.nav.home'))

@section('content')
    <!-- Hero Section (Bolder Visual Impact & Authority) -->
    <section class="hero-section text-center position-relative overflow-hidden">
        <div class="container position-relative z-1">
            <h1 class="display-3 fw-bold mb-3 text-white">{{ __('messages.home.hero.title') }}</h1>
            <p class="lead mb-4 text-white opacity-90 mx-auto" style="max-width: 65ch;">{{ __('messages.home.hero.subtitle') }}</p>
            
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="/catalog" class="btn btn-hero-primary btn-lg px-4 py-3 d-inline-flex align-items-center justify-content-center gap-2">
                    <span>{{ __('messages.home.hero.btn_catalog') }}</span>
                    <i class="bi bi-arrow-right fs-5"></i>
                </a>
                <a href="#featured" class="btn btn-hero-secondary btn-lg px-4 py-3 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-stars"></i>
                    <span>{{ __('messages.home.hero.btn_featured') }}</span>
                </a>
            </div>

            <!-- Hero Trust Metrics Strip (Phase 4.2 Bolder) -->
            <div class="hero-metrics-strip mt-5 mx-auto" role="region" aria-label="Métricas de confianza y garantía">
                <div class="row g-2 g-md-3 justify-content-center align-items-center">
                    <div class="col-12 col-md-4">
                        <div class="hero-metric-item">
                            <div class="metric-icon-wrap" aria-hidden="true">
                                <i class="bi bi-moon-stars-fill"></i>
                            </div>
                            <div class="metric-text-wrap">
                                <span class="metric-number tabular-nums">{{ __('messages.home.hero.metrics.restful_value') }}</span>
                                <span class="metric-label">{{ __('messages.home.hero.metrics.restful_label') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="hero-metric-item">
                            <div class="metric-icon-wrap" aria-hidden="true">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="metric-text-wrap">
                                <span class="metric-number tabular-nums">{{ __('messages.home.hero.metrics.trial_value') }}</span>
                                <span class="metric-label">{{ __('messages.home.hero.metrics.trial_label') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="hero-metric-item">
                            <div class="metric-icon-wrap" aria-hidden="true">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div class="metric-text-wrap">
                                <span class="metric-number tabular-nums">{{ __('messages.home.hero.metrics.shipping_value') }}</span>
                                <span class="metric-label">{{ __('messages.home.hero.metrics.shipping_label') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Sleep Finder (Discovery Workflow) -->
    <section id="sleep-finder" class="sleep-finder-wrapper" aria-label="{{ __('messages.home.finder.title') }}">
        <div class="container">
            <div class="sleep-finder-card">
                <div class="row align-items-center mb-4">
                    <div class="col-lg-8">
                        <h2 class="h3 fw-bold mb-1 text-navy">{{ __('messages.home.finder.title') }}</h2>
                        <p class="text-muted mb-0 small">{{ __('messages.home.finder.subtitle') }}</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-2 mt-lg-0">
                        <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold small">
                            <i class="bi bi-patch-check-fill text-primary me-1"></i> Asesor Anatómico Reposa+
                        </span>
                    </div>
                </div>

                <div class="row g-4 align-items-stretch">
                    <!-- Step 1: Sleeping Posture -->
                    <div class="col-lg-5">
                        <div class="finder-step-box">
                            <div>
                                <label class="form-label fw-bold small text-uppercase text-muted mb-2 d-block">
                                    {{ __('messages.home.finder.step1_title') }}
                                </label>
                                <div class="finder-pill-grid" role="group" aria-label="{{ __('messages.home.finder.step1_title') }}">
                                    <button type="button" 
                                            class="finder-pill-btn js-posture-btn active" 
                                            data-posture="side" 
                                            data-recommended-firmness="Media-Alta"
                                            aria-pressed="true">
                                        <i class="bi bi-layout-sidebar-inset"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.posture_side') }}</span>
                                        <span class="pill-hint">{{ __('messages.home.finder.posture_side_desc') }}</span>
                                    </button>
                                    <button type="button" 
                                            class="finder-pill-btn js-posture-btn" 
                                            data-posture="back" 
                                            data-recommended-firmness="Media"
                                            aria-pressed="false">
                                        <i class="bi bi-shield-shaded"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.posture_back') }}</span>
                                        <span class="pill-hint">{{ __('messages.home.finder.posture_back_desc') }}</span>
                                    </button>
                                    <button type="button" 
                                            class="finder-pill-btn js-posture-btn" 
                                            data-posture="stomach" 
                                            data-recommended-firmness="Suave"
                                            aria-pressed="false">
                                        <i class="bi bi-feather"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.posture_stomach') }}</span>
                                        <span class="pill-hint">{{ __('messages.home.finder.posture_stomach_desc') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Firmness Preference -->
                    <div class="col-lg-4">
                        <div class="finder-step-box">
                            <div>
                                <label class="form-label fw-bold small text-uppercase text-muted mb-2 d-block">
                                    {{ __('messages.home.finder.step2_title') }}
                                </label>
                                <div class="finder-pill-grid" role="group" aria-label="{{ __('messages.home.finder.step2_title') }}">
                                    <button type="button" 
                                            class="finder-pill-btn js-firmness-btn" 
                                            data-firmness="Suave"
                                            aria-pressed="false">
                                        <i class="bi bi-cloud"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.firmness_soft') }}</span>
                                        <span class="pill-hint">Efecto nube</span>
                                    </button>
                                    <button type="button" 
                                            class="finder-pill-btn js-firmness-btn" 
                                            data-firmness="Media"
                                            aria-pressed="false">
                                        <i class="bi bi-bullseye"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.firmness_medium') }}</span>
                                        <span class="pill-hint">Equilibrio</span>
                                    </button>
                                    <button type="button" 
                                            class="finder-pill-btn js-firmness-btn active" 
                                            data-firmness="Media-Alta"
                                            aria-pressed="true">
                                        <i class="bi bi-layers-half"></i>
                                        <span class="pill-label">{{ __('messages.home.finder.firmness_firm') }}</span>
                                        <span class="pill-hint">Soporte firme</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Action Button -->
                    <div class="col-lg-3">
                        <div class="finder-step-box bg-white border-0 shadow-none p-0 d-flex flex-column justify-content-center">
                            <a id="finder-action-link" 
                               href="/catalog?firmness=Media-Alta" 
                               class="btn-finder-action d-flex align-items-center justify-content-center gap-2">
                                <span id="finder-action-text">{{ __('messages.home.finder.btn_submit') }}</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </a>
                            <p class="text-muted text-center mt-2 mb-0" style="font-size: 0.75rem;">
                                <i class="bi bi-shield-check text-success me-1"></i>100 noches de prueba sin compromiso
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section (Atmospheric Photography & Highlights) -->
    <section class="section-spacing bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold mb-2">{{ __('messages.home.categories.title') }}</h2>
                <p class="text-muted mx-auto" style="max-width: 60ch;">{{ __('messages.home.categories.subtitle') }}</p>
                <div class="bg-secondary mx-auto mt-3" style="height: 3px; width: 50px; border-radius: 2px;"></div>
            </div>

            @php
                $categoryMeta = [
                    'cervical' => [
                        'image' => '/images/products/pillow_cervical.png',
                        'badge' => __('messages.home.categories_benefits.cervical'),
                        'desc' => __('messages.home.categories_benefits.cervical_desc'),
                    ],
                    'viscoelastica' => [
                        'image' => '/images/products/pillow_viscoelastica.png',
                        'badge' => __('messages.home.categories_benefits.viscoelastica'),
                        'desc' => __('messages.home.categories_benefits.viscoelastica_desc'),
                    ],
                    'termica' => [
                        'image' => '/images/products/pillow_gel.png',
                        'badge' => __('messages.home.categories_benefits.termica'),
                        'desc' => __('messages.home.categories_benefits.termica_desc'),
                    ],
                    'anti-ronquidos' => [
                        'image' => '/images/products/pillow_cervical.png',
                        'badge' => __('messages.home.categories_benefits.anti_ronquidos'),
                        'desc' => __('messages.home.categories_benefits.anti_ronquidos_desc'),
                    ],
                    'latex' => [
                        'image' => '/images/products/pillow_gel.png',
                        'badge' => __('messages.home.categories_benefits.latex'),
                        'desc' => __('messages.home.categories_benefits.latex_desc'),
                    ],
                    'espuma-con-memoria' => [
                        'image' => '/images/products/pillow_viscoelastica.png',
                        'badge' => __('messages.home.categories_benefits.espuma_con_memoria'),
                        'desc' => __('messages.home.categories_benefits.espuma_con_memoria_desc'),
                    ],
                    'viaje' => [
                        'image' => '/images/products/pillow_viaje.png',
                        'badge' => __('messages.home.categories_benefits.viaje'),
                        'desc' => __('messages.home.categories_benefits.viaje_desc'),
                    ],
                    'infantil' => [
                        'image' => '/images/products/pillow_antiacaros.png',
                        'badge' => __('messages.home.categories_benefits.infantil'),
                        'desc' => __('messages.home.categories_benefits.infantil_desc'),
                    ],
                ];
            @endphp

            <div class="row g-4 justify-content-center">
                @foreach($categories as $category)
                    @php
                        $meta = $categoryMeta[$category->slug] ?? [
                            'image' => '/images/pillow-detail.png',
                            'badge' => 'Reposa+',
                            'desc' => 'Confort anatómico diseñado para tu bienestar diario',
                        ];
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="/catalog?category={{ $category->slug }}" class="category-photo-link" aria-label="{{ $category->name }} - {{ $meta['badge'] }}">
                            <div class="category-photo-card shadow-sm">
                                <div class="category-photo-wrapper">
                                    <img src="{{ $meta['image'] }}" 
                                         alt="{{ $category->name }}" 
                                         loading="lazy">
                                    <div class="category-overlay"></div>
                                    <span class="category-badge-chip">
                                        <i class="bi bi-stars me-1"></i>{{ $meta['badge'] }}
                                    </span>
                                </div>
                                <div class="category-card-body">
                                    <h3 class="category-name">{{ $category->name }}</h3>
                                    <p class="category-desc">{{ $meta['desc'] }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="section-spacing bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold mb-2">{{ __('messages.home.featured.title') }}</h2>
                <p class="text-muted mx-auto" style="max-width: 60ch;">{{ __('messages.home.featured.subtitle') }}</p>
                <div class="bg-secondary mx-auto mt-3" style="height: 3px; width: 50px; border-radius: 2px;"></div>
            </div>
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-sm-6 col-lg-3">
                        <x-product-card :product="$product" :showFavorite="false" />
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="/catalog" class="btn btn-primary btn-cta-bold px-5 py-3 fw-bold">{{ __('messages.home.featured.btn_all') }}</a>
            </div>
        </div>
    </section>

    <!-- Trust & Rest Guarantee Pillars (Phase 4.2 Bolder) -->
    <section class="section-spacing bg-white border-top border-light-subtle" aria-label="{{ __('messages.home.trust.section_title') }}">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold mb-2 text-navy">{{ __('messages.home.trust.section_title') }}</h2>
                <p class="text-muted mx-auto" style="max-width: 60ch;">{{ __('messages.home.trust.section_subtitle') }}</p>
                <div class="bg-secondary mx-auto mt-3" style="height: 3px; width: 50px; border-radius: 2px;"></div>
            </div>
            
            <x-trust-seals variant="cards" />
        </div>
    </section>

    <!-- Accessible & Lightweight Sleep Finder Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const postureButtons = document.querySelectorAll('.js-posture-btn');
            const firmnessButtons = document.querySelectorAll('.js-firmness-btn');
            const actionLink = document.getElementById('finder-action-link');

            let selectedFirmness = 'Media-Alta';

            function updateActionLink() {
                if (actionLink) {
                    actionLink.href = '/catalog?firmness=' + encodeURIComponent(selectedFirmness);
                }
            }

            postureButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    postureButtons.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-pressed', 'false');
                    });
                    this.classList.add('active');
                    this.setAttribute('aria-pressed', 'true');

                    const recommendedFirmness = this.getAttribute('data-recommended-firmness');
                    if (recommendedFirmness) {
                        firmnessButtons.forEach(fb => {
                            const isMatch = fb.getAttribute('data-firmness') === recommendedFirmness;
                            fb.classList.toggle('active', isMatch);
                            fb.setAttribute('aria-pressed', isMatch ? 'true' : 'false');
                            if (isMatch) {
                                selectedFirmness = recommendedFirmness;
                            }
                        });
                        updateActionLink();
                    }
                });
            });

            firmnessButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    firmnessButtons.forEach(b => {
                        b.classList.remove('active');
                        b.setAttribute('aria-pressed', 'false');
                    });
                    this.classList.add('active');
                    this.setAttribute('aria-pressed', 'true');
                    selectedFirmness = this.getAttribute('data-firmness');
                    updateActionLink();
                });
            });
        });
    </script>
@endsection

