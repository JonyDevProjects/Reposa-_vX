@props([
    'product',
    'favoriteIds' => [],
    'searchQuery' => null,
    'showFavorite' => true,
])

@php
    $isFavorite = in_array($product->id, $favoriteIds);
    $imageUrl = $product->image_url ?: '/images/product-placeholder.svg';
    $productRoute = route('products.show', $product);
@endphp

<div {{ $attributes->merge(['class' => 'card card-product h-100 shadow-sm border-0 position-relative']) }}>
    <a href="{{ $productRoute }}" class="text-decoration-none text-dark d-block overflow-hidden" aria-label="{{ $product->name }}">
        <img src="{{ $imageUrl }}" 
             onerror="this.onerror=null; this.src='/images/product-placeholder.svg';"
             class="card-img-top object-fit-cover" 
             style="height: 220px; width: 100%; transition: transform 0.4s ease;"
             alt="{{ $product->name }}"
             loading="lazy">
    </a>

    <div class="card-body d-flex flex-column p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            @if($product->material)
                <span class="badge bg-light text-primary border border-primary-subtle fw-semibold">
                    {{ $product->material }}
                </span>
            @endif
            <span class="text-muted small ms-auto d-inline-flex align-items-center gap-1">
                <i class="bi bi-star-fill text-warning"></i> 4.8
            </span>
        </div>

        <h3 class="card-title fw-bold mb-1 fs-5 line-clamp-2" style="word-break: break-word; overflow-wrap: break-word; min-height: 2.6rem;">
            <a href="{{ $productRoute }}" class="text-decoration-none text-dark stretched-link">
                @if($searchQuery)
                    {!! str_ireplace($searchQuery, '<mark class="bg-warning-subtle text-dark p-0 rounded-1">' . e($searchQuery) . '</mark>', e($product->name)) !!}
                @else
                    {{ $product->name }}
                @endif
            </a>
        </h3>

        <p class="card-text text-muted small mb-3 flex-grow-1 line-clamp-3" style="word-break: break-word; overflow-wrap: break-word;">
            @php $descLimit = Str::limit($product->description, 90); @endphp
            @if($searchQuery)
                {!! str_ireplace($searchQuery, '<mark class="bg-warning-subtle text-dark p-0 rounded-1">' . e($searchQuery) . '</mark>', e($descLimit)) !!}
            @else
                {{ $descLimit }}
            @endif
        </p>

        @if($product->stock > 0 && $product->stock <= 5)
            <div class="mb-2">
                <small class="text-warning-emphasis fw-semibold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-seam me-1"></i>{{ __('messages.catalog.low_stock', ['count' => $product->stock]) }}
                </small>
            </div>
        @elseif($product->stock <= 0)
            <div class="mb-2">
                <small class="text-danger fw-semibold d-inline-flex align-items-center gap-1">
                    <i class="bi bi-x-circle me-1"></i>{{ __('messages.product.out_of_stock') ?? 'Agotado' }}
                </small>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center gap-2 mt-auto pt-2 position-relative" style="z-index: 2;">
            <x-price :amount="$product->price" size="lg" />

            <div class="d-flex gap-2 align-items-center">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" 
                                class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm btn-cart-add" 
                                style="width: 38px; height: 38px;"
                                aria-label="{{ __('messages.product.add_to_cart') ?? 'Añadir al carrito' }}"
                                title="{{ __('messages.product.add_to_cart') ?? 'Añadir al carrito' }}">
                            <i class="bi bi-cart-plus fs-6"></i>
                        </button>
                    </form>
                @else
                    <button class="btn btn-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm opacity-50" 
                            style="width: 38px; height: 38px;" 
                            disabled 
                            aria-label="{{ __('messages.product.out_of_stock') ?? 'Agotado' }}">
                        <i class="bi bi-cart-x fs-6"></i>
                    </button>
                @endif

                @if($showFavorite)
                    @auth
                        <form action="{{ route('favorites.toggle', $product) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-sm btn-favorite {{ $isFavorite ? 'btn-danger text-white' : 'btn-outline-danger' }}" 
                                    style="width: 38px; height: 38px;"
                                    data-url="{{ route('favorites.toggle', $product) }}"
                                    data-product-id="{{ $product->id }}"
                                    title="{{ $isFavorite ? __('messages.catalog.remove_favorite') : __('messages.catalog.add_favorite') }}"
                                    aria-label="{{ $isFavorite ? __('messages.catalog.remove_favorite') : __('messages.catalog.add_favorite') }}">
                                <i class="bi {{ $isFavorite ? 'bi-heart-fill' : 'bi-heart' }} fs-6"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" 
                           class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm btn-favorite" 
                           style="width: 38px; height: 38px;"
                           title="{{ __('messages.catalog.login_favorite') }}"
                           aria-label="{{ __('messages.catalog.login_favorite') }}">
                            <i class="bi bi-heart fs-6"></i>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</div>
