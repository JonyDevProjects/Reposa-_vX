@extends('layouts.app')

@section('title', __('messages.cart.title'))

@section('content')
<div class="container py-5">
    @php
        $totalItemsCount = $cartItems->sum('quantity');
        $freeShippingThreshold = 50.0;
        $isFreeShipping = $total >= $freeShippingThreshold;
        $remainingForFreeShipping = max(0, $freeShippingThreshold - $total);
        $shippingProgress = min(100, round(($total / $freeShippingThreshold) * 100));
        $subtotalNet = round($total / 1.21, 2);
        $taxVat = round($total - $subtotalNet, 2);
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 fw-bold text-navy mb-1">
                <i class="bi bi-cart3 me-2 text-primary" aria-hidden="true"></i>{{ __('messages.cart.title') }}
            </h1>
            <p class="text-muted small mb-0">
                @if(!$cartItems->isEmpty())
                    {{ $totalItemsCount }} {{ $totalItemsCount === 1 ? 'almohada en tu cesta de descanso' : 'almohadas en tu cesta de descanso' }}
                @else
                    {{ __('messages.app.tagline') }}
                @endif
            </p>
        </div>

        @if(!$cartItems->isEmpty())
            <a href="/catalog" class="btn btn-outline-secondary btn-sm rounded-pill px-3 text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>{{ __('messages.cart.view_catalog') }}
            </a>
        @endif
    </div>

    @if($cartItems->isEmpty())
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <x-empty-state 
                icon="bi-moon-stars"
                :title="__('messages.cart.empty_title')"
                :description="__('messages.cart.empty_subtitle')"
                :highlight="__('messages.cart.empty_social_proof')"
                actionUrl="/catalog"
                :actionText="__('messages.cart.empty_btn_catalog')"
                actionIcon="bi-arrow-right"
                secondaryUrl="/#sleep-finder"
                :secondaryText="__('messages.cart.empty_btn_advisor')"
                secondaryIcon="bi-stars"
            />
        </div>
    @else
        <div class="row g-4 align-items-start">
            <!-- Left Column: Cart Items (Defensive & Robust Layout) -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 cart-table">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th scope="col" class="ps-4 py-3">{{ __('messages.cart.product') }}</th>
                                    <th scope="col" class="text-center py-3" style="width: 160px;">{{ __('messages.cart.quantity') }}</th>
                                    <th scope="col" class="text-end py-3 d-none d-md-table-cell">{{ __('messages.cart.unit_price') }}</th>
                                    <th scope="col" class="text-end pe-4 py-3">{{ __('messages.cart.subtotal_col') }}</th>
                                    <th scope="col" class="text-center py-3" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr>
                                    <!-- Product Info & Safe Clamping -->
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <a href="{{ route('products.show', $item->product) }}" class="flex-shrink-0 d-block overflow-hidden rounded-3 border border-light-subtle bg-light" style="width: 64px; height: 64px;">
                                                <img src="{{ $item->product->image_url ?: '/images/product-placeholder.svg' }}" 
                                                     onerror="this.onerror=null; this.src='/images/product-placeholder.svg';"
                                                     alt="{{ $item->product->name }}" 
                                                     class="w-100 h-100 object-fit-cover"
                                                     width="64"
                                                     height="64"
                                                     loading="lazy">
                                            </a>
                                            <div class="min-w-0 flex-grow-1 pe-2" style="max-width: 320px;">
                                                <h3 class="h6 mb-1 fw-bold text-truncate-2">
                                                    <a href="{{ route('products.show', $item->product) }}" class="text-navy text-decoration-none hover-primary">
                                                        {{ $item->product->name }}
                                                    </a>
                                                </h3>
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    @if($item->product->material)
                                                        <span class="badge bg-light text-muted border border-light-subtle fw-normal small px-2 py-0" style="font-size: 0.72rem;">
                                                            {{ $item->product->material }}
                                                        </span>
                                                    @endif
                                                    @if($item->product->firmness)
                                                        <span class="badge bg-light text-muted border border-light-subtle fw-normal small px-2 py-0" style="font-size: 0.72rem;">
                                                            {{ $item->product->firmness }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Quantity Selector Form -->
                                    <td class="py-3 text-center">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                            @csrf
                                            <div class="input-group input-group-sm quantity-input-group" style="width: 110px;">
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock }}"
                                                       class="form-control text-center tabular-nums fw-semibold" 
                                                       aria-label="{{ __('messages.cart.quantity') }} para {{ $item->product->name }}">
                                                <button type="submit" 
                                                        class="btn btn-outline-primary border" 
                                                        title="Actualizar cantidad"
                                                        aria-label="Actualizar cantidad de {{ $item->product->name }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-3 text-end tabular-nums text-muted small d-none d-md-table-cell">
                                        {{ number_format($item->product->price, 2) }}€
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="py-3 text-end pe-4 fw-bold tabular-nums text-navy fs-6">
                                        {{ number_format($item->product->price * $item->quantity, 2) }}€
                                    </td>

                                    <!-- Delete Item Button -->
                                    <td class="py-3 text-center pe-3">
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.cart.remove_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-link text-danger p-1 text-decoration-none" 
                                                    title="{{ __('messages.cart.remove_tooltip') }}"
                                                    aria-label="Eliminar {{ $item->product->name }} del carrito">
                                                <i class="bi bi-trash3 fs-6"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Express Dispatch Assurance Strip -->
                <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-white border border-light-subtle shadow-2xs">
                    <div class="rounded-circle bg-indigo-subtle text-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <div class="small">
                        <span class="fw-bold text-navy d-block">{{ __('messages.cart.shipping_info') }}</span>
                        <span class="text-muted">Pedidos tramitados antes de las 14:00 h salen en expedición hoy mismo.</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary & Purchase Focus Mode -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-navy mb-3">{{ __('messages.cart.order_summary') }}</h2>

                        <!-- Free Shipping Dynamic Threshold Gauge -->
                        @if($isFreeShipping)
                            <div class="shipping-threshold-banner bg-success-subtle border border-success-subtle rounded-3 p-3 mb-4">
                                <div class="d-flex align-items-center gap-2 text-success fw-bold small">
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                    <span>{{ __('messages.cart.free_shipping_unlocked') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="shipping-threshold-banner bg-indigo-subtle border border-primary-subtle rounded-3 p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-navy">
                                        <i class="bi bi-truck text-primary me-1"></i>{{ __('messages.cart.shipping_express') }}
                                    </span>
                                    <span class="tabular-nums fw-bold text-primary">{{ number_format($total, 2) }}€ / 50.00€</span>
                                </div>
                                <div class="progress bg-white" style="height: 6px;" role="progressbar" aria-valuenow="{{ $shippingProgress }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: {{ $shippingProgress }}%;"></div>
                                </div>
                                <div class="mt-2 small text-muted">
                                    {{ __('messages.cart.free_shipping_threshold_remaining', ['amount' => number_format($remainingForFreeShipping, 2)]) }}
                                </div>
                            </div>
                        @endif

                        <!-- Transparent Cost Breakdown -->
                        <div class="cost-breakdown mb-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ __('messages.cart.subtotal_net') }}</span>
                                <span class="tabular-nums fw-semibold text-dark">{{ number_format($subtotalNet, 2) }}€</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ __('messages.cart.tax_included') }}</span>
                                <span class="tabular-nums text-muted">{{ number_format($taxVat, 2) }}€</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ __('messages.cart.shipping_express') }}</span>
                                <span class="text-success fw-bold">{{ __('messages.cart.free') }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Grand Total -->
                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="h5 fw-bold text-navy mb-0 d-block">{{ __('messages.cart.total') }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Impuestos y envío incluidos</small>
                            </div>
                            <x-price :amount="$total" size="lg" />
                        </div>

                        <!-- CTA Checkout Action -->
                        @guest
                            <x-button href="{{ route('cart.login') }}" size="lg" :pill="true" class="w-100 btn-cta-bold text-decoration-none py-3" icon="bi-box-arrow-in-right" iconPosition="right">
                                {{ __('messages.cart.btn_login') }}
                            </x-button>
                        @endguest

                        @auth
                            <x-button href="{{ route('stripe.checkout') }}" size="lg" :pill="true" class="w-100 btn-cta-bold text-decoration-none py-3" icon="bi-shield-lock-fill">
                                {{ __('messages.cart.btn_stripe') }}
                            </x-button>
                        @endauth

                        <!-- Trust Microcopy & Redirect Notice -->
                        <p class="text-center text-muted small mt-2 mb-3" style="font-size: 0.78rem;">
                            <i class="bi bi-shield-lock text-primary me-1"></i>{{ __('messages.cart.trust_redirect') }}
                        </p>

                        <!-- Trust Assurance Pillars -->
                        <div class="cart-trust-assurances p-3 rounded-3 bg-light border border-light-subtle">
                            <ul class="list-unstyled mb-0 text-navy" style="font-size: 0.8125rem;">
                                <li class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-shield-check text-success fs-5"></i>
                                    <span>{{ __('messages.cart.trust_ssl') }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-moon-stars text-primary fs-5"></i>
                                    <span>{{ __('messages.cart.trust_trial') }}</span>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-repeat text-indigo fs-5"></i>
                                    <span>{{ __('messages.cart.trust_returns') }}</span>
                                </li>
                            </ul>
                        </div>

                        <x-trust-seals variant="checkout" />
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
