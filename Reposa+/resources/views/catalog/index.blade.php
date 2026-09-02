@extends('layouts.app')

@section('title', __('messages.catalog.title'))

@section('content')
    <div class="bg-light py-4 border-bottom mb-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-0">{{ __('messages.catalog.title') }}</h1>
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
                <div class="d-flex gap-2 align-items-center">
                    @if(request('q') || request('category') || request('material') || request('firmness') || request('min_price') || request('max_price'))
                        <a href="/catalog" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle me-1"></i>{{ __('messages.catalog.clear_filters') }}
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-down-up me-1"></i>{{ __('messages.catalog.sort') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @php $currentSort = request('sort', 'newest'); @endphp
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
        <div class="row g-4">
            <!-- Sidebar / Filters -->
            <div class="col-md-3">
                <div class="filter-card sticky-sidebar">
                    <form action="/catalog" method="GET" id="filter-form">
                        {{-- Preserve current search and sort --}}
                        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                        <h2 class="h5 fw-bold mb-4"><i class="bi bi-funnel me-2"></i>{{ __('messages.catalog.filters') }}</h2>

                        {{-- Search --}}
                        <div class="filter-group mb-4">
                            <label for="catalog-search-input" class="form-label fw-semibold small text-muted">{{ __('messages.catalog.search') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" id="catalog-search-input" class="form-control border-start-0 bg-light" placeholder="{{ __('messages.catalog.search_placeholder') }}" value="{{ request('q') }}">
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="filter-group mb-4">
                            <label class="form-label fw-semibold small text-muted">{{ __('messages.catalog.category') }}</label>
                            <div class="list-group list-group-flush">
                                <a href="/catalog?{{ http_build_query(request()->except('category', 'page')) }}" class="list-group-item list-group-item-action border-0 px-0 {{ !request('category') ? 'text-primary fw-bold' : '' }}">
                                    {{ __('messages.catalog.filter.all') }}
                                </a>
                                @foreach($categories as $category)
                                    <a href="/catalog?{{ http_build_query(array_merge(request()->except('category', 'page'), ['category' => $category->slug])) }}"
                                       class="list-group-item list-group-item-action border-0 px-0 {{ request('category') == $category->slug ? 'text-primary fw-bold' : '' }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        {{-- Material --}}
                        <div class="filter-group mb-4">
                            <label for="catalog-material-select" class="form-label fw-semibold small text-muted">{{ __('messages.catalog.material') }}</label>
                            <select name="material" id="catalog-material-select" class="form-select form-select-sm">
                                <option value="">{{ __('messages.catalog.all') }}</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Firmness --}}
                        <div class="filter-group mb-4">
                            <label for="catalog-firmness-select" class="form-label fw-semibold small text-muted">{{ __('messages.catalog.firmness') }}</label>
                            <select name="firmness" id="catalog-firmness-select" class="form-select form-select-sm">
                                <option value="">{{ __('messages.catalog.all_firmness') }}</option>
                                @foreach($firmnesses as $firmness)
                                    <option value="{{ $firmness }}" {{ request('firmness') == $firmness ? 'selected' : '' }}>{{ $firmness }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="filter-group mb-4">
                            <label id="catalog-price-label" class="form-label fw-semibold small text-muted">{{ __('messages.catalog.price_range') }}</label>
                            <div class="d-flex gap-2 align-items-center" aria-labelledby="catalog-price-label">
                                <input type="number" name="min_price" id="catalog-min-price" aria-label="Precio mínimo" class="form-control form-control-sm tabular-nums" placeholder="Mín" value="{{ request('min_price') }}" min="0" step="0.01" style="width: 80px;">
                                <span class="text-muted" aria-hidden="true">—</span>
                                <input type="number" name="max_price" id="catalog-max-price" aria-label="Precio máximo" class="form-control form-control-sm tabular-nums" placeholder="Máx" value="{{ request('max_price') }}" min="0" step="0.01" style="width: 80px;">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bi bi-funnel me-1"></i>{{ __('messages.catalog.apply_filters') }}
                            </button>
                            @if(request('q') || request('category') || request('material') || request('firmness') || request('min_price') || request('max_price'))
                                <a href="/catalog" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('messages.catalog.clear_filters') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-md-9">
                {{-- Active filters badges --}}
                @if(request('q') || request('material') || request('firmness') || request('min_price') || request('max_price'))
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if(request('q'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_search') }} «{{ request('q') }}»
                                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none" aria-label="Eliminar filtro de búsqueda">&times;</a>
                            </span>
                        @endif
                        @if(request('category'))
                            @php $cat = $categories->firstWhere('slug', request('category')); @endphp
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_category') }} {{ $cat?->name ?? request('category') }}
                                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none" aria-label="Eliminar filtro de categoría">&times;</a>
                            </span>
                        @endif
                        @if(request('material'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_material') }} {{ request('material') }}
                                <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none" aria-label="Eliminar filtro de material">&times;</a>
                            </span>
                        @endif
                        @if(request('firmness'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_firmness') }} {{ request('firmness') }}
                                <a href="{{ request()->fullUrlWithQuery(['firmness' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none" aria-label="Eliminar filtro de firmeza">&times;</a>
                            </span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_price') }} {{ request('min_price', '0') }}€ — {{ request('max_price', '∞') }}€
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none" aria-label="Eliminar filtro de precio">&times;</a>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Results count --}}
                <p class="text-muted mb-3">{{ __('messages.catalog.results_count', ['count' => $products->total()]) }}</p>

                @if($products->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <h4 class="mt-3">{{ __('messages.catalog.empty.title') }}</h4>
                        <p class="text-muted">{{ __('messages.catalog.empty.desc') }}</p>
                        <a href="/catalog" class="btn btn-primary mt-3">{{ __('messages.catalog.empty.btn') }}</a>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($products as $product)
                            <div class="col-md-4">
                                <x-product-card :product="$product" :favoriteIds="$favoriteIds" :searchQuery="request('q')" />
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $products->appends(request()->input())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
