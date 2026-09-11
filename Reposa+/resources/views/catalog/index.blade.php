@extends('layouts.app')

@section('title', __('messages.catalog.title'))

@section('content')
    @php
        $activeCategory = request('category');
        $hasSecondaryFilters = request()->filled('material') || request()->filled('firmness') || request()->filled('min_price') || request()->filled('max_price');
        $activeSecondaryCount = (request()->filled('material') ? 1 : 0) 
            + (request()->filled('firmness') ? 1 : 0) 
            + (request()->filled('min_price') || request()->filled('max_price') ? 1 : 0);
        $hasAnyFilter = request()->filled('q') || request()->filled('category') || $hasSecondaryFilters;
        $currentSort = request('sort', 'newest');
    @endphp

    {{-- Breadcrumb & Title Area --}}
    <div class="bg-light py-4 border-bottom mb-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-0 text-navy">{{ __('messages.catalog.title') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2">
                            <li class="breadcrumb-item"><a href="/">{{ __('messages.catalog.breadcrumb.home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('messages.catalog.breadcrumb.catalog') }}</li>
                            @if(request('q'))
                                <li class="breadcrumb-item active text-primary">«{{ request('q') }}»</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                
                {{-- Quick Header Actions --}}
                <div class="d-flex gap-2 align-items-center">
                    @if($hasAnyFilter)
                        <a href="/catalog" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                            <i class="bi bi-x-circle me-1"></i>{{ __('messages.catalog.clear_filters') }}
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle btn-sm rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-arrow-down-up me-1"></i>{{ __('messages.catalog.sort') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                            <li><a class="dropdown-item {{ $currentSort === 'newest' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">{{ __('messages.catalog.sort.newest') }}</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'price_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">{{ __('messages.catalog.sort.price_asc') }}</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'price_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">{{ __('messages.catalog.sort.price_desc') }}</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'name_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">{{ __('messages.catalog.sort.name_asc') }}</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'name_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">{{ __('messages.catalog.sort.name_desc') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        {{-- 1. Prominent Search Toolbar & Fast Category Chips (1-Click) --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
            {{-- Search Bar --}}
            <form action="/catalog" method="GET" class="mb-3" id="catalog-search-form">
                {{-- Preserve current filters & sorting --}}
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('material'))<input type="hidden" name="material" value="{{ request('material') }}">@endif
                @if(request('firmness'))<input type="hidden" name="firmness" value="{{ request('firmness') }}">@endif
                @if(request('min_price'))<input type="hidden" name="min_price" value="{{ request('min_price') }}">@endif
                @if(request('max_price'))<input type="hidden" name="max_price" value="{{ request('max_price') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                <div class="input-group input-group-lg rounded-pill border bg-light overflow-hidden shadow-xs focus-within-shadow">
                    <span class="input-group-text bg-transparent border-0 ps-3 ps-md-4 text-primary">
                        <i class="bi bi-search fs-5"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           id="catalog-search-input" 
                           class="form-control bg-transparent border-0 py-3 ps-2 pe-3 text-navy" 
                           placeholder="{{ __('messages.catalog.search_smart_placeholder') }}" 
                           value="{{ request('q') }}"
                           aria-label="{{ __('messages.catalog.search') }}">
                    @if(request('q'))
                        <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" 
                           class="btn btn-link text-muted pe-3 d-flex align-items-center text-decoration-none" 
                           title="{{ __('messages.catalog.clear_search') }}"
                           aria-label="{{ __('messages.catalog.clear_search') }}">
                            <i class="bi bi-x-circle-fill fs-5 text-secondary"></i>
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary px-4 px-md-5 fw-semibold rounded-pill my-1 me-1">
                        <span class="d-none d-sm-inline">{{ __('messages.catalog.search') }}</span>
                        <i class="bi bi-arrow-right d-sm-none"></i>
                    </button>
                </div>
            </form>

            {{-- 2. Horizontal Category Chips (Category Chips in 1 Click) --}}
            <div class="d-flex align-items-center gap-2 pt-1">
                <span class="text-muted small fw-semibold d-none d-lg-inline text-nowrap me-1">
                    <i class="bi bi-tags me-1"></i>{{ __('messages.catalog.category') }}:
                </span>
                <div class="category-chips-wrapper d-flex gap-2 overflow-x-auto pb-1 no-scrollbar flex-grow-1 align-items-center">
                    {{-- All Categories Chip --}}
                    <a href="/catalog?{{ http_build_query(request()->except('category', 'page')) }}" 
                       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap transition-all {{ !$activeCategory ? 'btn-primary shadow-sm' : 'btn-light border bg-white text-secondary hover-lift' }}">
                        <i class="bi bi-grid-fill me-1"></i> {{ __('messages.catalog.category_chips_all') }}
                    </a>

                    @foreach($categories as $category)
                        @php $isCatActive = ($activeCategory === $category->slug); @endphp
                        <a href="/catalog?{{ http_build_query(array_merge(request()->except('category', 'page'), ['category' => $category->slug])) }}" 
                           class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap transition-all {{ $isCatActive ? 'btn-primary shadow-sm' : 'btn-light border bg-white text-secondary hover-lift' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Toolbar: Secondary Filters Toggle & Active Tags --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 mt-3 border-top">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Toggle Secondary Filters Panel --}}
                    <button class="btn btn-sm {{ $hasSecondaryFilters ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-2" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#secondaryFiltersPanel" 
                            aria-expanded="{{ $hasSecondaryFilters ? 'true' : 'false' }}" 
                            aria-controls="secondaryFiltersPanel"
                            id="toggleFiltersBtn">
                        <i class="bi bi-sliders2"></i>
                        <span>{{ __('messages.catalog.secondary_filters_toggle') }}</span>
                        @if($activeSecondaryCount > 0)
                            <span class="badge bg-white text-primary rounded-pill">{{ $activeSecondaryCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down small" id="filtersChevron"></i>
                    </button>

                    {{-- Active Filter Dismissible Chips --}}
                    @if(request('q'))
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-search text-primary"></i> «{{ request('q') }}»
                            <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="text-muted hover-danger ms-1 text-decoration-none" aria-label="Eliminar filtro de búsqueda">&times;</a>
                        </span>
                    @endif

                    @if(request('category'))
                        @php $currentCat = $categories->firstWhere('slug', request('category')); @endphp
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-tag-fill text-primary"></i> {{ $currentCat?->name ?? request('category') }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="text-muted hover-danger ms-1 text-decoration-none" aria-label="Eliminar filtro de categoría">&times;</a>
                        </span>
                    @endif

                    @if(request('material'))
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-feather text-primary"></i> {{ request('material') }}
                            <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => null]) }}" class="text-muted hover-danger ms-1 text-decoration-none" aria-label="Eliminar filtro de material">&times;</a>
                        </span>
                    @endif

                    @if(request('firmness'))
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-activity text-primary"></i> {{ request('firmness') }}
                            <a href="{{ request()->fullUrlWithQuery(['firmness' => null, 'page' => null]) }}" class="text-muted hover-danger ms-1 text-decoration-none" aria-label="Eliminar filtro de firmeza">&times;</a>
                        </span>
                    @endif

                    @if(request('min_price') || request('max_price'))
                        <span class="badge bg-light text-navy border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1">
                            <i class="bi bi-cash-stack text-primary"></i> {{ request('min_price', '0') }}€ — {{ request('max_price', '∞') }}€
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="text-muted hover-danger ms-1 text-decoration-none" aria-label="Eliminar filtro de precio">&times;</a>
                        </span>
                    @endif

                    @if($hasAnyFilter)
                        <a href="/catalog" class="btn btn-link btn-sm text-danger text-decoration-none py-0 px-1 fw-semibold">
                            <i class="bi bi-trash3 me-1"></i>{{ __('messages.catalog.clear_filters') }}
                        </a>
                    @endif
                </div>

                {{-- Results Count --}}
                <div class="text-muted small fw-semibold">
                    {{ __('messages.catalog.results_count', ['count' => $products->total()]) }}
                </div>
            </div>

            {{-- 3. Collapsible Secondary Filters Panel --}}
            <div class="collapse {{ $hasSecondaryFilters ? 'show' : '' }} mt-3 pt-3 border-top" id="secondaryFiltersPanel">
                <form action="/catalog" method="GET" id="secondary-filter-form">
                    {{-- Preserve search, category, sort --}}
                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                    <div class="row g-3 align-items-end">
                        {{-- Material Filter --}}
                        <div class="col-md-3">
                            <label for="catalog-material-select" class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-feather me-1"></i>{{ __('messages.catalog.material') }}
                            </label>
                            <select name="material" id="catalog-material-select" class="form-select form-select-sm rounded-3">
                                <option value="">{{ __('messages.catalog.all') }}</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Firmness Filter --}}
                        <div class="col-md-3">
                            <label for="catalog-firmness-select" class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-activity me-1"></i>{{ __('messages.catalog.firmness') }}
                            </label>
                            <select name="firmness" id="catalog-firmness-select" class="form-select form-select-sm rounded-3">
                                <option value="">{{ __('messages.catalog.all_firmness') }}</option>
                                @foreach($firmnesses as $firmness)
                                    <option value="{{ $firmness }}" {{ request('firmness') == $firmness ? 'selected' : '' }}>{{ $firmness }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range Filter --}}
                        <div class="col-md-4">
                            <label id="catalog-price-label" class="form-label fw-semibold small text-muted mb-1">
                                <i class="bi bi-currency-euro me-1"></i>{{ __('messages.catalog.price_range') }}
                            </label>
                            <div class="d-flex gap-2 align-items-center" aria-labelledby="catalog-price-label">
                                <input type="number" name="min_price" id="catalog-min-price" aria-label="Precio mínimo" class="form-control form-control-sm rounded-3 tabular-nums" placeholder="Mín" value="{{ request('min_price') }}" min="0" step="0.01">
                                <span class="text-muted" aria-hidden="true">—</span>
                                <input type="number" name="max_price" id="catalog-max-price" aria-label="Precio máximo" class="form-control form-control-sm rounded-3 tabular-nums" placeholder="Máx" value="{{ request('max_price') }}" min="0" step="0.01">
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-3 fw-semibold w-100">
                                <i class="bi bi-funnel me-1"></i>{{ __('messages.catalog.apply_filters') }}
                            </button>
                            @if($hasSecondaryFilters)
                                <a href="{{ request()->fullUrlWithQuery(['material' => null, 'firmness' => null, 'min_price' => null, 'max_price' => null, 'page' => null]) }}" 
                                   class="btn btn-outline-secondary btn-sm rounded-3" 
                                   title="{{ __('messages.catalog.clear_filters') }}">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. Signature Feature: Interactive Anatomical Firmness Guide (Collapsed by Default) --}}
        @include('catalog.partials.firmness-guide')

        {{-- 5. Products Grid (Full-width 12-column Scaffolding) --}}
        @if($products->isEmpty())
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden p-3 p-md-4 mt-2">
                <x-empty-state 
                    icon="bi-search"
                    :title="request('q') ? __('messages.catalog.empty.search_title') : __('messages.catalog.empty.title')"
                    :description="request('q') ? __('messages.catalog.empty.search_subtitle', ['query' => request('q')]) : __('messages.catalog.empty.desc')"
                    actionUrl="/catalog"
                    :actionText="__('messages.catalog.empty.btn_reset')"
                    actionIcon="bi-arrow-repeat"
                    secondaryUrl="#firmness-guide-section"
                    :secondaryText="__('messages.catalog.empty.btn_advisor')"
                    secondaryIcon="bi-stars"
                >
                    <!-- Search Tips & Fast Category Discovery -->
                    <div class="mt-4 pt-4 border-top text-start" style="max-width: 580px; margin: 0 auto;">
                        <h4 class="h6 fw-bold text-navy mb-3">
                            <i class="bi bi-lightbulb text-warning me-2"></i>{{ __('messages.catalog.empty.tips_title') }}
                        </h4>
                        <ul class="list-unstyled text-muted small mb-4">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="bi bi-check2 text-primary mt-1"></i>
                                <span>{{ __('messages.catalog.empty.tip_1') }}</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check2 text-primary mt-1"></i>
                                <span>{{ __('messages.catalog.empty.tip_2') }}</span>
                            </li>
                        </ul>

                        <!-- Direct Sleep Finder Banner Callout -->
                        <div class="p-3 rounded-3 bg-indigo-subtle border border-primary-subtle d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div>
                                <span class="fw-bold text-navy d-block small">{{ __('messages.catalog.empty.advisor_banner_title') }}</span>
                                <span class="text-muted" style="font-size: 0.78rem;">{{ __('messages.catalog.empty.advisor_banner_desc') }}</span>
                            </div>
                            <button type="button" 
                                    class="btn btn-primary btn-sm rounded-pill px-3 flex-shrink-0 text-decoration-none"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#advisorContentCollapse" 
                                    aria-expanded="true" 
                                    onclick="const el = document.getElementById('firmness-guide-section'); if (el) el.scrollIntoView({ behavior: 'smooth' });">
                                <i class="bi bi-stars me-1"></i>{{ __('messages.catalog.empty.btn_advisor') }}
                            </button>
                        </div>
                    </div>
                </x-empty-state>
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
                @foreach($products as $product)
                    <div class="col">
                        <x-product-card :product="$product" :favoriteIds="$favoriteIds" :searchQuery="request('q')" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
