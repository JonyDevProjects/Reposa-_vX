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
                {{-- Aviso para usuarios no registrados con enlace a Login o Google --}}
                @guest
                    <div class="card border border-primary-subtle bg-indigo-subtle rounded-4 p-3 mb-4 shadow-2xs">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-navy small d-block">{{ __('messages.checkout.guest_notice') }}</span>
                                    <span class="text-muted small" style="font-size: 0.78rem;">Compra como invitado o accede para utilizar tus direcciones guardadas.</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('auth.google', ['redirect' => 'checkout']) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 bg-white d-flex align-items-center gap-1 border-light-subtle shadow-2xs">
                                    <svg width="14" height="14" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                                        <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                        <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.173 0 7.547 0 9s.348 2.827.957 4.039l3.007-2.332z"/>
                                        <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                                    </svg>
                                    <span>Google</span>
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    {{ __('messages.checkout.login_link') }}
                                </a>
                            </div>
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

                        <div class="d-flex flex-column gap-3">
                            {{-- Opción 1: Tarjeta Bancaria / Stripe (Por defecto) --}}
                            <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between gap-3 payment-method-card" id="card-payment-stripe">
                                <div class="form-check flex-grow-1">
                                    <input class="form-check-input mt-1" type="radio" 
                                           name="payment_method" 
                                           id="payment_stripe" 
                                           value="stripe" 
                                           checked
                                           onchange="updatePaymentMethod('stripe')">
                                    <label class="form-check-label w-100" for="payment_stripe">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <strong class="text-navy">{{ __('messages.checkout.pay_with_stripe') }}</strong>
                                                <span class="badge bg-primary-subtle text-primary border ms-1 small">Recomendado</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-muted">
                                                <i class="bi bi-credit-card fs-5 text-primary"></i>
                                                <i class="bi bi-shield-lock-fill text-success ms-1"></i>
                                                <span class="small fw-semibold text-secondary">SSL 256-bit</span>
                                            </div>
                                        </div>
                                        <div class="text-muted small mt-1">Tarjeta de crédito o débito (Visa, Mastercard), Apple Pay y Google Pay procesados de forma segura con Stripe.</div>
                                    </label>
                                </div>
                            </div>

                            {{-- Opción 2: Pago Contra Reembolso / Pedido Directo --}}
                            <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between gap-3 payment-method-card" id="card-payment-direct">
                                <div class="form-check flex-grow-1">
                                    <input class="form-check-input mt-1" type="radio" 
                                           name="payment_method" 
                                           id="payment_direct" 
                                           value="direct"
                                           onchange="updatePaymentMethod('direct')">
                                    <label class="form-check-label w-100" for="payment_direct">
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <div>
                                                <strong class="text-navy">Pago contra reembolso / Pedido directo</strong>
                                                <span class="badge bg-light text-secondary border ms-1 small">Sin tarjeta</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 text-muted">
                                                <i class="bi bi-cash-stack fs-5 text-success"></i>
                                            </div>
                                        </div>
                                        <div class="text-muted small mt-1">Confirmación directa del pedido sin necesidad de tarjeta bancaria (ideal para pruebas y entrega contra reembolso).</div>
                                    </label>
                                </div>
                            </div>
                        </div>
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

                        <button type="submit" id="btn-submit-order" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm mb-3">
                            <i id="btn-submit-icon" class="bi bi-credit-card me-2"></i><span id="btn-submit-text">{{ __('messages.checkout.btn_place_order') }}</span>
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

    function updatePaymentMethod(method) {
        const btnText = document.getElementById('btn-submit-text');
        const btnIcon = document.getElementById('btn-submit-icon');
        if (method === 'stripe') {
            if (btnText) btnText.textContent = "{{ __('messages.checkout.btn_place_order') }}";
            if (btnIcon) btnIcon.className = 'bi bi-credit-card me-2';
        } else {
            if (btnText) btnText.textContent = "{{ __('messages.checkout.direct_order') }}";
            if (btnIcon) btnIcon.className = 'bi bi-shield-lock-fill me-2';
        }
    }
</script>
@endsection
