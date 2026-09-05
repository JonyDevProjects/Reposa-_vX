@extends('layouts.app')

@section('title', __('messages.orders.show.title', ['id' => $order->id]))

@section('content')
<div class="container py-4 py-lg-5">
    {{-- Hero de Confirmación Serena (Midnight Sanctuary) --}}
    <section class="order-confirmation-hero" aria-labelledby="order-confirmation-heading">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-start gap-3">
                <div class="serene-confirm-badge" aria-hidden="true">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h1 id="order-confirmation-heading" class="order-hero-title">
                        {{ __('messages.orders.show.hero_title') }}
                    </h1>
                    <p class="order-hero-subtitle mb-3">
                        {{ __('messages.orders.show.hero_subtitle', ['id' => $order->id]) }}
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge badge-sanctuary-status {{ in_array($order->status, ['delivered', 'completed']) ? 'status-success' : ($order->status === 'cancelled' ? 'status-danger' : 'status-processing') }}">
                            <i class="bi bi-circle-fill me-1 small" aria-hidden="true"></i>{{ ucfirst($order->status) }}
                        </span>
                        <span class="badge badge-sanctuary-meta">
                            <i class="bi bi-receipt me-1" aria-hidden="true"></i>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="badge badge-sanctuary-meta">
                            <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                        </span>
                        <span class="badge badge-sanctuary-meta">
                            <i class="bi bi-shield-lock-fill me-1" aria-hidden="true"></i>{{ __('messages.orders.show.payment_secure_verified') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Acciones rápidas en cabecera --}}
            <div class="d-flex flex-row flex-md-column gap-2 flex-shrink-0 align-self-start align-self-md-center">
                <a href="{{ route('orders.invoice', ['order' => $order, 'token' => $order->guest_token]) }}" class="btn btn-light btn-sm fw-semibold shadow-sm text-primary text-nowrap">
                    <i class="bi bi-file-earmark-pdf-fill me-1 text-danger" aria-hidden="true"></i>{{ __('messages.orders.show.download_invoice') }}
                </a>
                @if(auth()->check())
                    <a href="{{ route('profile') }}#orders" class="btn btn-outline-light btn-sm text-nowrap">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>{{ __('messages.orders.show.back_to_orders') }}
                    </a>
                @else
                    <a href="{{ url('/catalog') }}" class="btn btn-outline-light btn-sm text-nowrap">
                        <i class="bi bi-bag me-1" aria-hidden="true"></i>{{ __('messages.cart.view_catalog') }}
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Conversión Invitado a Usuario Registrado en 1 Clic --}}
    @if($order->isGuest() && !auth()->check())
        <section class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 1px solid #cbd5e1 !important;">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-2 py-1 mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Acceso Rápido</span>
                                <h2 class="h5 fw-bold text-navy mb-1">{{ __('messages.orders.claim_account_title') }}</h2>
                                <p class="text-muted small mb-2">{{ __('messages.orders.claim_account_subtitle') }}</p>
                                <div class="d-flex flex-wrap gap-2 small text-secondary">
                                    <span><i class="bi bi-envelope-check me-1 text-success"></i><strong>{{ $order->customer_email }}</strong></span>
                                    <span>&bull;</span>
                                    <span><i class="bi bi-shield-check me-1 text-primary"></i>Datos de envío asociados</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        @if($errors->has('password'))
                            <div class="alert alert-danger py-1 px-2 small mb-2">{{ $errors->first('password') }}</div>
                        @endif
                        <form action="{{ route('orders.claim_account', $order) }}" method="POST" class="row g-2">
                            @csrf
                            <input type="hidden" name="token" value="{{ $order->guest_token }}">
                            <div class="col-sm-6">
                                <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror" 
                                       placeholder="Contraseña (mín. 8)" required autocomplete="new-password">
                            </div>
                            <div class="col-sm-6">
                                <input type="password" name="password_confirmation" class="form-control form-control-sm" 
                                       placeholder="Repite contraseña" required autocomplete="new-password">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold shadow-sm">
                                    <i class="bi bi-lock-fill me-1"></i> {{ __('messages.orders.claim_btn') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @php
        $statusStepMap = [
            'pending' => 1,
            'processing' => 2,
            'shipped' => 3,
            'delivered' => 4,
            'completed' => 4,
        ];
        $currentStep = $statusStepMap[$order->status] ?? 1;
        $isCancelled = $order->status === 'cancelled';
        $isRefunded = $order->status === 'refunded';
        $progressPercent = $isCancelled ? 0 : (($currentStep - 1) / 3 * 100);
        if (in_array($order->status, ['delivered', 'completed'])) {
            $progressPercent = 100;
        }

        $stepsData = [
            1 => [
                'name' => __('messages.orders.show.timeline_step1'),
                'desc' => __('messages.orders.show.timeline_step1_desc'),
                'detail' => __('messages.orders.show.timeline_step1_detail'),
                'icon' => 'bi-shield-check',
            ],
            2 => [
                'name' => __('messages.orders.show.timeline_step2'),
                'desc' => __('messages.orders.show.timeline_step2_desc'),
                'detail' => __('messages.orders.show.timeline_step2_detail'),
                'icon' => 'bi-box-seam-fill',
            ],
            3 => [
                'name' => __('messages.orders.show.timeline_step3'),
                'desc' => __('messages.orders.show.timeline_step3_desc'),
                'detail' => __('messages.orders.show.timeline_step3_detail'),
                'icon' => 'bi-truck',
            ],
            4 => [
                'name' => __('messages.orders.show.timeline_step4'),
                'desc' => __('messages.orders.show.timeline_step4_desc'),
                'detail' => __('messages.orders.show.timeline_step4_detail'),
                'icon' => 'bi-moon-stars-fill',
            ],
        ];

        $activeStepData = $stepsData[$currentStep] ?? $stepsData[1];
    @endphp

    {{-- Timeline Interactivo de Fases de Descanso --}}
    <section class="order-timeline-card" aria-label="{{ __('messages.orders.show.timeline_title') }}">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-baseline mb-3">
            <div>
                <h2 class="timeline-header-title">{{ __('messages.orders.show.timeline_title') }}</h2>
                <p class="timeline-header-subtitle">{{ __('messages.orders.show.timeline_subtitle') }}</p>
            </div>
            @if(!$isCancelled && !$isRefunded)
                <span class="badge bg-light text-primary border px-3 py-2 fw-semibold">
                    <i class="bi bi-clock-history me-1" aria-hidden="true"></i>{{ __('messages.orders.show.status') }}: {{ ucfirst($order->status) }}
                </span>
            @endif
        </div>

        @if($isCancelled)
            <div class="alert alert-danger d-flex align-items-center rounded-3 p-3 mb-0" role="alert">
                <i class="bi bi-x-octagon-fill fs-3 me-3 text-danger" aria-hidden="true"></i>
                <div>
                    <h3 class="h6 fw-bold mb-1">{{ __('messages.orders.show.timeline_cancelled') }}</h3>
                    <p class="small mb-0">{{ __('messages.orders.show.timeline_cancelled_desc') }}</p>
                </div>
            </div>
        @elseif($isRefunded)
            <div class="alert alert-secondary d-flex align-items-center rounded-3 p-3 mb-0" role="alert">
                <i class="bi bi-arrow-counterclockwise fs-3 me-3 text-secondary" aria-hidden="true"></i>
                <div>
                    <h3 class="h6 fw-bold mb-1">{{ __('messages.orders.show.timeline_refunded') }}</h3>
                    <p class="small mb-0">{{ __('messages.orders.show.timeline_refunded_desc') }}</p>
                </div>
            </div>
        @else
            <div class="order-timeline-track">
                <div class="timeline-rail d-none d-md-block" aria-hidden="true">
                    <div class="timeline-progress-fill" style="--timeline-progress-scale: {{ round($progressPercent / 100, 2) }};"></div>
                </div>

                <ol class="order-timeline-steps">
                    @foreach($stepsData as $num => $step)
                        @php
                            $isStepCompleted = ($num < $currentStep) || ($num === $currentStep && in_array($order->status, ['delivered', 'completed']));
                            $isStepActive = ($num === $currentStep) && !in_array($order->status, ['delivered', 'completed']);
                            $stepStateClass = $isStepCompleted ? 'is-completed' : ($isStepActive ? 'is-active' : 'is-upcoming');
                        @endphp
                        <li class="timeline-step {{ $stepStateClass }}" 
                            data-step-number="{{ $num }}"
                            data-step-title="{{ $step['name'] }}"
                            data-step-detail="{{ $step['detail'] }}"
                            data-step-icon="{{ $step['icon'] }}"
                            @if($isStepActive || ($num === $currentStep)) aria-current="step" @endif>
                            
                            <div class="timeline-node-wrapper">
                                <button type="button" 
                                        class="timeline-node-btn" 
                                        aria-label="{{ $step['name'] }} — {{ $isStepCompleted ? __('messages.orders.show.timeline_step1') : ($isStepActive ? __('messages.orders.show.status') : '') }}"
                                        aria-expanded="{{ $num === $currentStep ? 'true' : 'false' }}">
                                    @if($isStepCompleted)
                                        <i class="bi bi-check2" aria-hidden="true"></i>
                                    @else
                                        <i class="bi {{ $step['icon'] }}" aria-hidden="true"></i>
                                    @endif
                                </button>
                                <div>
                                    <div class="timeline-step-title">{{ $step['name'] }}</div>
                                    <p class="timeline-step-desc">{{ $step['desc'] }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Panel dinámico e interactivo de detalle de fase --}}
            <div class="timeline-detail-panel" aria-live="polite">
                <div class="detail-panel-icon" aria-hidden="true">
                    <i class="bi {{ $activeStepData['icon'] }}"></i>
                </div>
                <div>
                    <h3 class="detail-panel-title">{{ $activeStepData['name'] }}</h3>
                    <p class="detail-panel-text">{{ $activeStepData['detail'] }}</p>
                </div>
            </div>
        @endif
    </section>

    {{-- Seguimiento de Paquetería en Tiempo Real (Correos Express) --}}
    @if($order->shipment)
        <section class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" aria-labelledby="shipment-tracking-heading">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2 py-1 text-uppercase fw-bold" style="font-size: 0.72rem;">
                            <i class="bi bi-truck me-1"></i>{{ $order->shipment->carrier }}
                        </span>
                        <h2 id="shipment-tracking-heading" class="h6 fw-bold mb-0 text-navy">
                            {{ __('messages.shipment.timeline_title') }}
                        </h2>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="font-monospace fw-bold px-2 py-1 bg-light border rounded text-dark small">
                            <i class="bi bi-upc me-1"></i>{{ $order->shipment->tracking_number }}
                        </span>
                        <span class="badge bg-{{ $order->shipment->status_color }}-subtle text-{{ $order->shipment->status_color }} border px-2 py-1 fw-semibold small">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ $order->shipment->status_label }}
                        </span>
                        @if($order->shipment->estimated_delivery_date)
                            <span class="text-muted small">
                                <i class="bi bi-calendar-check me-1 text-primary"></i>Entrega estimada: <strong>{{ $order->shipment->estimated_delivery_date->format('d/m/Y') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @php
                    $events = $order->shipment->tracking_history ?? [];
                    $sortedEvents = array_reverse($events);
                @endphp

                @if(!empty($sortedEvents))
                    <div class="tracking-timeline-list position-relative ps-2">
                        @foreach($sortedEvents as $idx => $event)
                            <div class="d-flex gap-3 mb-3 position-relative">
                                <div class="d-flex flex-column align-items-center flex-shrink-0">
                                    <div class="rounded-circle {{ $idx === 0 ? 'bg-primary text-white shadow-sm' : 'bg-light text-muted border' }} d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px; font-size: 0.85rem; z-index: 2;">
                                        @if($idx === 0)
                                            <i class="bi bi-geo-alt-fill"></i>
                                        @else
                                            <i class="bi bi-check2"></i>
                                        @endif
                                    </div>
                                    @if(!$loop->last)
                                        <div class="flex-grow-1" style="width: 2px; background-color: #e2e8f0; min-height: 28px;"></div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 pb-2">
                                    <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark small">{{ $event['status_label'] ?? ucfirst($event['status'] ?? '') }}</span>
                                            @if(!empty($event['location']))
                                                <span class="badge bg-light text-secondary border small py-0 px-2">{{ $event['location'] }}</span>
                                            @endif
                                        </div>
                                        <span class="text-muted small tabular-nums">
                                            {{ isset($event['timestamp']) ? \Carbon\Carbon::parse($event['timestamp'])->format('d/m/Y H:i') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0 mt-1">{{ $event['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small mb-0">Expedición registrada. Los eventos en tiempo real se actualizarán cuando el paquete sea recogido en plataforma.</p>
                @endif
            </div>
        </section>
    @endif

    {{-- Cuadrícula principal de compra y resumen --}}
    <div class="row g-4">
        {{-- Columna izquierda: Artículos y Ritual de Bienvenida --}}
        <div class="col-lg-8">
            {{-- Listado de Artículos --}}
            <section class="order-section-card" aria-labelledby="purchased-items-title">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 id="purchased-items-title" class="order-section-title mb-0">
                        {{ __('messages.orders.show.purchased_items') }}
                    </h2>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-medium">
                        {{ $order->orderItems->sum('quantity') }} {{ trans_choice('artículo|artículos', $order->orderItems->sum('quantity')) }}
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="py-3 px-3">{{ __('messages.orders.show.product') }}</th>
                                <th scope="col" class="text-center py-3">{{ __('messages.orders.show.quantity') }}</th>
                                <th scope="col" class="text-end py-3">{{ __('messages.orders.show.unit_price') }}</th>
                                <th scope="col" class="text-end py-3 px-3">{{ __('messages.orders.show.subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td class="py-3 px-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="order-item-img">
                                            @else
                                                <div class="order-item-placeholder" aria-hidden="true">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                @if($item->product)
                                                    <a href="{{ route('products.show', $item->product) }}" class="order-item-name">
                                                        {{ $item->product->name }}
                                                    </a>
                                                @else
                                                    <span class="order-item-name">{{ $item->product_name ?? 'Almohada Reposa+' }}</span>
                                                @endif
                                                <br>
                                                <span class="order-item-chip">
                                                    <i class="bi bi-layers me-1" aria-hidden="true"></i>{{ __('messages.orders.show.firmness') }} {{ $item->product->firmness ?? 'Media' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end py-3 text-muted" style="font-variant-numeric: tabular-nums;">
                                        {{ number_format($item->price_at_purchase, 2) }}€
                                    </td>
                                    <td class="text-end py-3 px-3 fw-bold text-dark" style="font-variant-numeric: tabular-nums;">
                                        {{ number_format($item->price_at_purchase * $item->quantity, 2) }}€
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Ritual de Bienvenida a tu Almohada (Unboxing Sereno de Marca) --}}
            <section class="unboxing-ritual-card" aria-labelledby="unboxing-ritual-title">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-moon-stars text-primary fs-5" aria-hidden="true"></i>
                    <h2 id="unboxing-ritual-title" class="unboxing-title mb-0">
                        {{ __('messages.orders.show.unboxing_title') }}
                    </h2>
                </div>
                <p class="unboxing-subtitle">
                    {{ __('messages.orders.show.unboxing_subtitle') }}
                </p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="ritual-box">
                            <div class="ritual-icon-badge" aria-hidden="true">
                                <i class="bi bi-wind"></i>
                            </div>
                            <h3 class="ritual-box-title">{{ __('messages.orders.show.unboxing_step1_title') }}</h3>
                            <p class="ritual-box-desc">{{ __('messages.orders.show.unboxing_step1_desc') }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="ritual-box">
                            <div class="ritual-icon-badge" aria-hidden="true">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <h3 class="ritual-box-title">{{ __('messages.orders.show.unboxing_step2_title') }}</h3>
                            <p class="ritual-box-desc">{{ __('messages.orders.show.unboxing_step2_desc') }}</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="ritual-box">
                            <div class="ritual-icon-badge" aria-hidden="true">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h3 class="ritual-box-title">{{ __('messages.orders.show.unboxing_step3_title') }}</h3>
                            <p class="ritual-box-desc">{{ __('messages.orders.show.unboxing_step3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Columna derecha: Resumen Económico y Datos de Envío --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 96px;">
                {{-- Resumen de la Compra --}}
                <section class="order-summary-card" aria-labelledby="order-summary-title">
                    <h2 id="order-summary-title" class="summary-title">
                        {{ __('messages.orders.show.summary_title') }}
                    </h2>

                    <div class="summary-line">
                        <span>{{ __('messages.orders.show.subtotal') }}</span>
                        <span class="summary-val">{{ number_format($order->total_amount, 2) }}€</span>
                    </div>

                    <div class="summary-line">
                        <span>{{ __('messages.orders.show.shipping') }}</span>
                        <span class="badge bg-success-subtle text-success fw-bold">
                            {{ __('messages.orders.show.shipping_free') }}
                        </span>
                    </div>

                    <div class="summary-line">
                        <span>{{ __('messages.orders.show.vat_included') }}</span>
                        <span class="summary-val">{{ number_format($order->total_amount * 0.21 / 1.21, 2) }}€</span>
                    </div>

                    <div class="summary-total-line">
                        <span class="total-label">{{ __('messages.orders.show.order_total') }}</span>
                        <span class="total-amount">{{ number_format($order->total_amount, 2) }}€</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.invoice', ['order' => $order, 'token' => $order->guest_token]) }}" class="btn btn-primary btn-download-invoice">
                            <i class="bi bi-file-earmark-pdf-fill me-2 text-danger" aria-hidden="true"></i>{{ __('messages.orders.show.download_invoice_pdf') }}
                        </a>

                        @if(auth()->check())
                            <a href="{{ route('profile') }}#orders" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>{{ __('messages.orders.show.back_to_orders') }}
                            </a>
                        @else
                            <a href="{{ url('/catalog') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-bag me-1" aria-hidden="true"></i>{{ __('messages.cart.view_catalog') }}
                            </a>
                        @endif
                    </div>

                    {{-- Garantías Reposa+ --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex flex-column gap-2 small text-muted">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-success fs-6" aria-hidden="true"></i>
                                <span>{{ __('messages.trust.100_nights') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-truck text-primary fs-6" aria-hidden="true"></i>
                                <span>{{ __('messages.trust.free_shipping') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-lock-fill text-secondary fs-6" aria-hidden="true"></i>
                                <span>{{ __('messages.orders.show.payment_card') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Dirección y Asesoría de Descanso --}}
                <section class="order-section-card" aria-labelledby="shipping-destination-title">
                    <h2 id="shipping-destination-title" class="order-section-title mb-3 fs-6">
                        {{ __('messages.orders.show.shipping_destination') }}
                    </h2>

                    <div class="small text-muted mb-2">
                        <strong class="text-dark d-block">{{ $order->customer_name }}</strong>
                        <span>{{ $order->customer_email }}</span>
                        @if($order->shipping_phone)
                            <div class="mt-1"><i class="bi bi-telephone me-1 text-primary"></i>{{ $order->shipping_phone }}</div>
                        @endif
                    </div>

                    @if($order->shipping_street)
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-geo-alt me-1 text-primary" aria-hidden="true"></i>{{ $order->shipping_street }}, {{ $order->shipping_zip_code }} {{ $order->shipping_city }} {{ $order->shipping_province ? '(' . $order->shipping_province . ')' : '' }}
                        </p>
                    @elseif($order->user && $order->user->addresses && $order->user->addresses->count() > 0)
                        @php $mainAddr = $order->user->addresses->where('is_main', true)->first() ?? $order->user->addresses->first(); @endphp
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-geo-alt me-1 text-primary" aria-hidden="true"></i>{{ $mainAddr->street }}, {{ $mainAddr->zip_code }} {{ $mainAddr->city }}
                        </p>
                    @else
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-geo-alt me-1 text-primary" aria-hidden="true"></i>{{ __('messages.orders.show.no_address') }}
                        </p>
                    @endif

                    <div class="order-support-box">
                        <div class="d-flex align-items-center mb-1 text-dark fw-semibold">
                            <i class="bi bi-headset me-2 text-primary" aria-hidden="true"></i>{{ __('messages.orders.show.need_help_title') }}
                        </div>
                        <p class="mb-0 small">{{ __('messages.orders.show.need_help_desc') }}</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
