@extends('layouts.app')

@section('title', 'Catálogo')

@section('content')
    <div class="bg-light py-4 border-bottom mb-5">
        <div class="container">
            <h1 class="fw-bold mb-0">{{ __('messages.catalog.title') }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 mt-2">
                    <li class="breadcrumb-item"><a href="/">{{ __('messages.catalog.breadcrumb.home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('messages.catalog.breadcrumb.catalog') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            <!-- Sidebar / Filters -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">{{ __('messages.catalog.filter.title') }}</h5>
                    <div class="list-group list-group-flush">
                        <a href="/catalog" class="list-group-item list-group-item-action border-0 px-0 {{ !request('category') ? 'text-primary fw-bold' : '' }}">
                            {{ __('messages.catalog.filter.all') }}
                        </a>
                        @foreach($categories as $category)
                            <a href="/catalog?category={{ $category->slug }}" 
                               class="list-group-item list-group-item-action border-0 px-0 {{ request('category') == $category->slug ? 'text-primary fw-bold' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-md-9">
                <!-- Cajas Visuales de Categorías (Cross-categorization) -->
                <div class="row g-3 mb-4">
                    @foreach($categories as $category)
                        <div class="col-6 col-md-4">
                            <a href="/catalog?category={{ $category->slug }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm text-center p-3 {{ request('category') == $category->slug ? 'bg-primary text-white' : 'bg-white text-dark hover-lift' }}">
                                    <h6 class="fw-bold mb-0">
                                        <i class="bi bi-tag-fill me-2 opacity-50"></i>{{ $category->name }}
                                    </h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

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
                                        <img src="https://placehold.co/400x300/182447/ffffff?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-light text-primary border">{{ $product->material }}</span>
                                                <span class="text-muted small"><i class="bi bi-star-fill text-warning"></i> 4.8</span>
                                            </div>
                                            <h5 class="card-title fw-bold mb-1">{{ $product->name }}</h5>
                                            <p class="card-text text-muted small mb-3">{{ Str::limit($product->description, 60) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fs-4 fw-bold text-primary">{{ number_format($product->price, 2) }}€</span>
                                                <div class="d-flex gap-2">
                                                    @php
                                                        $isFav = Auth::check() && Auth::user()->favorites->contains($product->id);
                                                    @endphp
                                                    <button type="button" 
                                                            class="btn {{ $isFav ? 'btn-danger text-white' : 'btn-outline-danger' }} btn-sm rounded-circle btn-favorite" 
                                                            data-product-id="{{ $product->id }}"
                                                            data-url="{{ route('favorites.toggle', $product) }}"
                                                            title="{{ __('messages.footer.favorites') }}">
                                                        <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                                    </button>
                                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-cart-plus"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
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
