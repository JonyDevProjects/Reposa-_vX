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
                    <span class="badge bg-success position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill shadow">
                        <i class="bi bi-check-circle me-1"></i>{{ __('messages.product.in_stock') ?? 'En stock' }}
                    </span>
                </div>
                <div class="row mt-3 g-2">
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-truck text-primary fs-4"></i>
                            <small class="d-block mt-1 fw-semibold text-dark" style="font-size: 0.7rem;">{{ __('messages.product.free_shipping') }}</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-shield-check text-primary fs-4"></i>
                            <small class="d-block mt-1 fw-semibold text-dark" style="font-size: 0.7rem;">{{ __('messages.product.trial_days') }}</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0 bg-light rounded-3 p-3 text-center h-100">
                            <i class="bi bi-award text-primary fs-4"></i>
                            <small class="d-block mt-1 fw-semibold text-dark" style="font-size: 0.7rem;">{{ __('messages.product.certified') ?? 'Calidad certificada' }}</small>
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
                        <span class="text-muted">{{ __('messages.product.reviews_count') }}</span>
                    </div>

                    <h2 class="display-6 fw-bold text-primary mb-4">{{ number_format($product->price, 2) }}€</h2>

                    <div class="mb-4">
                        <h6 class="fw-bold">{{ __('messages.product.specs_title') }}</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.material') }}</strong> {{ $product->material }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.firmness') }}</strong> {{ $product->firmness }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>{{ __('messages.product.dimensions') }}</strong> {{ $product->dimensions }}</li>
                        </ul>
                    </div>

                    <p class="text-muted mb-5 lead">{{ $product->description }}</p>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-3 mb-5">
                        @csrf
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                            <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary flex-grow-1 py-3 fw-bold">
                            <i class="bi bi-cart-plus me-2"></i>{{ __('messages.product.add_to_cart') }}
                        </button>
                        @php
                            $isFav = Auth::check() && Auth::user()->favorites->contains($product->id);
                        @endphp
                        <button type="button" 
                                class="btn {{ $isFav ? 'btn-danger text-white' : 'btn-outline-danger' }} py-3 px-4 btn-favorite" 
                                data-product-id="{{ $product->id }}"
                                data-url="{{ route('favorites.toggle', $product) }}"
                                title="{{ __('messages.footer.favorites') }}">
                            <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
                    </form>

                    <div class="card bg-light border-0 p-4 rounded-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-truck fs-3 text-primary me-3"></i>
                                    <div>
                                        <small class="d-block fw-bold">{{ __('messages.product.free_shipping') }}</small>
                                        <small class="text-muted">{{ __('messages.product.free_shipping_desc') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-return-left fs-3 text-primary me-3"></i>
                                    <div>
                                        <small class="d-block fw-bold">{{ __('messages.product.trial_days') }}</small>
                                        <small class="text-muted">{{ __('messages.product.free_returns') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
