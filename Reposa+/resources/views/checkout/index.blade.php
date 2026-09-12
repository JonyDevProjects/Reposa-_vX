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

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm" role="alert" id="checkout-server-errors">
            <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-danger">
                <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                <span>{{ __('messages.checkout.form_errors_header') }}</span>
            </div>
            <ul class="mb-0 ps-3 small text-danger-emphasis">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm d-none" role="alert" id="checkout-client-errors">
        <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-danger">
            <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
            <span>{{ __('messages.checkout.form_errors_header') }}</span>
        </div>
        <ul class="mb-0 ps-3 small text-danger-emphasis" id="checkout-client-errors-list">
        </ul>
    </div>

    <form action="{{ route('checkout') }}" method="POST" id="checkout-form" class="needs-validation" novalidate>
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

                        {{-- Aviso inline cuando faltan campos obligatorios --}}
                        <div id="shipping-required-notice" class="alert alert-warning border border-warning-subtle rounded-3 p-3 mb-3 d-none">
                            <div class="d-flex align-items-center gap-2 small fw-semibold text-dark">
                                <i class="bi bi-exclamation-circle-fill fs-5 text-warning flex-shrink-0"></i>
                                <span>{{ __('messages.checkout.form_required_notice') }}</span>
                            </div>
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
                                    <input type="text" class="form-control rounded-3 @error('shipping_name') is-invalid @enderror" id="shipping_name" name="shipping_name" 
                                           value="{{ old('shipping_name', Auth::user()?->name ?? ($guestShipping['shipping_name'] ?? '')) }}" required
                                           placeholder="Ej. María García López">
                                    <div class="invalid-feedback" id="feedback-shipping_name">
                                        {{ $errors->first('shipping_name') ?: __('messages.checkout.validation.name_required') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_email" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control rounded-3 @error('shipping_email') is-invalid @enderror" id="shipping_email" name="shipping_email" 
                                           value="{{ old('shipping_email', Auth::user()?->email ?? ($guestShipping['shipping_email'] ?? '')) }}" required
                                           placeholder="maria@ejemplo.com">
                                    <div class="invalid-feedback" id="feedback-shipping_email">
                                        {{ $errors->first('shipping_email') ?: __('messages.checkout.validation.email_required') }}
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="shipping_street" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.street') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('shipping_street') is-invalid @enderror" id="shipping_street" name="shipping_street" 
                                           value="{{ old('shipping_street', $guestShipping['shipping_street'] ?? '') }}" required
                                           placeholder="{{ __('messages.auth.street_placeholder') }}">
                                    <div class="invalid-feedback" id="feedback-shipping_street">
                                        {{ $errors->first('shipping_street') ?: __('messages.checkout.validation.street_required') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_city" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.city') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('shipping_city') is-invalid @enderror" id="shipping_city" name="shipping_city" 
                                           value="{{ old('shipping_city', $guestShipping['shipping_city'] ?? '') }}" required
                                           placeholder="{{ __('messages.auth.city_placeholder') }}">
                                    <div class="invalid-feedback" id="feedback-shipping_city">
                                        {{ $errors->first('shipping_city') ?: __('messages.checkout.validation.city_required') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_zip_code" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.zip_code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('shipping_zip_code') is-invalid @enderror" id="shipping_zip_code" name="shipping_zip_code" 
                                           value="{{ old('shipping_zip_code', $guestShipping['shipping_zip_code'] ?? '') }}" required
                                           placeholder="{{ __('messages.auth.zip_code_placeholder') }}">
                                    <div class="invalid-feedback" id="feedback-shipping_zip_code">
                                        {{ $errors->first('shipping_zip_code') ?: __('messages.checkout.validation.zip_code_required') }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_province" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.province') }}
                                    </label>
                                    <input type="text" class="form-control rounded-3 @error('shipping_province') is-invalid @enderror" id="shipping_province" name="shipping_province" 
                                           value="{{ old('shipping_province', $guestShipping['shipping_province'] ?? '') }}"
                                           placeholder="{{ __('messages.auth.province_placeholder') }}">
                                    @error('shipping_province')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="shipping_phone" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.phone') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control rounded-3 @error('shipping_phone') is-invalid @enderror" id="shipping_phone" name="shipping_phone" 
                                           value="{{ old('shipping_phone', $userPhone ?? ($guestShipping['shipping_phone'] ?? '')) }}" required
                                           placeholder="{{ __('messages.auth.phone_placeholder') }}">
                                    <div class="invalid-feedback" id="feedback-shipping_phone">
                                        {{ $errors->first('shipping_phone') ?: __('messages.checkout.validation.phone_required') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bloque 2: Opciones de Envío / Paquetería --}}
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h2 class="h6 fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-truck text-primary"></i>
                                <span>{{ __('messages.checkout.shipping_method') }}</span>
                            </h2>
                            @if($total >= 50.00)
                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-2 py-1 small">
                                    <i class="bi bi-gift-fill me-1"></i>¡Envío gratuito disponible!
                                </span>
                            @endif
                        </div>

                        @if($total >= 50.00)
                            <div class="alert alert-success d-flex align-items-center gap-3 py-2 px-3 rounded-3 border-0 bg-success-subtle text-success mb-3 small">
                                <i class="bi bi-stars fs-4 flex-shrink-0 text-success"></i>
                                <div>
                                    <div class="fw-bold">¡Enhorabuena! Has superado el umbral de 50,00€.</div>
                                    <div class="text-success-emphasis" style="font-size: 0.8rem;">Disfrutas de Envío Estándar Correos Express <strong>100% gratuito</strong> en esta compra.</div>
                                </div>
                            </div>
                        @else
                            @php $remainingForFree = 50.00 - $total; @endphp
                            <div class="p-2 px-3 rounded-3 bg-light border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 small text-muted">
                                <span><i class="bi bi-info-circle text-primary me-1"></i>Añade <strong>{{ number_format($remainingForFree, 2) }}€</strong> más para conseguir <strong>Envío Estándar Gratuito</strong>.</span>
                                <a href="{{ route('catalog') }}" class="text-primary fw-semibold text-decoration-none">
                                    Añadir productos <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        @endif

                        @php
                            $selectedServiceId = old('shipping_service_type', $guestShipping['shipping_service_type'] ?? ($shippingRates[0]['id'] ?? 'standard_48h'));
                            $selectedRate = collect($shippingRates)->firstWhere('id', $selectedServiceId) ?? ($shippingRates[0] ?? ['cost' => 0.0, 'id' => 'standard_48h']);
                        @endphp
                        <div class="d-flex flex-column gap-3">
                            @foreach($shippingRates as $rate)
                                @php 
                                    $isFree = ($rate['cost'] == 0); 
                                    $isChecked = ($rate['id'] === $selectedRate['id']);
                                @endphp
                                <div class="p-3 rounded-3 border {{ $isFree ? 'border-success border-2 bg-success-subtle bg-opacity-25 shadow-2xs' : 'bg-light border-light-subtle' }} d-flex align-items-center justify-content-between gap-3 shipping-rate-card transition-all"
                                     style="{{ $isFree ? 'border-color: rgba(5, 150, 105, 0.45) !important;' : '' }}">
                                    <div class="form-check flex-grow-1">
                                        <input class="form-check-input mt-1" type="radio" 
                                               name="shipping_service_type" 
                                               id="ship_{{ $rate['id'] }}" 
                                               value="{{ $rate['id'] }}" 
                                               data-cost="{{ $rate['cost'] }}"
                                               {{ $isChecked ? 'checked' : '' }}
                                               onchange="updateShippingTotal({{ $rate['cost'] }})">
                                        <label class="form-check-label w-100 cursor-pointer" for="ship_{{ $rate['id'] }}">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <strong class="text-navy">{{ $rate['name'] }}</strong>
                                                    <span class="badge bg-white text-secondary border small">{{ $rate['carrier'] }}</span>
                                                    @if($isFree)
                                                        <span class="badge bg-success text-white fw-bold small shadow-2xs">
                                                            <i class="bi bi-check-circle-fill me-1"></i>¡ENVÍO GRATUITO!
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="badge {{ $isFree ? 'bg-success text-white px-3 py-1 fs-6' : 'bg-primary-subtle text-primary border' }} fw-bold tabular-nums">
                                                    {{ $isFree ? '0,00€ (Gratis)' : number_format($rate['cost'], 2) . '€' }}
                                                </span>
                                            </div>
                                            <div class="text-muted small mt-1">{{ $rate['description'] }}</div>
                                            <div class="small {{ $isFree ? 'text-success fw-semibold' : 'text-primary fw-semibold' }} mt-1">
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
                            $defaultShippingCost = $selectedRate['cost'] ?? ($shippingRates[0]['cost'] ?? 0.0);
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

                        <div id="btn-submit-feedback" class="alert alert-danger py-2 px-3 small rounded-3 mb-3 d-none text-center shadow-2xs">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ __('messages.checkout.btn_missing_data_hint') }}
                        </div>

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

<style>
@keyframes checkoutShake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-6px); }
    40%, 80% { transform: translateX(6px); }
}
.animate-shake {
    animation: checkoutShake 0.45s ease-in-out;
    border: 1px solid #dc3545 !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
}
</style>

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

    document.addEventListener('DOMContentLoaded', function () {
        const checkoutForm = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('btn-submit-order');
        const shippingNotice = document.getElementById('shipping-required-notice');
        const clientErrorsAlert = document.getElementById('checkout-client-errors');
        const clientErrorsList = document.getElementById('checkout-client-errors-list');
        const btnSubmitFeedback = document.getElementById('btn-submit-feedback');
        const shippingCard = document.querySelector('#guest-address-fields')?.closest('.card');

        const addressRadios = document.querySelectorAll('input[name="address_id"]');
        const guestFieldsContainer = document.getElementById('guest-address-fields');

        function syncAddressFieldRequirements() {
            const selectedAddr = document.querySelector('input[name="address_id"]:checked');
            const isUsingSaved = selectedAddr && selectedAddr.value !== 'new';
            const inputs = guestFieldsContainer?.querySelectorAll('input[name^="shipping_"]');
            if (inputs) {
                inputs.forEach(inp => {
                    if (isUsingSaved) {
                        inp.removeAttribute('required');
                        inp.classList.remove('is-invalid');
                    } else if (inp.id !== 'shipping_province') {
                        inp.setAttribute('required', 'required');
                    }
                });
            }
        }
        addressRadios.forEach(radio => radio.addEventListener('change', syncAddressFieldRequirements));

        const fieldDefinitions = [
            { id: 'shipping_name', name: "{{ __('messages.auth.full_name') }}", minLength: 2 },
            { id: 'shipping_email', name: "{{ __('messages.auth.email') }}", isEmail: true },
            { id: 'shipping_street', name: "{{ __('messages.auth.street') }}", minLength: 3 },
            { id: 'shipping_city', name: "{{ __('messages.auth.city') }}", minLength: 2 },
            { id: 'shipping_zip_code', name: "{{ __('messages.auth.zip_code') }}", minLength: 3 },
            { id: 'shipping_phone', name: "{{ __('messages.auth.phone') }}", minLength: 6 },
        ];

        fieldDefinitions.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                input.addEventListener('input', function () {
                    if (this.value.trim().length > 0) {
                        this.classList.remove('is-invalid');
                        const anyInvalidLeft = fieldDefinitions.some(f => {
                            const inp = document.getElementById(f.id);
                            return inp && inp.classList.contains('is-invalid');
                        });
                        if (!anyInvalidLeft) {
                            if (shippingNotice) shippingNotice.classList.add('d-none');
                            if (clientErrorsAlert) clientErrorsAlert.classList.add('d-none');
                            if (btnSubmitFeedback) btnSubmitFeedback.classList.add('d-none');
                            if (shippingCard) shippingCard.classList.remove('animate-shake');
                        }
                    }
                });
            }
        });

        if (checkoutForm && submitBtn) {
            checkoutForm.addEventListener('submit', function (e) {
                const selectedAddr = document.querySelector('input[name="address_id"]:checked');
                const isUsingSaved = selectedAddr && selectedAddr.value !== 'new';

                if (!isUsingSaved) {
                    let hasErrors = false;
                    let firstInvalidEl = null;
                    const errorMessages = [];

                    fieldDefinitions.forEach(field => {
                        const input = document.getElementById(field.id);
                        if (!input) return;

                        const val = input.value.trim();
                        let isFieldValid = true;

                        if (!val || (field.minLength && val.length < field.minLength)) {
                            isFieldValid = false;
                            errorMessages.push(`El campo ${field.name.toLowerCase()} es obligatorio.`);
                        } else if (field.isEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                            isFieldValid = false;
                            errorMessages.push(`El campo ${field.name.toLowerCase()} debe ser un correo válido.`);
                        }

                        if (!isFieldValid) {
                            hasErrors = true;
                            input.classList.add('is-invalid');
                            if (!firstInvalidEl) {
                                firstInvalidEl = input;
                            }
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (clientErrorsAlert && clientErrorsList) {
                            clientErrorsList.innerHTML = errorMessages.map(msg => `<li>${msg}</li>`).join('');
                            clientErrorsAlert.classList.remove('d-none');
                        }

                        if (shippingNotice) {
                            shippingNotice.classList.remove('d-none');
                        }

                        if (btnSubmitFeedback) {
                            btnSubmitFeedback.classList.remove('d-none');
                        }

                        if (shippingCard) {
                            shippingCard.classList.remove('animate-shake');
                            void shippingCard.offsetWidth;
                            shippingCard.classList.add('animate-shake');
                        }

                        if (firstInvalidEl) {
                            firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => firstInvalidEl.focus(), 350);
                        }

                        return false;
                    }
                }

                if (clientErrorsAlert) clientErrorsAlert.classList.add('d-none');
                if (shippingNotice) shippingNotice.classList.add('d-none');
                if (btnSubmitFeedback) btnSubmitFeedback.classList.add('d-none');

                saveGuestDataToStorage();

                const isStripe = document.getElementById('payment_stripe')?.checked;
                setTimeout(function () {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = isStripe
                        ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Redirigiendo a pasarela segura...'
                        : '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando pedido seguro...';
                }, 10);
            });
        }

        // Persistencia en sessionStorage para usuarios invitados (evita pérdida al regresar de Stripe)
        const GUEST_STORAGE_KEY = 'reposa_checkout_guest_shipping';
        const isAuthUser = {{ Auth::check() ? 'true' : 'false' }};

        function saveGuestDataToStorage() {
            if (isAuthUser) return;
            const data = {
                shipping_name: document.getElementById('shipping_name')?.value || '',
                shipping_email: document.getElementById('shipping_email')?.value || '',
                shipping_street: document.getElementById('shipping_street')?.value || '',
                shipping_city: document.getElementById('shipping_city')?.value || '',
                shipping_zip_code: document.getElementById('shipping_zip_code')?.value || '',
                shipping_province: document.getElementById('shipping_province')?.value || '',
                shipping_phone: document.getElementById('shipping_phone')?.value || '',
                shipping_service_type: document.querySelector('input[name="shipping_service_type"]:checked')?.value || '',
            };
            try {
                sessionStorage.setItem(GUEST_STORAGE_KEY, JSON.stringify(data));
            } catch (e) {}
        }

        function restoreGuestDataFromStorage() {
            if (isAuthUser) return;
            try {
                const raw = sessionStorage.getItem(GUEST_STORAGE_KEY);
                if (!raw) return;
                const data = JSON.parse(raw);
                ['shipping_name', 'shipping_email', 'shipping_street', 'shipping_city', 'shipping_zip_code', 'shipping_province', 'shipping_phone'].forEach(fieldId => {
                    const el = document.getElementById(fieldId);
                    if (el && !el.value && data[fieldId]) {
                        el.value = data[fieldId];
                    }
                });
                if (data.shipping_service_type) {
                    const rateRadio = document.querySelector(`input[name="shipping_service_type"][value="${data.shipping_service_type}"]`);
                    if (rateRadio && !rateRadio.checked) {
                        rateRadio.checked = true;
                        const cost = parseFloat(rateRadio.getAttribute('data-cost') || 0);
                        updateShippingTotal(cost);
                    }
                }
            } catch (e) {}
        }

        restoreGuestDataFromStorage();
        saveGuestDataToStorage();

        ['shipping_name', 'shipping_email', 'shipping_street', 'shipping_city', 'shipping_zip_code', 'shipping_province', 'shipping_phone'].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) {
                el.addEventListener('input', saveGuestDataToStorage);
                el.addEventListener('change', saveGuestDataToStorage);
            }
        });

        document.querySelectorAll('input[name="shipping_service_type"]').forEach(radio => {
            radio.addEventListener('change', saveGuestDataToStorage);
        });
    });
</script>
@endsection
