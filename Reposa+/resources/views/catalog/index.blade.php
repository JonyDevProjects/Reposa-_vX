@extends('layouts.app')

@section('title', __('messages.catalog.title'))

@section('content')
    @php
        $activeCategory = request('category');
        $hasActiveFilters = request()->filled('category') || request()->filled('material') || request()->filled('firmness') || request()->filled('min_price') || request()->filled('max_price');
        $activeFilterCount = (request()->filled('category') ? 1 : 0) 
            + (request()->filled('material') ? 1 : 0) 
            + (request()->filled('firmness') ? 1 : 0) 
            + (request()->filled('min_price') || request()->filled('max_price') ? 1 : 0);
        $hasAnyFilter = request()->filled('q') || $hasActiveFilters;
        $currentSort = request('sort', 'newest');
    @endphp

    {{-- Breadcrumb & Title Area --}}
    <div class="bg-light py-3 py-md-4 border-bottom mb-3 mb-md-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fs-4 fs-md-2 fw-bold mb-1 text-navy">{{ __('messages.catalog.title') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">{{ __('messages.catalog.breadcrumb.home') }}</a></li>
                            <li class="breadcrumb-item active text-navy fw-medium" aria-current="page">{{ __('messages.catalog.breadcrumb.catalog') }}</li>
                            @if(request('q'))
                                <li class="breadcrumb-item active text-primary fw-medium">«{{ request('q') }}»</li>
                            @endif
                        </ol>
                    </nav>
                </div>
                
                {{-- Quick Clear Link if any filter is active --}}
                @if($hasAnyFilter)
                    <div>
                        <a href="/catalog" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-xs">
                            <i class="bi bi-trash3 me-1"></i>{{ __('messages.catalog.clear_filters') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container pb-5">
        {{-- Catalog Filter Hub & Toolbar --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white catalog-hub-card">
            {{-- Toolbar: Filters Toggle, Results Count & Sorting --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                {{-- Action Controls: Filters Button --}}
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm {{ $hasActiveFilters ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2" 
                            type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#secondaryFiltersPanel" 
                            aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}" 
                            aria-controls="secondaryFiltersPanel"
                            id="toggleFiltersBtn"
                            style="min-height: 44px;">
                        <i class="bi bi-sliders2"></i>
                        <span>{{ __('messages.catalog.secondary_filters_toggle') }}</span>
                        @if($activeFilterCount > 0)
                            <span class="badge bg-white text-primary rounded-pill">{{ $activeFilterCount }}</span>
                        @endif
                        <i class="bi bi-chevron-down small" id="filtersChevron" style="transition: transform 0.2s ease; {{ $hasActiveFilters ? 'transform: rotate(180deg);' : '' }}"></i>
                    </button>
                </div>

                {{-- Results Count & Sorting --}}
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <div class="text-muted small fw-medium tabular-nums d-none d-sm-inline">
                        {{ __('messages.catalog.results_count', ['count' => $products->total()]) }}
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle btn-sm rounded-pill px-2 px-sm-3 py-2 fw-semibold text-navy bg-white border shadow-2xs d-inline-flex align-items-center justify-content-center" 
                                type="button" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false"
                                style="min-height: 44px;">
                            <i class="bi bi-arrow-down-up me-1 text-primary flex-shrink-0"></i>
                            <span class="d-none d-sm-inline me-1">{{ __('messages.catalog.sort') }}:</span>
                            <span class="text-navy fw-bold text-truncate">{{ __('messages.catalog.sort.' . $currentSort) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                            <li><a class="dropdown-item py-2 {{ $currentSort === 'newest' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">{{ __('messages.catalog.sort.newest') }}</a></li>
                            <li><a class="dropdown-item py-2 {{ $currentSort === 'price_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">{{ __('messages.catalog.sort.price_asc') }}</a></li>
                            <li><a class="dropdown-item py-2 {{ $currentSort === 'price_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">{{ __('messages.catalog.sort.price_desc') }}</a></li>
                            <li><a class="dropdown-item py-2 {{ $currentSort === 'name_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">{{ __('messages.catalog.sort.name_asc') }}</a></li>
                            <li><a class="dropdown-item py-2 {{ $currentSort === 'name_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">{{ __('messages.catalog.sort.name_desc') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Active Filter Dismissible Chips (when any filter is active) --}}
            @if($hasAnyFilter)
                <div class="d-flex align-items-center gap-2 flex-wrap pt-3 mt-3 border-top">
                    <span class="text-muted small fw-semibold me-1">
                        <i class="bi bi-funnel me-1"></i>{{ __('messages.catalog.filter_active_tag') }}:
                    </span>

                    @if(request('q'))
                        <span class="active-filter-badge">
                            <i class="bi bi-search text-primary"></i> «{{ request('q') }}»
                            <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="filter-remove-btn" aria-label="Eliminar filtro de búsqueda">&times;</a>
                        </span>
                    @endif

                    @if(request('category'))
                        @php $currentCat = $categories->firstWhere('slug', request('category')); @endphp
                        <span class="active-filter-badge">
                            <i class="bi bi-tag-fill text-primary"></i> {{ $currentCat?->name ?? request('category') }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="filter-remove-btn" aria-label="Eliminar filtro de categoría">&times;</a>
                        </span>
                    @endif

                    @if(request('material'))
                        <span class="active-filter-badge">
                            <i class="bi bi-feather text-primary"></i> {{ request('material') }}
                            <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => null]) }}" class="filter-remove-btn" aria-label="Eliminar filtro de material">&times;</a>
                        </span>
                    @endif

                    @if(request('firmness'))
                        <span class="active-filter-badge">
                            <i class="bi bi-activity text-primary"></i> {{ request('firmness') }}
                            <a href="{{ request()->fullUrlWithQuery(['firmness' => null, 'page' => null]) }}" class="filter-remove-btn" aria-label="Eliminar filtro de firmeza">&times;</a>
                        </span>
                    @endif

                    @if(request('min_price') || request('max_price'))
                        <span class="active-filter-badge">
                            <i class="bi bi-cash-stack text-primary"></i> {{ request('min_price', '0') }}€ — {{ request('max_price', '∞') }}€
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="filter-remove-btn" aria-label="Eliminar filtro de precio">&times;</a>
                        </span>
                    @endif

                    <a href="/catalog" class="btn btn-link btn-sm text-danger text-decoration-none py-0 px-1 fw-semibold ms-auto">
                        <i class="bi bi-trash3 me-1"></i>{{ __('messages.catalog.clear_filters') }}
                    </a>
                </div>
            @endif

            {{-- Collapsible Filters Panel --}}
            <div class="collapse {{ $hasActiveFilters ? 'show' : '' }} mt-3 pt-3 border-top" id="secondaryFiltersPanel">
                <div class="p-3 rounded-3 secondary-filters-card bg-light border">
                    <form action="/catalog" method="GET" id="secondary-filter-form">
                        {{-- Preserve search & sort --}}
                        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                        <div class="row g-3 align-items-end">
                            {{-- Category Filter --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="catalog-category-select" class="form-label fw-semibold small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-tags text-primary"></i> {{ __('messages.catalog.category') }}
                                </label>
                                <select name="category" id="catalog-category-select" class="form-select form-select-sm rounded-3">
                                    <option value="">{{ __('messages.catalog.category_all') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Material Filter --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="catalog-material-select" class="form-label fw-semibold small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-feather text-primary"></i> {{ __('messages.catalog.material') }}
                                </label>
                                <select name="material" id="catalog-material-select" class="form-select form-select-sm rounded-3">
                                    <option value="">{{ __('messages.catalog.all') }}</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Firmness Filter --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="catalog-firmness-select" class="form-label fw-semibold small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-activity text-primary"></i> {{ __('messages.catalog.firmness') }}
                                </label>
                                <select name="firmness" id="catalog-firmness-select" class="form-select form-select-sm rounded-3">
                                    <option value="">{{ __('messages.catalog.all_firmness') }}</option>
                                    @foreach($firmnesses as $firmness)
                                        <option value="{{ $firmness }}" {{ request('firmness') == $firmness ? 'selected' : '' }}>{{ $firmness }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Price Range Filter --}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label id="catalog-price-label" class="form-label fw-semibold small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-currency-euro text-primary"></i> {{ __('messages.catalog.price_range') }}
                                </label>
                                <div class="input-group input-group-sm" aria-labelledby="catalog-price-label">
                                    <input type="number" name="min_price" id="catalog-min-price" aria-label="Precio mínimo" class="form-control rounded-start-3 tabular-nums" placeholder="Mín €" value="{{ request('min_price') }}" min="0" step="0.01">
                                    <span class="input-group-text bg-white text-muted border-start-0 border-end-0 px-2">—</span>
                                    <input type="number" name="max_price" id="catalog-max-price" aria-label="Precio máximo" class="form-control rounded-end-3 tabular-nums" placeholder="Máx €" value="{{ request('max_price') }}" min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end align-items-center gap-2 mt-3 pt-3 border-top">
                            @if($hasActiveFilters)
                                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'material' => null, 'firmness' => null, 'min_price' => null, 'max_price' => null, 'page' => null]) }}" 
                                   class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-medium d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('messages.catalog.clear_filters') }}
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs">
                                <i class="bi bi-funnel me-1"></i>{{ __('messages.catalog.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. Products Grid (Full-width 12-column Scaffolding) --}}
        @if($products->isEmpty())
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden p-3 p-md-4 mt-2">
                <x-empty-state 
                    icon="bi-search"
                    :title="request('q') ? __('messages.catalog.empty.search_title') : __('messages.catalog.empty.title')"
                    :description="request('q') ? __('messages.catalog.empty.search_subtitle', ['query' => request('q')]) : __('messages.catalog.empty.desc')"
                    actionUrl="/catalog"
                    :actionText="__('messages.catalog.empty.btn_reset')"
                    actionIcon="bi-arrow-repeat"
                >
                    <!-- Search Tips -->
                    <div class="mt-4 pt-4 border-top text-start" style="max-width: 580px; margin: 0 auto;">
                        <h4 class="h6 fw-bold text-navy mb-3">
                            <i class="bi bi-lightbulb text-warning me-2"></i>{{ __('messages.catalog.empty.tips_title') }}
                        </h4>
                        <ul class="list-unstyled text-muted small mb-0">
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="bi bi-check2 text-primary mt-1"></i>
                                <span>{{ __('messages.catalog.empty.tip_1') }}</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check2 text-primary mt-1"></i>
                                <span>{{ __('messages.catalog.empty.tip_2') }}</span>
                            </li>
                        </ul>
                    </div>
                </x-empty-state>
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterPanel = document.getElementById('secondaryFiltersPanel');
    const filterChevron = document.getElementById('filtersChevron');
    if (filterPanel && filterChevron) {
        filterPanel.addEventListener('hidden.bs.collapse', function() {
            filterChevron.style.transform = 'rotate(0deg)';
        });
        filterPanel.addEventListener('shown.bs.collapse', function() {
            filterChevron.style.transform = 'rotate(180deg)';
        });
        if (filterPanel.classList.contains('show')) {
            filterChevron.style.transform = 'rotate(180deg)';
        }
    }
});
</script>
@endpush
