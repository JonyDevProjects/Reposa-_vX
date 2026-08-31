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
                <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                    <form action="/catalog" method="GET" id="filter-form">
                        {{-- Preserve current search and sort --}}
                        @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                        @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                        <h5 class="fw-bold mb-4"><i class="bi bi-funnel me-2"></i>{{ __('messages.catalog.filters') }}</h5>

                        {{-- Search --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">{{ __('messages.catalog.search') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 bg-light" placeholder="{{ __('messages.catalog.search_placeholder') }}" value="{{ request('q') }}">
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="mb-4">
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
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">{{ __('messages.catalog.material') }}</label>
                            <select name="material" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">{{ __('messages.catalog.all') }}</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Firmness --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">{{ __('messages.catalog.firmness') }}</label>
                            <select name="firmness" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">{{ __('messages.catalog.all_firmness') }}</option>
                                @foreach($firmnesses as $firmness)
                                    <option value="{{ $firmness }}" {{ request('firmness') == $firmness ? 'selected' : '' }}>{{ $firmness }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">{{ __('messages.catalog.price_range') }}</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Mín" value="{{ request('min_price') }}" min="0" step="0.01" style="width: 80px;">
                                <span class="text-muted">—</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Máx" value="{{ request('max_price') }}" min="0" step="0.01" style="width: 80px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-funnel me-1"></i>{{ __('messages.catalog.apply_filters') }}
                        </button>
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
                                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('category'))
                            @php $cat = $categories->firstWhere('slug', request('category')); @endphp
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_category') }} {{ $cat?->name ?? request('category') }}
                                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('material'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_material') }} {{ request('material') }}
                                <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('firmness'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_firmness') }} {{ request('firmness') }}
                                <a href="{{ request()->fullUrlWithQuery(['firmness' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="badge bg-primary">
                                {{ __('messages.catalog.results_price') }} {{ request('min_price', '0') }}€ — {{ request('max_price', '∞') }}€
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
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
                                <div class="card card-product h-100 shadow-sm border-0">
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                        <img src="{{ $product->image_url ?? 'https://placehold.co/400x300/182447/ffffff?text=' . urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                                    </a>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-light text-primary border">{{ $product->material }}</span>
                                            <span class="text-muted small"><i class="bi bi-star-fill text-warning"></i> 4.8</span>
                                        </div>
                                        <h5 class="card-title fw-bold mb-1">
                                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                                @if(request('q'))
                                                    {!! str_ireplace(request('q'), '<mark>' . e(request('q')) . '</mark>', e($product->name)) !!}
                                                @else
                                                    {{ $product->name }}
                                                @endif
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted small mb-3">
                                            @if(request('q'))
                                                {!! str_ireplace(request('q'), '<mark>' . e(request('q')) . '</mark>', e(Str::limit($product->description, 60))) !!}
                                            @else
                                                {{ Str::limit($product->description, 60) }}
                                            @endif
                                        </p>
                                        @if($product->stock > 0 && $product->stock <= 5)
                                            <small class="text-warning fw-semibold"><i class="bi bi-box-seam me-1"></i>{{ __('messages.catalog.low_stock', ['count' => $product->stock]) }}</small>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                                            <span class="fs-4 fw-bold text-primary">{{ number_format($product->price, 2) }}€</span>
                                            <div class="d-flex gap-2">
                                                @if($product->stock > 0)
                                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-cart-plus"></i></button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-secondary btn-sm rounded-circle" disabled><i class="bi bi-cart-x"></i></button>
                                                @endif
                                                @auth
                                                    <form action="{{ route('favorites.toggle', $product) }}" method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm rounded-circle {{ in_array($product->id, $favoriteIds) ? 'btn-danger text-white' : 'btn-outline-danger' }}" title="{{ in_array($product->id, $favoriteIds) ? __('messages.catalog.remove_favorite') : __('messages.catalog.add_favorite') }}">
                                                            <i class="bi {{ in_array($product->id, $favoriteIds) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger rounded-circle" title="{{ __('messages.catalog.login_favorite') }}">
                                                        <i class="bi bi-heart"></i>
                                                    </a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
