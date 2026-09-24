@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @php
        $galleryImages = [
            [
                'url' => $product->image_url ?: '/images/pillow-detail.png',
                'label' => __('messages.mobile.gallery_badge_main'),
                'alt' => $product->name . ' - ' . __('messages.mobile.gallery_badge_main'),
            ],
            [
                'url' => '/images/products/pillow_cervical.png',
                'label' => __('messages.mobile.gallery_badge_ergonomic'),
                'alt' => $product->name . ' - ' . __('messages.mobile.gallery_badge_ergonomic'),
            ],
            [
                'url' => '/images/products/pillow_viscoelastica.png',
                'label' => __('messages.mobile.gallery_badge_cover'),
                'alt' => $product->name . ' - ' . __('messages.mobile.gallery_badge_cover'),
            ],
        ];
    @endphp

    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{ __('messages.catalog.breadcrumb.home') }}</a></li>
                <li class="breadcrumb-item"><a href="/catalog">{{ __('messages.catalog.breadcrumb.catalog') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Product Gallery (Touch-Optimized, CLS 0 & Multi-View) -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4 position-relative product-gallery-card">
                    <div class="product-gallery-viewport w-100" id="product-gallery-viewport" style="aspect-ratio: 1 / 1; width: 100%; min-height: 320px;">
                        @foreach($galleryImages as $index => $img)
                            <div class="product-gallery-slide {{ $index === 0 ? 'active' : '' }}" 
                                 id="gallery-slide-{{ $index }}" 
                                 data-index="{{ $index }}"
                                 style="aspect-ratio: 1 / 1; width: 100%;">
                                <img src="{{ $img['url'] }}" 
                                     onerror="this.onerror=null; this.src='/images/product-placeholder.svg';"
                                     class="img-fluid product-main-img w-100 object-fit-cover" 
                                     width="600" 
                                     height="600" 
                                     style="aspect-ratio: 1 / 1;"
                                     alt="{{ $img['alt'] }}"
                                     @if($index === 0) loading="eager" fetchpriority="high" @else loading="lazy" @endif
                                     decoding="async">
                                <span class="badge bg-light text-primary border border-primary-subtle position-absolute top-0 start-0 m-3 shadow-sm rounded-pill px-3 py-1 fw-semibold small">
                                    {{ $img['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <x-badge-stock :stock="$product->stock" class="position-absolute top-0 end-0 m-3 shadow" />
                </div>

                <!-- Accessible Touch Thumbnails & Navigation Indicator -->
                <div class="d-flex align-items-center justify-content-between gap-2 mt-3 product-gallery-thumbs" role="tablist" aria-label="{{ __('messages.mobile.gallery_label') }}">
                    <div class="d-flex gap-2">
                        @foreach($galleryImages as $index => $img)
                            <button type="button" 
                                    class="gallery-thumb-btn {{ $index === 0 ? 'active' : '' }}" 
                                    data-slide-index="{{ $index }}"
                                    role="tab"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-controls="gallery-slide-{{ $index }}"
                                    aria-label="{{ __('messages.mobile.gallery_thumbnail', ['number' => $index + 1, 'total' => count($galleryImages)]) }}">
                                <img src="{{ $img['url'] }}" 
                                     onerror="this.onerror=null; this.src='/images/product-placeholder.svg';"
                                     alt="{{ $img['label'] }}" 
                                     width="54" 
                                     height="54" 
                                     style="aspect-ratio: 1 / 1;"
                                     class="object-fit-cover rounded-2"
                                     loading="lazy"
                                     decoding="async">
                            </button>
                        @endforeach
                    </div>
                    <div class="gallery-touch-indicator text-muted small d-md-none">
                        <i class="bi bi-hand-index-thumb me-1"></i><span class="tabular-nums" id="gallery-counter">1 / {{ count($galleryImages) }}</span>
                    </div>
                </div>

                <div class="row mt-4 g-2">
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
                    <h1 class="display-5 fw-bold mb-3 text-break">{{ $product->name }}</h1>
                    
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

                    <!-- Purchase Actions Form (Observed by Sticky Purchase Bar) -->
                    <div class="d-flex flex-column flex-md-row gap-3 mb-5" id="product-actions-wrap">
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-3 w-100" id="main-buy-form">
                                @csrf
                                <div class="input-group" style="width: 136px;">
                                    <button class="btn btn-outline-secondary btn-touch-target" type="button" onclick="this.nextElementSibling.stepDown()" aria-label="{{ __('messages.catalog.show.decrease_qty') }}">-</button>
                                    <input type="number" name="quantity" class="form-control text-center tabular-nums fw-bold fs-6" value="1" min="1" max="{{ $product->stock }}" aria-label="{{ __('messages.cart.quantity') }}">
                                    <button class="btn btn-outline-secondary btn-touch-target" type="button" onclick="this.previousElementSibling.stepUp()" aria-label="{{ __('messages.catalog.show.increase_qty') }}">+</button>
                                </div>
                                <button type="submit" id="main-buy-btn" class="btn btn-primary btn-cta-bold flex-grow-1 py-3 fw-bold">
                                    <i class="bi bi-cart-plus me-2"></i>{{ __('messages.product.add_to_cart') }}
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary flex-grow-1 py-3 fw-bold" id="main-buy-btn" disabled>
                                <i class="bi bi-cart-x me-2"></i>{{ __('messages.catalog.show.no_stock') }}
                            </button>
                        @endif

                        @auth
                            <form action="{{ route('favorites.toggle', $product) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-favorite {{ $isFavorite ? 'btn-danger text-white' : 'btn-outline-danger' }} py-3 px-4 btn-touch-target" 
                                        data-url="{{ route('favorites.toggle', $product) }}"
                                        data-product-id="{{ $product->id }}"
                                        title="{{ $isFavorite ? __('messages.catalog.remove_favorite') : __('messages.catalog.add_favorite') }}" 
                                        aria-label="{{ $isFavorite ? __('messages.catalog.remove_favorite') : __('messages.catalog.add_favorite') }}">
                                    <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }} fs-5"></i>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-favorite btn-outline-danger py-3 px-4 btn-touch-target" title="{{ __('messages.catalog.login_favorite') }}" aria-label="{{ __('messages.catalog.login_favorite') }}">
                                <i class="bi bi-heart fs-5"></i>
                            </a>
                        @endauth
                    </div>

                    <x-trust-seals variant="compact" />
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 7: Sticky Purchase Bar for Mobile (<768px Viewports) -->
    <div id="sticky-purchase-bar" class="sticky-purchase-bar d-md-none" aria-label="{{ __('messages.mobile.sticky_bar_label') }}">
        <div class="sticky-purchase-inner container-fluid px-3 py-2">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <!-- Mini Thumbnail & Price -->
                <div class="d-flex align-items-center gap-2 min-w-0" style="max-width: 48%;">
                    <img src="{{ $product->image_url ?: '/images/pillow-detail.png' }}" 
                         onerror="this.onerror=null; this.src='/images/product-placeholder.svg';"
                         alt="{{ $product->name }}" 
                         width="44" 
                         height="44" 
                         style="aspect-ratio: 1 / 1;"
                         class="rounded-2 object-fit-cover border border-light-subtle flex-shrink-0"
                         loading="lazy"
                         decoding="async">
                    <div class="text-truncate">
                        <span class="d-block text-truncate fw-bold text-navy small">{{ $product->name }}</span>
                        <x-price :amount="$product->price" size="sm" />
                    </div>
                </div>

                <!-- Fast Sticky Add to Cart CTA -->
                <div class="flex-grow-1 d-flex justify-content-end">
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex align-items-center gap-2 m-0 w-100 justify-content-end" id="sticky-buy-form">
                            @csrf
                            <div class="input-group input-group-sm flex-nowrap" style="width: 86px;">
                                <button class="btn btn-outline-secondary btn-touch-target-sm px-2" type="button" onclick="this.nextElementSibling.stepDown()" aria-label="{{ __('messages.catalog.show.decrease_qty') }}">-</button>
                                <input type="number" name="quantity" class="form-control text-center tabular-nums p-0 fw-bold" value="1" min="1" max="{{ $product->stock }}" aria-label="{{ __('messages.cart.quantity') }}">
                                <button class="btn btn-outline-secondary btn-touch-target-sm px-2" type="button" onclick="this.previousElementSibling.stepUp()" aria-label="{{ __('messages.catalog.show.increase_qty') }}">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sticky-buy fw-bold px-3 py-2 text-nowrap">
                                <i class="bi bi-cart-plus me-1"></i>{{ __('messages.mobile.sticky_add_to_cart') }}
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary btn-sticky-buy fw-bold px-3 py-2 text-nowrap" disabled>
                            <i class="bi bi-cart-x me-1"></i>{{ __('messages.mobile.sticky_out_of_stock') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

