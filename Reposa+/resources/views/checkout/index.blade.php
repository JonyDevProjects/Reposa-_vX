@extends('layouts.app')

@section('title', __('messages.checkout.title'))

@section('content')
<div class="container py-4 py-lg-5">
    {{-- Header --}}
    <div class="mb-4 pb-2 border-bottom">
        <h1 class="h3 fw-bold text-navy mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>{{ __('messages.checkout.title') }}
        </h1>
        <p class="text-muted small mb-0">{{ __('messages.checkout.subtitle') }}</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout') }}" method="POST" id="checkout-form" novalidate>
        @csrf
        <div class="row g-4 align-items-start">
            {{-- Columna izquierda: Datos de Envío y Opciones --}}
            <div class="col-lg-7">
                {{-- Aviso para usuarios no registrados con enlace a Login --}}
                @guest
                    <div class="card border border-primary-subtle bg-indigo-subtle rounded-4 p-3 mb-4 shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-navy small d-block">{{ __('messages.checkout.guest_notice') }}</span>
                                    <span class="text-muted small" style="font-size: 0.78rem;">Compra sin registrarte o accede a tu cuenta para usar tus direcciones.</span>
                                </div>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                {{ __('messages.checkout.login_link') }}
                            </a>
                        </div>
                    </div>
                @endguest

                {{-- Bloque 1: Datos del Destinatario y Entrega --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <span>{{ __('messages.checkout.shipping_data') }}</span>
                            </h2>
                            @auth
                                <span class="badge bg-success-subtle text-success border border-success-subtle small fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i>{{ Auth::user()->name }}
                                </span>
                            @else
                                <span class="badge bg-light text-muted border small fw-normal">
                                    Compra como Invitado
                                </span>
                            @endauth
                        </div>

                        {{-- Si está autenticado y tiene direcciones guardadas --}}
                        @auth
                            @if($userAddresses->isNotEmpty())
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted">{{ __('messages.checkout.saved_address') }}</label>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($userAddresses as $addr)
                                            <div class="form-check p-3 rounded-3 border bg-light">
                                                <input class="form-check-input mt-1" type="radio" name="address_id" id="addr_{{ $addr->id }}" value="{{ $addr->id }}" {{ $loop->first ? 'checked' : '' }}>
                                                <label class="form-check-label small w-100" for="addr_{{ $addr->id }}">
                                                    <div class="fw-semibold text-dark">{{ $addr->street }}</div>
                                                    <div class="text-muted">{{ $addr->zip_code }} {{ $addr->city }} @if($addr->province)({{ $addr->province }})@endif</div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- Campos de dirección (obligatorios para invitados, opcionales si usuario elige guardada) --}}
                        <div id="guest-address-fields" class="@auth @if($userAddresses->isNotEmpty()) mt-4 pt-3 border-top @endif @endauth">
                            @auth
                                @if($userAddresses->isNotEmpty())
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="address_id" id="addr_new" value="new">
                                        <label class="form-check-label fw-semibold small text-navy" for="addr_new">
                                            {{ __('messages.checkout.new_address') }}
                                        </label>
                                    </div>
                                @endif
                            @endauth

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="shipping_name" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.full_name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3" id="shipping_name" name="shipping_name" 
                                           value="{{ old('shipping_name', Auth::user()?->name) }}" required
                                           placeholder="Ej. María García López">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_email" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control rounded-3" id="shipping_email" name="shipping_email" 
                                           value="{{ old('shipping_email', Auth::user()?->email) }}" required
                                           placeholder="maria@ejemplo.com">
                                </div>
                                <div class="col-md-12">
                                    <label for="shipping_street" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.street') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3" id="shipping_street" name="shipping_street" 
                                           value="{{ old('shipping_street') }}"
                                           placeholder="{{ __('messages.auth.street_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_city" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.city') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3" id="shipping_city" name="shipping_city" 
                                           value="{{ old('shipping_city') }}"
                                           placeholder="{{ __('messages.auth.city_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_zip_code" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.zip_code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3" id="shipping_zip_code" name="shipping_zip_code" 
                                           value="{{ old('shipping_zip_code') }}"
                                           placeholder="{{ __('messages.auth.zip_code_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_province" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.province') }}
                                    </label>
                                    <input type="text" class="form-control rounded-3" id="shipping_province" name="shipping_province" 
                                           value="{{ old('shipping_province') }}"
                                           placeholder="{{ __('messages.auth.province_placeholder') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_phone" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.phone') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control rounded-3" id="shipping_phone" name="shipping_phone" 
                                           value="{{ old('shipping_phone', $userPhone) }}" required
                                           placeholder="{{ __('messages.auth.phone_placeholder') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bloque 2: Opciones de Envío / Paquetería --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-truck text-primary"></i>
                            <span>{{ __('messages.checkout.shipping_method') }}</span>
                        </h2>

                        <div class="d-flex flex-column gap-3">
                            @foreach($shippingRates as $rate)
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between gap-3">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input mt-1" type="radio" 
                                               name="shipping_service_type" 
                                               id="ship_{{ $rate['id'] }}" 
                                               value="{{ $rate['id'] }}" 
                                               data-cost="{{ $rate['cost'] }}"
                                               {{ $loop->first ? 'checked' : '' }}
                                               onchange="updateShippingTotal({{ $rate['cost'] }})">
                                        <label class="form-check-label w-100" for="ship_{{ $rate['id'] }}">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <strong class="text-navy">{{ $rate['name'] }}</strong>
                                                    <span class="badge bg-light text-secondary border ms-1 small">{{ $rate['carrier'] }}</span>
                                                </div>
                                                <span class="badge {{ $rate['cost'] == 0 ? 'bg-success text-white' : 'bg-primary-subtle text-primary border' }} fw-bold tabular-nums">
                                                    {{ $rate['cost'] == 0 ? 'Gratis' : number_format($rate['cost'], 2) . '€' }}
                                                </span>
                                            </div>
                                            <div class="text-muted small mt-1">{{ $rate['description'] }}</div>
                                            <div class="small text-primary fw-semibold mt-1">
                                                <i class="bi bi-clock-history me-1"></i>{{ $rate['estimated_days'] }}
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Bloque 3: Pasarela de Pago --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card-2-front-fill text-primary"></i>
                            <span>{{ __('messages.checkout.payment_method') }}</span>
                        </h2>

                        <div class="p-3 rounded-3 border border-primary-subtle bg-indigo-subtle d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-shield-lock-fill fs-5"></i>
                            </div>
                            <div class="small">
                                <span class="fw-bold text-navy d-block">{{ __('messages.checkout.pay_with_stripe') }}</span>
                                <span class="text-muted">Tarjeta de crédito / débito, Apple Pay, Google Pay con cifrado de grado bancario SSL.</span>
                            </div>
                        </div>

                        <input type="hidden" name="payment_method" value="stripe">
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Resumen de Compra --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold text-navy mb-3">{{ __('messages.checkout.order_summary') }}</h2>

                        {{-- Listado de productos del pedido --}}
                        <div class="order-items-scroll pe-1 mb-3" style="max-height: 280px; overflow-y: auto;">
                            @foreach($cartItems as $item)
                                <div class="d-flex align-items-center justify-content-between gap-3 py-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2 min-w-0">
                                        <img src="{{ $item->product->image_url ?: '/images/product-placeholder.svg' }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="rounded-2 border bg-light object-fit-cover flex-shrink-0" 
                                             width="44" height="44">
                                        <div class="min-w-0">
                                            <div class="fw-semibold text-navy text-truncate small" style="max-width: 170px;">
                                                {{ $item->product->name }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">
                                                {{ $item->quantity }} x {{ number_format($item->product->price, 2) }}€
                                            </div>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-navy tabular-nums small">
                                        {{ number_format($item->product->price * $item->quantity, 2) }}€
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desglose de costes --}}
                        @php
                            $defaultShippingCost = $shippingRates[0]['cost'] ?? 0.0;
                            $subtotalNet = round($total / 1.21, 2);
                            $taxVat = round($total - $subtotalNet, 2);
                        @endphp

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
                                <span class="text-muted">{{ __('messages.checkout.shipping_method') }}</span>
                                <span id="summary-shipping-cost" class="tabular-nums fw-semibold {{ $defaultShippingCost == 0 ? 'text-success' : 'text-dark' }}">
                                    {{ $defaultShippingCost == 0 ? 'Gratis' : number_format($defaultShippingCost, 2) . '€' }}
                                </span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="h5 fw-bold text-navy mb-0 d-block">{{ __('messages.cart.total') }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">IVA incluido</small>
                            </div>
                            <span id="summary-grand-total" class="h4 fw-bold text-primary tabular-nums mb-0">
                                {{ number_format($total + $defaultShippingCost, 2) }}€
                            </span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm mb-3">
                            <i class="bi bi-shield-lock-fill me-2"></i>{{ __('messages.checkout.btn_place_order') }}
                        </button>

                        <p class="text-center text-muted small mb-3" style="font-size: 0.78rem;">
                            <i class="bi bi-shield-check text-success me-1"></i>{{ __('messages.checkout.guarantee_text') }}
                        </p>

                        <x-trust-seals variant="checkout" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const itemsTotal = {{ (float) $total }};
    function updateShippingTotal(shippingCost) {
        const grandTotal = itemsTotal + parseFloat(shippingCost);
        const costEl = document.getElementById('summary-shipping-cost');
        const totalEl = document.getElementById('summary-grand-total');

        if (costEl && totalEl) {
            costEl.textContent = shippingCost == 0 ? 'Gratis' : shippingCost.toFixed(2) + '€';
            costEl.className = 'tabular-nums fw-semibold ' + (shippingCost == 0 ? 'text-success' : 'text-dark');
            totalEl.textContent = grandTotal.toFixed(2) + '€';
        }
    }
</script>
@endsection
