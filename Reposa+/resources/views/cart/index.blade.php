@extends('layouts.app')

@section('title', __('messages.cart.title'))

@section('content')
<div class="container py-4 py-md-5">
    @php
        $totalItemsCount = $totals['items_count'] ?? $cartItems->sum('quantity');
        $freeShippingThreshold = $totals['free_shipping_threshold'] ?? 50.0;
        $isFreeShipping = $totals['is_free_shipping'] ?? ($total >= $freeShippingThreshold);
        $remainingForFreeShipping = $totals['remaining_for_free_shipping'] ?? max(0, $freeShippingThreshold - $total);
        $shippingProgress = $totals['shipping_progress'] ?? min(100, round(($total / $freeShippingThreshold) * 100));
        $subtotalNet = $totals['subtotal_net'] ?? round($total / 1.21, 2);
        $taxVat = $totals['tax_vat'] ?? round($total - $subtotalNet, 2);
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 fw-bold text-navy mb-1">
                <i class="bi bi-cart3 me-2 text-primary" aria-hidden="true"></i>{{ __('messages.cart.title') }}
            </h1>
            <p class="text-muted small mb-0">
                @if(!$cartItems->isEmpty())
                    <span id="cart-items-count-text">{{ $totalItemsCount }} {{ $totalItemsCount === 1 ? __('messages.cart.items_count_single') : __('messages.cart.items_count_plural') }}</span>
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
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden w-100">
            <x-empty-state 
                icon="bi-moon-stars"
                :title="__('messages.cart.empty_title')"
                :description="__('messages.cart.empty_subtitle')"
                :highlight="__('messages.cart.empty_social_proof')"
                actionUrl="/catalog"
                :actionText="__('messages.cart.empty_btn_catalog')"
                actionIcon="bi-arrow-right"
            />
        </div>
    @else
        <div class="row g-4 align-items-start">
            <!-- Left Column: Cart Items (Defensive & Robust Layout) -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="table-responsive cart-table-responsive">
                        <table class="table table-hover align-middle mb-0 cart-table">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th scope="col" class="ps-4 py-3">{{ __('messages.cart.product') }}</th>
                                    <th scope="col" class="text-center py-3" style="width: 170px;">{{ __('messages.cart.quantity') }}</th>
                                    <th scope="col" class="text-end py-3 d-none d-md-table-cell">{{ __('messages.cart.unit_price') }}</th>
                                    <th scope="col" class="text-end pe-4 py-3">{{ __('messages.cart.subtotal_col') }}</th>
                                    <th scope="col" class="text-center py-3" style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                <tr id="cart-item-row-{{ $item->id }}" 
                                    class="cart-item-row"
                                    data-item-id="{{ $item->id }}" 
                                    data-unit-price="{{ $item->product->price }}" 
                                    data-stock="{{ $item->product->stock }}"
                                    data-update-url="{{ route('cart.update', $item->id) }}">
                                    <!-- Product Info & Safe Clamping -->
                                    <td class="ps-4 py-3 cell-product">
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
                                            <div class="min-w-0 flex-grow-1 pe-2 cart-product-meta">
                                                <h3 class="h6 mb-1 fw-bold text-truncate-2 cart-item-title">
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
                                                <div class="text-muted small tabular-nums d-md-none mt-1" style="font-size: 0.78rem;">
                                                    {{ number_format($item->product->price, 2, ',', '.') }}€ / ud.
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Quantity Selector with Real-time Stepper -->
                                    <td class="py-3 text-center cell-quantity">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline-flex flex-column align-items-center justify-content-center js-qty-form mb-0">
                                            @csrf
                                            <div class="input-group input-group-sm quantity-input-group rounded-pill overflow-hidden border shadow-2xs bg-white" style="width: 120px;">
                                                <button type="button" 
                                                        class="btn btn-light px-2 border-0 text-navy js-qty-btn" 
                                                        data-action="decrement"
                                                        aria-label="Disminuir cantidad"
                                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock }}"
                                                       class="form-control text-center tabular-nums fw-bold border-0 px-1 js-qty-input no-spin" 
                                                       aria-label="{{ __('messages.cart.quantity') }} para {{ $item->product->name }}"
                                                       autocomplete="off">
                                                <button type="button" 
                                                        class="btn btn-light px-2 border-0 text-navy js-qty-btn" 
                                                        data-action="increment"
                                                        aria-label="Aumentar cantidad"
                                                        {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                            
                                            {{-- Fallback no-JS --}}
                                            <noscript>
                                                <button type="submit" class="btn btn-outline-primary btn-sm mt-1 px-2 py-0" style="font-size: 0.72rem;">
                                                    {{ __('messages.cart.updated') }}
                                                </button>
                                            </noscript>

                                            {{-- Real-time Status / Stock feedback --}}
                                            <div class="js-stock-feedback text-danger small mt-1 d-none" style="font-size: 0.72rem; line-height: 1.2;"></div>
                                            <div class="js-qty-loading small text-muted mt-1 d-none" style="font-size: 0.70rem;">
                                                <span class="spinner-border spinner-border-sm text-primary me-1" style="width: 0.65rem; height: 0.65rem;" role="status"></span>{{ __('messages.cart.updating') }}
                                            </div>
                                        </form>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="py-3 text-end tabular-nums text-muted small d-none d-md-table-cell cell-unit-price">
                                        {{ number_format($item->product->price, 2, ',', '.') }}€
                                    </td>

                                    <!-- Subtotal -->
                                    <td class="py-3 text-end pe-4 fw-bold tabular-nums text-navy fs-6 js-item-subtotal cell-subtotal" id="item-subtotal-{{ $item->id }}">
                                        {{ number_format($item->product->price * $item->quantity, 2, ',', '.') }}€
                                    </td>

                                    <!-- Delete Item Button -->
                                    <td class="py-3 text-center pe-3 cell-remove">
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="js-remove-form mb-0" data-confirm-msg="{{ __('messages.cart.remove_confirm') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-light text-danger rounded-circle p-2 d-inline-flex align-items-center justify-content-center js-remove-btn" 
                                                    style="width: 36px; height: 36px;"
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
                        <span class="text-muted">{{ __('messages.cart.shipping_dispatch_hint') }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary & Purchase Focus Mode -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-navy mb-3">{{ __('messages.cart.order_summary') }}</h2>

                        <!-- Free Shipping Dynamic Threshold Gauge Container -->
                        <div id="shipping-threshold-container">
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
                                        <span class="tabular-nums fw-bold text-primary" id="cart-shipping-ratio">{{ number_format($total, 2, ',', '.') }}€ / 50,00 €</span>
                                    </div>
                                    <div class="progress bg-white" style="height: 6px;" role="progressbar" aria-valuenow="{{ $shippingProgress }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-primary" id="cart-shipping-progress-bar" style="width: {{ $shippingProgress }}%;"></div>
                                    </div>
                                    <div class="mt-2 small text-muted" id="cart-shipping-remaining-text">
                                        {{ __('messages.cart.free_shipping_threshold_remaining', ['amount' => number_format($remainingForFreeShipping, 2, ',', '.')]) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Transparent Cost Breakdown -->
                        <div class="cost-breakdown mb-3">
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ __('messages.cart.subtotal_net') }}</span>
                                <span class="tabular-nums fw-semibold text-dark" id="cart-subtotal-net">{{ number_format($subtotalNet, 2, ',', '.') }}€</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">{{ __('messages.cart.tax_included') }}</span>
                                <span class="tabular-nums text-muted" id="cart-tax-vat">{{ number_format($taxVat, 2, ',', '.') }}€</span>
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
                                <small class="text-muted" style="font-size: 0.75rem;">{{ __('messages.cart.taxes_included') }}</small>
                            </div>
                            <div id="cart-grand-total">
                                <x-price :amount="$total" size="lg" />
                            </div>
                        </div>

                        <!-- Secondary Action: Volver al catálogo para continuar comprando -->
                        <x-button href="{{ route('catalog') }}" variant="outline-primary" size="md" :pill="true" class="w-100 text-decoration-none py-2 mb-2" icon="bi-arrow-left">
                            {{ __('messages.cart.continue_shopping') }}
                        </x-button>

                        <!-- CTA Checkout Action: Finalizar Pedido -->
                        <x-button href="{{ route('checkout.page') }}" size="lg" :pill="true" class="w-100 btn-cta-bold text-decoration-none py-3" icon="bi-shield-lock-fill">
                            {{ __('messages.cart.finalize_order') }}
                        </x-button>

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

<style>
.quantity-input-group input[type=number]::-webkit-inner-spin-button,
.quantity-input-group input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.quantity-input-group input[type=number] {
    -moz-appearance: textfield;
}
.cart-subtotal-highlight {
    animation: cartSubtotalPulse 0.35s ease-in-out;
}
@keyframes cartSubtotalPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.08); color: var(--bs-primary); }
    100% { transform: scale(1); }
}

