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
                            'badge' => __('messages.home.finder.default_category_badge'),
                            'desc' => __('messages.home.finder.default_category_desc'),
                        ];
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="/catalog?category={{ $category->slug }}" class="category-photo-link" aria-label="{{ $category->name }} - {{ $meta['badge'] }}">
                            <div class="category-photo-card shadow-sm">
                                <div class="category-photo-wrapper">
                                    <img src="{{ $meta['image'] }}" 
                                         alt="{{ $category->name }}" 
                                         width="320"
                                         height="200"
                                         style="aspect-ratio: 16 / 10; object-fit: cover;"
                                         loading="lazy"
                                         decoding="async">
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
@endsection

