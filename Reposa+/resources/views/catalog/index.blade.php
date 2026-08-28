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
                            <i class="bi bi-x-circle me-1"></i>Limpiar filtros
                        </a>
                    @endif
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-down-up me-1"></i>Ordenar
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @php $currentSort = request('sort', 'newest'); @endphp
                            <li><a class="dropdown-item {{ $currentSort === 'newest' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">Más recientes</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'price_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">Precio: menor a mayor</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'price_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">Precio: mayor a menor</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'name_asc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">Nombre: A-Z</a></li>
                            <li><a class="dropdown-item {{ $currentSort === 'name_desc' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">Nombre: Z-A</a></li>
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

                        <h5 class="fw-bold mb-4"><i class="bi bi-funnel me-2"></i>Filtros</h5>

                        {{-- Search --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Buscar</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 bg-light" placeholder="Nombre o descripción..." value="{{ request('q') }}">
                            </div>
                        </div>

                        {{-- Categories --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Categoría</label>
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
                            <label class="form-label fw-semibold small text-muted">Material</label>
                            <select name="material" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material }}" {{ request('material') == $material ? 'selected' : '' }}>{{ $material }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Firmness --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Firmeza</label>
                            <select name="firmness" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach($firmnesses as $firmness)
                                    <option value="{{ $firmness }}" {{ request('firmness') == $firmness ? 'selected' : '' }}>{{ $firmness }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Rango de precio (€)</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Mín" value="{{ request('min_price') }}" min="0" step="0.01" style="width: 80px;">
                                <span class="text-muted">—</span>
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Máx" value="{{ request('max_price') }}" min="0" step="0.01" style="width: 80px;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-funnel me-1"></i>Aplicar filtros
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
                                Buscar: «{{ request('q') }}»
                                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('category'))
                            @php $cat = $categories->firstWhere('slug', request('category')); @endphp
                            <span class="badge bg-primary">
                                Categoría: {{ $cat?->name ?? request('category') }}
                                <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('material'))
                            <span class="badge bg-primary">
                                Material: {{ request('material') }}
                                <a href="{{ request()->fullUrlWithQuery(['material' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('firmness'))
                            <span class="badge bg-primary">
                                Firmeza: {{ request('firmness') }}
                                <a href="{{ request()->fullUrlWithQuery(['firmness' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                        @if(request('min_price') || request('max_price'))
                            <span class="badge bg-primary">
                                Precio: {{ request('min_price', '0') }}€ — {{ request('max_price', '∞') }}€
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null, 'page' => null]) }}" class="text-white ms-1 text-decoration-none">&times;</a>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Results count --}}
                <p class="text-muted mb-3">{{ $products->total() }} producto{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}</p>

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
                                            <small class="text-warning fw-semibold"><i class="bi bi-box-seam me-1"></i>¡Quedan {{ $product->stock }}!</small>
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
                                                        <button type="submit" class="btn btn-sm rounded-circle {{ in_array($product->id, $favoriteIds) ? 'btn-danger text-white' : 'btn-outline-danger' }}" title="{{ in_array($product->id, $favoriteIds) ? 'Eliminar de favoritos' : 'Añadir a favoritos' }}">
                                                            <i class="bi {{ in_array($product->id, $favoriteIds) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger rounded-circle" title="Inicia sesión para añadir a favoritos">
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