@media (min-width: 768px) {
    .cart-product-meta {
        max-width: 320px;
    }
}

/* Mobile Narrow Screen Card Transformation */
@media (max-width: 767.98px) {
    .cart-table-responsive {
        overflow-x: visible !important;
    }
    .cart-table thead {
        display: none !important;
    }
    .cart-table, 
    .cart-table tbody {
        display: block !important;
        width: 100% !important;
    }
    .cart-table tr.cart-item-row {
        display: grid !important;
        grid-template-columns: 1fr auto !important;
        grid-template-rows: auto auto !important;
        row-gap: 0.875rem !important;
        column-gap: 0.75rem !important;
        padding: 1.125rem 1rem !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
        background-color: #ffffff !important;
        position: relative;
        --bs-table-accent-bg: transparent !important;
        --bs-table-bg: transparent !important;
        --bs-table-hover-bg: transparent !important;
    }
    .cart-table tr.cart-item-row:hover {
        background-color: #fafbfc !important;
    }
    .cart-table tr.cart-item-row:last-child {
        border-bottom: none !important;
    }
    .cart-table tr.cart-item-row > td {
        display: block !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }
    .cart-table tr.cart-item-row td.cell-product {
        grid-column: 1 !important;
        grid-row: 1 !important;
    }
    .cart-table tr.cart-item-row td.cell-remove {
        grid-column: 2 !important;
        grid-row: 1 !important;
        text-align: right !important;
        align-self: start !important;
    }
    .cart-table tr.cart-item-row td.cell-quantity {
        grid-column: 1 !important;
        grid-row: 2 !important;
        text-align: left !important;
        justify-self: start !important;
        align-self: center !important;
    }
    .cart-table tr.cart-item-row td.cell-quantity .js-qty-form {
        align-items: flex-start !important;
    }
    .cart-table tr.cart-item-row td.cell-unit-price {
        display: none !important;
    }
    .cart-table tr.cart-item-row td.cell-subtotal {
        grid-column: 2 !important;
        grid-row: 2 !important;
        text-align: right !important;
        align-self: center !important;
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        padding-right: 0 !important;
        white-space: nowrap !important;
    }
    .cart-product-meta {
        max-width: none !important;
    }
    .cart-item-title {
        text-transform: none !important;
        font-size: 0.95rem !important;
        letter-spacing: normal !important;
        line-height: 1.35 !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const activeAbortControllers = new Map();
    const debounceTimers = new Map();

    const isSpanish = (document.documentElement.lang || 'es').startsWith('es');
    const strings = {
        updating: isSpanish ? 'Actualizando cesta...' : 'Updating cart...',
        maxStock: isSpanish ? 'Stock máximo alcanzado (:count uds.)' : 'Maximum stock reached (:count pcs.)',
        minQty: isSpanish ? 'La cantidad mínima es 1 ud.' : 'Minimum quantity is 1 pc.',
        pillowSingle: isSpanish ? 'almohada en tu cesta de descanso' : 'pillow in your sleep basket',
        pillowPlural: isSpanish ? 'almohadas en tu cesta de descanso' : 'pillows in your sleep basket',
        freeShippingUnlocked: isSpanish ? '¡Felicidades! Tienes envío express 24/48h gratuito garantizado.' : 'Congratulations! You have unlocked free express 24/48h shipping.',
        freeShippingRemaining: isSpanish 
            ? '¡Solo te faltan :amount para conseguir envío gratuito 24/48h!' 
            : 'Only :amount left for free express 24/48h shipping!',
        shippingExpress: isSpanish ? 'Envío Express 24/48h' : 'Express 24/48h Shipping',
    };

    function updateNavBadges(count) {
        document.querySelectorAll('.js-cart-badge, #cart-badge').forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        });
    }

    function updateSummaryTotals(totals) {
        // 1. Base imponible
        const netEl = document.getElementById('cart-subtotal-net');
        if (netEl && totals.formatted?.subtotal_net) {
            netEl.textContent = totals.formatted.subtotal_net;
        }

        // 2. IVA
        const vatEl = document.getElementById('cart-tax-vat');
        if (vatEl && totals.formatted?.tax_vat) {
            vatEl.textContent = totals.formatted.tax_vat;
        }

        // 3. Total general
        const totalContainer = document.getElementById('cart-grand-total');
        if (totalContainer && totals.formatted?.total) {
            const priceFull = totalContainer.querySelector('.price-full');
            if (priceFull) {
                priceFull.textContent = totals.formatted.total;
            } else {
                totalContainer.innerHTML = `<span class="fw-bold price-full lh-1">${totals.formatted.total}</span>`;
            }
        }

        // 4. Conteo de almohadas en la cabecera
        const countTextEl = document.getElementById('cart-items-count-text');
        if (countTextEl) {
            const label = totals.items_count === 1 ? strings.pillowSingle : strings.pillowPlural;
            countTextEl.textContent = `${totals.items_count} ${label}`;
        }

        // 5. Umbral de envío gratuito
        const shippingContainer = document.getElementById('shipping-threshold-container');
        if (shippingContainer) {
            if (totals.is_free_shipping) {
                shippingContainer.innerHTML = `
                    <div class="shipping-threshold-banner bg-success-subtle border border-success-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex align-items-center gap-2 text-success fw-bold small">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <span>${strings.freeShippingUnlocked}</span>
                        </div>
                    </div>
                `;
            } else {
                const remainingText = strings.freeShippingRemaining.replace(':amount', totals.formatted.remaining_for_free_shipping);
                shippingContainer.innerHTML = `
                    <div class="shipping-threshold-banner bg-indigo-subtle border border-primary-subtle rounded-3 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1 small">
                            <span class="fw-semibold text-navy">
                                <i class="bi bi-truck text-primary me-1"></i>${strings.shippingExpress}
                            </span>
                            <span class="tabular-nums fw-bold text-primary" id="cart-shipping-ratio">${totals.formatted.total} / 50,00 €</span>
                        </div>
                        <div class="progress bg-white" style="height: 6px;" role="progressbar" aria-valuenow="${totals.shipping_progress}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-primary" id="cart-shipping-progress-bar" style="width: ${totals.shipping_progress}%;"></div>
                        </div>
                        <div class="mt-2 small text-muted" id="cart-shipping-remaining-text">
                            ${remainingText}
                        </div>
                    </div>
                `;
            }
        }
    }

    // Inicializar cada fila de producto
    document.querySelectorAll('.cart-item-row').forEach(row => {
        const itemId = row.dataset.itemId;
        const unitPrice = parseFloat(row.dataset.unitPrice || '0');
        const stock = parseInt(row.dataset.stock || '999', 10);
        const updateUrl = row.dataset.updateUrl;

        const input = row.querySelector('.js-qty-input');
        const btnMinus = row.querySelector('[data-action="decrement"]');
        const btnPlus = row.querySelector('[data-action="increment"]');
        const subtotalEl = row.querySelector('.js-item-subtotal');
        const feedbackEl = row.querySelector('.js-stock-feedback');
        const loadingEl = row.querySelector('.js-qty-loading');

        function updateButtonStates(qty) {
            if (btnMinus) btnMinus.disabled = qty <= 1;
            if (btnPlus) btnPlus.disabled = qty >= stock;
        }

        function setFeedback(msg = null) {
            if (!feedbackEl) return;
            if (msg) {
                feedbackEl.textContent = msg;
                feedbackEl.classList.remove('d-none');
            } else {
                feedbackEl.textContent = '';
                feedbackEl.classList.add('d-none');
            }
        }

        function setLoading(isLoading) {
            if (!loadingEl) return;
            loadingEl.classList.toggle('d-none', !isLoading);
        }

        function triggerOptimisticUpdate(qty) {
            if (subtotalEl) {
                const optimisticSubtotal = (unitPrice * qty).toFixed(2).replace('.', ',');
                subtotalEl.textContent = `${optimisticSubtotal} €`;
                subtotalEl.classList.add('cart-subtotal-highlight');
                setTimeout(() => subtotalEl.classList.remove('cart-subtotal-highlight'), 350);
            }
            updateButtonStates(qty);
        }

        function sendUpdate(qty) {
            setLoading(true);

            if (activeAbortControllers.has(itemId)) {
                activeAbortControllers.get(itemId).abort();
            }
            const controller = new AbortController();
            activeAbortControllers.set(itemId, controller);

            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ quantity: qty }),
                signal: controller.signal,
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                setLoading(false);
                setFeedback(null);

                if (subtotalEl && data.item?.subtotal_formatted) {
                    subtotalEl.textContent = data.item.subtotal_formatted;
                }

                if (input && data.item?.quantity) {
                    input.value = data.item.quantity;
                    updateButtonStates(data.item.quantity);
                }

                if (data.totals) {
                    updateSummaryTotals(data.totals);
                }

                if (typeof data.cart_count !== 'undefined') {
                    updateNavBadges(data.cart_count);
                }
            })
            .catch(err => {
                if (err.name === 'AbortError') {
                    return;
                }
                setLoading(false);

                if (err.error_code === 'exceeds_stock' && typeof err.max_stock !== 'undefined') {
                    input.value = err.max_stock;
                    updateButtonStates(err.max_stock);
                    const msg = err.message || strings.maxStock.replace(':count', err.max_stock);
                    setFeedback(msg);
                    triggerOptimisticUpdate(err.max_stock);
                    if (window.showToast) {
                        window.showToast('warning', msg);
                    }
                } else if (err.message) {
                    setFeedback(err.message);
                    if (window.showToast) {
                        window.showToast('error', err.message);
                    }
                }
            })
            .finally(() => {
                if (activeAbortControllers.get(itemId) === controller) {
                    activeAbortControllers.delete(itemId);
                }
            });
        }

        function scheduleUpdate(qty) {
            if (debounceTimers.has(itemId)) {
                clearTimeout(debounceTimers.get(itemId));
            }
            debounceTimers.set(itemId, setTimeout(() => {
                sendUpdate(qty);
            }, 250));
        }

        // Stepper: botón disminuir (-)
        if (btnMinus) {
            btnMinus.addEventListener('click', (e) => {
                e.preventDefault();
                let current = parseInt(input.value || '1', 10);
                if (current > 1) {
                    const next = current - 1;
                    input.value = next;
                    setFeedback(null);
                    triggerOptimisticUpdate(next);
                    scheduleUpdate(next);
                }
            });
        }

        // Stepper: botón aumentar (+)
        if (btnPlus) {
            btnPlus.addEventListener('click', (e) => {
                e.preventDefault();
                let current = parseInt(input.value || '1', 10);
                if (current < stock) {
                    const next = current + 1;
                    input.value = next;
                    setFeedback(null);
                    triggerOptimisticUpdate(next);
                    scheduleUpdate(next);
                } else {
                    const msg = strings.maxStock.replace(':count', stock);
                    setFeedback(msg);
                    if (window.showToast) {
                        window.showToast('warning', msg);
                    }
                }
            });
        }

        // Entrada manual en input
        if (input) {
            input.addEventListener('input', () => {
                let val = parseInt(input.value, 10);

                if (isNaN(val) || val < 1) {
                    setFeedback(strings.minQty);
                    return;
                }

                if (val > stock) {
                    val = stock;
                    input.value = stock;
                    const msg = strings.maxStock.replace(':count', stock);
                    setFeedback(msg);
                    if (window.showToast) {
                        window.showToast('warning', msg);
                    }
                } else {
                    setFeedback(null);
                }

                triggerOptimisticUpdate(val);
                scheduleUpdate(val);
            });

            input.addEventListener('blur', () => {
                let val = parseInt(input.value, 10);
                if (isNaN(val) || val < 1) {
                    val = 1;
                    input.value = 1;
                    setFeedback(null);
                    triggerOptimisticUpdate(1);
                    sendUpdate(1);
                }
            });
        }
    });

    // Interceptar eliminación asíncrona de artículos
    document.querySelectorAll('.js-remove-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const confirmMsg = form.dataset.confirmMsg || '¿Deseas retirar esta almohada de tu carrito?';
            if (!confirm(confirmMsg)) {
                return;
            }

            const row = form.closest('.cart-item-row');
            const removeUrl = form.action;

            fetch(removeUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.is_empty) {
                    window.location.reload();
                    return;
                }

                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        row.remove();
                        if (data.totals) {
                            updateSummaryTotals(data.totals);
                        }
                        if (typeof data.cart_count !== 'undefined') {
                            updateNavBadges(data.cart_count);
                        }
                        if (window.showToast) {
                            window.showToast('info', data.message || 'Producto eliminado del carrito.');
                        }
                    }, 300);
                }
            })
            .catch(() => {
                form.submit();
            });
        });
    });
});
</script>
@endpush
