@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{ __('messages.catalog.breadcrumb.home') }}</a></li>
                <li class="breadcrumb-item"><a href="/catalog">{{ __('messages.catalog.breadcrumb.catalog') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4 position-relative">
                    <img src="{{ $product->image_url ?? '/images/pillow-detail.png' }}" class="img-fluid product-main-img" alt="{{ $product->name }}">
                    <x-badge-stock :stock="$product->stock" class="position-absolute top-0 end-0 m-3 shadow" />
                </div>
                <div class="row mt-3 g-2">
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-truck text-primary fs-4"></i>
                            <span class="d-block mt-1 fw-semibold text-dark fs-caption">{{ __('messages.product.free_shipping') }}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-shield-check text-primary fs-4"></i>
                            <span class="d-block mt-1 fw-semibold text-dark fs-caption">{{ __('messages.product.trial_days') }}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-award text-primary fs-4"></i>
                            <span class="d-block mt-1 fw-semibold text-dark fs-caption">{{ __('messages.product.certified') ?? 'Calidad certificada' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <div class="ps-md-4">
                    <div class="mb-3">
                        @foreach($product->categories as $category)
                            <span class="badge bg-light text-primary border me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <h1 class="display-5 fw-bold mb-3">{{ $product->name }}</h1>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="text-warning me-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span class="text-muted small">{{ __('messages.product.reviews_count') }}</span>
                    </div>

                    <div class="mb-2">
                        <x-price :amount="$product->price" size="xl" :splitDecimals="true" />
                    </div>

                    @if($product->stock > 0)
                        <p class="text-muted mb-4 small">
                            <i class="bi bi-box-seam me-1"></i>
                            @if($product->stock <= 5)
                                <span class="text-warning fw-semibold">{{ __('messages.catalog.show.last_units', ['count' => $product->stock]) }}</span>
                            @else
                                <span class="tabular-nums">{{ __('messages.catalog.show.units_available', ['count' => $product->stock]) }}</span>
                            @endif
                        </p>
                    @else
                        <p class="text-danger mb-4 fw-semibold small">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ __('messages.catalog.show.out_of_stock_notify') }}
                        </p>
                    @endif

                    <div class="mb-4">
                        <h2 class="h5 fw-bold mb-3">{{ __('messages.product.specs_title') }}</h2>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.material') }}</strong> {{ $product->material }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.firmness') }}</strong> {{ $product->firmness }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.dimensions') }}</strong> {{ $product->dimensions }}</li>
                        </ul>
                    </div>

                    <p class="text-muted mb-5 lead prose-reading">{{ $product->description }}</p>

                    <div class="d-flex flex-column flex-md-row gap-3 mb-5">
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-3 w-100">
                                @csrf
                                <div class="input-group" style="width: 130px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()" aria-label="Disminuir cantidad">-</button>
                                    <input type="number" name="quantity" class="form-control text-center tabular-nums" value="1" min="1" max="{{ $product->stock }}" aria-label="Cantidad">
                                    <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()" aria-label="Aumentar cantidad">+</button>
                                </div>
                                <button type="submit" class="btn btn-primary btn-cta-bold flex-grow-1 py-3 fw-bold">
                                    <i class="bi bi-cart-plus me-2"></i>{{ __('messages.product.add_to_cart') }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary flex-grow-1 py-3 fw-bold" disabled>
                                <i class="bi bi-cart-x me-2"></i>{{ __('messages.catalog.show.no_stock') }}
                            </button>
                        @endif

                        @auth
                            <form action="{{ route('favorites.toggle', $product) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }} py-3 px-4" title="{{ __('messages.footer.favorites') }}" aria-label="{{ __('messages.footer.favorites') }}">
                                    <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-danger py-3 px-4" title="{{ __('messages.footer.favorites') }}">
                                <i class="bi bi-heart"></i>
                            </a>
                        @endauth
                    </div>

                    <x-trust-seals variant="compact" />
                </div>
            </div>
        </div>
    </div>
@endsection
