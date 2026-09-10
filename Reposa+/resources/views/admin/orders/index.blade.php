@extends('layouts.app')

@section('title', __('messages.admin.orders.title'))

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Navigation Sidebar --}}
        <div class="col-lg-3">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main Orders View --}}
        <div class="col-lg-9">
            {{-- Header & Total Count --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1 text-navy">{{ __('messages.admin.orders.title') }}</h1>
                    <p class="text-muted small mb-0">{{ __('messages.admin.orders.history') }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted border px-2 py-1 tabular-nums small">
                        {{ $orders->total() }} pedidos
                    </span>
                </div>
            </div>

            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            {{-- Operational Filter & Search Bar --}}
            <div class="card border shadow-sm rounded-3 mb-3">
                <div class="card-body p-3">
                    <form action="{{ route('admin.orders') }}" method="GET" class="row g-2 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0" 
                                       placeholder="{{ __('messages.admin.orders.search_placeholder') }}" 
                                       value="{{ request('q') }}">
                                @if(request('q'))
                                    <a href="{{ route('admin.orders', request()->except('q', 'page')) }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                                        <i class="bi bi-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Status Filter Select --}}
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">{{ __('messages.admin.orders.filter_all') }}</option>
                                @foreach(['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'] as $st)
                                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                                        {{ \App\Models\Order::getStatusLabel($st) }} 
                                        @if(isset($statusCounts[$st])) ({{ $statusCounts[$st] }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Carrier Filter Select --}}
                        <div class="col-md-2">
                            <select name="carrier" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todos los transportistas</option>
                                <option value="Correos Express" {{ request('carrier') === 'Correos Express' ? 'selected' : '' }}>Correos Express</option>
                            </select>
                        </div>

                        {{-- Action Buttons & Date Range Toggle --}}
                        <div class="col-md-3 d-flex gap-2 justify-content-md-end">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 flex-md-grow-0">
                                <i class="bi bi-funnel me-1"></i> Filtrar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#dateFilterCollapse" aria-expanded="{{ request('date_from') || request('date_to') ? 'true' : 'false' }}" title="Rango de fechas">
                                <i class="bi bi-calendar3"></i>
                            </button>
                            @if(request('status') || request('q') || request('carrier') || request('date_from') || request('date_to'))
                                <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-secondary" title="{{ __('messages.catalog.clear_filters') }}">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>

                        {{-- Collapsible Date Filters --}}
                        <div class="col-12 collapse {{ request('date_from') || request('date_to') ? 'show' : '' }} mt-2 pt-2 border-top" id="dateFilterCollapse">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">Desde</span>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light text-muted">Hasta</span>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2 text-end">
                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                        Aplicar fechas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Compact Operational Orders Table --}}
            <div class="card border shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-admin align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 85px;">ID</th>
                                    <th>{{ __('messages.admin.orders.customer') }}</th>
                                    <th>{{ __('messages.admin.orders.products') }}</th>
                                    <th class="text-end" style="width: 100px;">Total</th>
                                    <th style="width: 120px;">Fecha</th>
                                    <th style="width: 155px;">Estado</th>
                                    <th style="width: 165px;">{{ __('messages.admin.orders.shipment_tracking') }}</th>
                                    <th class="text-end" style="width: 110px;">{{ __('messages.admin.orders.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    {{-- ID --}}
                                    <td class="fw-bold tabular-nums text-primary text-nowrap">
                                        #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                    </td>

                                    {{-- Customer --}}
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 150px;">
                                            {{ $order->customer_name }}
                                            @if($order->isGuest())
                                                <span class="badge bg-secondary-subtle text-secondary border ms-1" style="font-size: 0.65rem;">Invitado</span>
                                            @endif
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 150px;">{{ $order->customer_email }}</div>
                                    </td>

                                    {{-- Products --}}
                                    <td>
                                        <div class="small">
                                            @php $itemCount = $order->orderItems->sum('quantity'); @endphp
                                            <span class="badge bg-light text-dark border me-1 tabular-nums">{{ $itemCount }} uds</span>
                                            <span class="text-muted text-truncate d-inline-block align-bottom" style="max-width: 170px;" title="{{ $order->orderItems->map(fn($it) => $it->quantity . 'x ' . ($it->product->name ?? 'N/A'))->join(', ') }}">
                                                {{ $order->orderItems->first()?->product?->name ?? 'Artículos' }}
                                                @if($order->orderItems->count() > 1)
                                                    <span class="opacity-75">+{{ $order->orderItems->count() - 1 }}</span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="text-end fw-bold tabular-nums text-navy text-nowrap">
                                        {{ number_format($order->total_amount, 2) }}€
                                    </td>

                                    {{-- Date --}}
                                    <td class="text-muted small tabular-nums text-nowrap">
                                        <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                        <div style="font-size: 0.7rem;">{{ $order->created_at->format('H:i') }}</div>
                                    </td>

                                    {{-- Status & Quick Transitions --}}
                                    <td>
                                        @php $transitions = \App\Models\Order::getAllowedTransitions($order->status); @endphp
                                        @if(!empty($transitions))
                                            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm py-1 px-2 border-0 bg-light fw-semibold" 
                                                        style="font-size: 0.75rem; border-radius: 6px;" 
                                                        onchange="if(confirm('¿Confirmar cambio de estado de pedido a ' + this.options[this.selectedIndex].text.replace(/[→\s]+/g, ' ').trim() + '?')) { this.form.submit(); } else { this.value = '{{ $order->status }}'; }"
                                                        aria-label="Cambiar estado del pedido #{{ $order->id }}">
                                                    <option value="{{ $order->status }}" disabled selected>
                                                        {{ \App\Models\Order::getStatusLabel($order->status) }}
                                                    </option>
                                                    @foreach($transitions as $transition)
                                                        <option value="{{ $transition }}">
                                                            &rarr; {{ \App\Models\Order::getStatusLabel($transition) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            <span class="badge admin-badge admin-badge-{{ $order->status }}">
                                                {{ \App\Models\Order::getStatusLabel($order->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Shipment / Paquetería --}}
                                    <td>
                                        @if($order->shipment)
                                            <div class="d-flex flex-column gap-1">
                                                <div>
                                                    <span class="badge bg-{{ $order->shipment->status_color }}-subtle text-{{ $order->shipment->status_color }} border" style="font-size: 0.7rem;">
                                                        {{ $order->shipment->status_label }}
                                                    </span>
                                                </div>
                                                <div class="font-monospace text-muted" style="font-size: 0.68rem;">
                                                    <i class="bi bi-upc me-1"></i>{{ $order->shipment->tracking_number }}
                                                </div>
                                                <div class="d-flex gap-1 mt-1">
                                                    @if($order->shipment->status !== \App\Models\Shipment::STATUS_DELIVERED)
                                                        <form action="{{ route('admin.shipments.advance', $order->shipment) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 0.68rem;" title="{{ __('messages.admin.orders.advance_status') }}">
                                                                <i class="bi bi-fast-forward-fill me-1"></i>Avanzar
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('admin.shipments.label', $order->shipment) }}" target="_blank" class="btn btn-sm btn-outline-dark bg-white shadow-2xs py-0 px-2 fw-semibold d-inline-flex align-items-center" style="font-size: 0.68rem;" title="Imprimir albarán térmico A6 (10x15cm) con código Code 128">
                                                        <i class="bi bi-printer-fill text-primary me-1"></i>Etiqueta A6
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">&mdash;</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end text-nowrap">
                                        <div class="btn-group btn-group-sm">
                                            @if(in_array($order->status, ['completed', 'delivered']) && $order->payment_intent_id && !$order->refunds()->where('status', 'succeeded')->exists())
                                                <button class="btn btn-sm btn-outline-danger py-0 px-2" type="button"
                                                        data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}"
                                                        title="{{ __('messages.admin.orders.refund') }}"
                                                        aria-label="{{ __('messages.admin.orders.refund') }}">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                    <span class="d-none d-xl-inline ms-1">{{ __('messages.admin.orders.refund') }}</span>
                                                </button>
                                            @elseif($order->status === 'refunded')
                                                <span class="badge admin-badge admin-badge-refunded py-1 px-2">
                                                    {{ __('messages.admin.orders.refunded') }}
                                                </span>
                                            @else
                                                <span class="text-muted small px-2">&mdash;</span>
                                            @endif
                                        </div>

                                        {{-- Refund Modal --}}
                                        @if(in_array($order->status, ['completed', 'delivered']) && $order->payment_intent_id && !$order->refunds()->where('status', 'succeeded')->exists())
                                            <div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1" aria-labelledby="refundModalLabel{{ $order->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered text-start">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('admin.orders.refund', $order) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header py-3 px-4 bg-light border-bottom">
                                                                <h5 class="modal-title h6 fw-bold text-navy mb-0" id="refundModalLabel{{ $order->id }}">
                                                                    <i class="bi bi-arrow-counterclockwise text-danger me-2"></i>{{ __('messages.admin.orders.refund_title') }}{{ $order->id }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <p class="mb-2">
                                                                    {{ __('messages.admin.orders.refund_desc') }} 
                                                                    <strong class="text-danger">{{ number_format($order->total_amount, 2) }}€</strong> 
                                                                    {{ __('messages.admin.orders.refund_to_client') }} 
                                                                    <strong>{{ $order->customer_name }}</strong>.
                                                                </p>
                                                                <p class="text-muted small mb-3">
                                                                    <i class="bi bi-info-circle me-1"></i>{{ __('messages.admin.orders.stock_restore') }}
                                                                </p>
                                                                <div class="mb-0">
                                                                    <label for="reason{{ $order->id }}" class="form-label small fw-semibold">{{ __('messages.admin.orders.reason_optional') }}</label>
                                                                    <input type="text" class="form-control form-control-sm" id="reason{{ $order->id }}" name="reason" maxlength="500" placeholder="Ej: Producto defectuoso, solicitud del cliente...">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer py-2 px-4 bg-light border-top">
                                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ __('messages.admin.orders.cancel') }}</button>
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Confirmar reembolso de {{ number_format($order->total_amount, 2) }}€?')">
                                                                    <i class="bi bi-check2 me-1"></i>{{ __('messages.admin.orders.confirm_refund') }}
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="py-3">
                                            <i class="bi bi-inbox fs-2 text-muted opacity-50 d-block mb-2"></i>
                                            <p class="mb-0 small">{{ __('messages.admin.orders.no_orders_found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Compact Pagination --}}
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    Mostrando {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} de {{ $orders->total() }} pedidos
                </span>
                <div>
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
