@extends('layouts.app')

@section('title', __('messages.nav.home'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center position-relative overflow-hidden">
        <div class="container position-relative z-1">
            <h1 class="display-3 fw-bold mb-3 text-white">{{ __('messages.home.hero.title') }}</h1>
            <p class="lead mb-5 text-white opacity-90">{{ __('messages.home.hero.subtitle') }}</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/catalog" class="btn btn-secondary btn-lg px-5">{{ __('messages.home.hero.btn_catalog') }}</a>
                <a href="#featured" class="btn btn-outline-light btn-lg px-5">{{ __('messages.home.hero.btn_featured') }}</a>
            </div>
        </div>
        <!-- Decorative elements could go here -->
    </section>

    <!-- Categories Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ __('messages.home.categories.title') }}</h2>
                <div class="bg-secondary mx-auto" style="height: 3px; width: 60px;"></div>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($categories as $category)
                    <div class="col-6 col-md-3">
                        <a href="/catalog?category={{ $category->slug }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center p-4 hover-lift">
                                <div class="mb-3 text-primary">
                                    <i class="bi bi-moon-stars fs-1"></i>
                                </div>
                                <h3 class="h5 fw-bold text-dark mb-0">{{ $category->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ __('messages.home.featured.title') }}</h2>
                <p class="text-muted">{{ __('messages.home.featured.subtitle') }}</p>
            </div>
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3">
                        <x-product-card :product="$product" :showFavorite="false" />
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="/catalog" class="btn btn-primary px-5">{{ __('messages.home.featured.btn_all') }}</a>
            </div>
        </div>
    </section>

    <!-- Value Propositions -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <i class="bi bi-truck fs-1 mb-3"></i>
                    <h3 class="h5 fw-bold">{{ __('messages.home.features.express.title') }}</h3>
                    <p class="opacity-75 small">{{ __('messages.home.features.express.desc') }}</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-shield-check fs-1 mb-3"></i>
                    <h3 class="h5 fw-bold">{{ __('messages.home.features.guarantee.title') }}</h3>
                    <p class="opacity-75 small">{{ __('messages.home.features.guarantee.desc') }}</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-heart-pulse fs-1 mb-3"></i>
                    <h3 class="h5 fw-bold">{{ __('messages.home.features.health.title') }}</h3>
                    <p class="opacity-75 small">{{ __('messages.home.features.health.desc') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
