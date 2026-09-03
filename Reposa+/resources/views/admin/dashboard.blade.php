@extends('layouts.app')

@section('title', __('messages.admin.dashboard.title'))

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Navigation Sidebar --}}
        <div class="col-lg-3">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main Dashboard Content --}}
        <div class="col-lg-9">
            {{-- Dashboard Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1 text-navy">{{ __('messages.admin.dashboard.system_summary') }}</h1>
                    <p class="text-muted small mb-0">{{ __('messages.admin.dashboard.title') }} &middot; Reposa+ E-commerce</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted border px-2 py-1 small">
                        <i class="bi bi-clock me-1"></i> {{ now()->format('d/m/Y H:i') }}
                    </span>
                    <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-receipt me-1"></i> {{ __('messages.admin.sidebar.orders') }}
                    </a>
                </div>
            </div>

            {{-- Quieter KPI Cards --}}
            <div class="row g-3 mb-4">
                {{-- Total Revenue --}}
                <div class="col-6 col-md-3">
                    <div class="card border shadow-sm rounded-3 h-100 kpi-card-quieter">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    {{ __('messages.admin.dashboard.revenue') }}
                                </span>
                                <span class="kpi-icon-pill bg-primary-subtle text-primary">
                                    <i class="bi bi-currency-euro"></i>
                                </span>
                            </div>
                            <div class="h4 fw-bold mb-0 text-navy tabular-nums">{{ number_format($totalRevenue, 2) }}€</div>
                            <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.admin.dashboard.sales_eur') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Total Orders --}}
                <div class="col-6 col-md-3">
                    <div class="card border shadow-sm rounded-3 h-100 kpi-card-quieter">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    {{ __('messages.admin.dashboard.orders') }}
                                </span>
                                <span class="kpi-icon-pill bg-success-subtle text-success">
                                    <i class="bi bi-receipt"></i>
                                </span>
                            </div>
                            <div class="h4 fw-bold mb-0 text-navy tabular-nums">{{ $totalOrders }}</div>
                            <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.admin.orders.title') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Total Products --}}
                <div class="col-6 col-md-3">
                    <div class="card border shadow-sm rounded-3 h-100 kpi-card-quieter">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    {{ __('messages.admin.dashboard.products') }}
                                </span>
                                <span class="kpi-icon-pill bg-info-subtle text-info">
                                    <i class="bi bi-box-seam"></i>
                                </span>
                            </div>
                            <div class="h4 fw-bold mb-0 text-navy tabular-nums">{{ $totalProducts }}</div>
                            <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.admin.products.title') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Average Ticket --}}
                <div class="col-6 col-md-3">
                    <div class="card border shadow-sm rounded-3 h-100 kpi-card-quieter">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    {{ __('messages.admin.dashboard.avg_ticket') }}
                                </span>
                                <span class="kpi-icon-pill bg-warning-subtle text-warning">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </span>
                            </div>
                            <div class="h4 fw-bold mb-0 text-navy tabular-nums">
                                {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }}€
                            </div>
                            <span class="text-muted" style="font-size: 0.72rem;">{{ __('messages.admin.dashboard.avg_ticket') }} / orden</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart & Status Section --}}
            <div class="row g-3 mb-4">
                {{-- Sales Chart --}}
                <div class="col-lg-8">
                    <div class="card border shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                            <h2 class="h6 mb-0 fw-bold text-navy">
                                <i class="bi bi-bar-chart-line me-2 text-primary"></i>{{ __('messages.admin.dashboard.monthly_sales') }}
                            </h2>
                            <span class="badge bg-light text-muted border">6 Meses</span>
                        </div>
                        <div class="card-body p-3">
                            <div style="height: 220px;">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Orders by Status --}}
                <div class="col-lg-4">
                    <div class="card border shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3 px-3 border-bottom">
                            <h2 class="h6 mb-0 fw-bold text-navy">
                                <i class="bi bi-pie-chart me-2 text-primary"></i>{{ __('messages.admin.dashboard.orders_by_status') }}
                            </h2>
                        </div>
                        <div class="card-body p-3">
                            @forelse($ordersByStatus as $status => $count)
                                @php
                                    $pct = $totalOrders > 0 ? round(($count / $totalOrders) * 100) : 0;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge admin-badge admin-badge-{{ $status }}">
                                            {{ \App\Models\Order::getStatusLabel($status) }}
                                        </span>
                                        <div class="text-end">
                                            <span class="fw-bold tabular-nums small">{{ $count }}</span>
                                            <span class="text-muted small">({{ $pct }}%)</span>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 4px;" role="progressbar" aria-label="{{ \App\Models\Order::getStatusLabel($status) }}" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-primary" style="width: {{ $pct }}%;"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-4 mb-0 small">{{ __('messages.admin.dashboard.no_orders') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Products & Favorites --}}
            <div class="row g-3 mb-4">
                {{-- Top Selling Products --}}
                <div class="col-md-6">
                    <div class="card border shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3 px-3 border-bottom">
                            <h2 class="h6 mb-0 fw-bold text-navy">
                                <i class="bi bi-trophy me-2 text-warning"></i>{{ __('messages.admin.dashboard.top_selling') }}
                            </h2>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-admin mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>{{ __('messages.admin.products.name') }}</th>
                                            <th class="text-end">{{ __('messages.admin.products.stock') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topSellingProducts as $i => $item)
                                        <tr>
                                            <td>
                                                <span class="badge rounded-circle {{ $i === 0 ? 'bg-warning text-dark' : 'bg-light text-muted border' }}" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                                    {{ $i + 1 }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-truncate" style="max-width: 200px;">
                                                {{ $item->product->name ?? 'N/A' }}
                                            </td>
                                            <td class="text-end fw-bold tabular-nums text-navy">
                                                {{ $item->total_sold }} uds
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3 small">{{ __('messages.admin.dashboard.no_sales') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Favorited Products --}}
                <div class="col-md-6">
                    <div class="card border shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white py-3 px-3 border-bottom">
                            <h2 class="h6 mb-0 fw-bold text-navy">
                                <i class="bi bi-heart-fill text-danger me-2"></i>{{ __('messages.admin.dashboard.top_favorites') }}
                            </h2>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-admin mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>{{ __('messages.admin.products.name') }}</th>
                                            <th class="text-end">Favoritos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topExpectedProducts as $i => $product)
                                        <tr>
                                            <td>
                                                <span class="badge rounded-circle {{ $i === 0 ? 'bg-danger text-white' : 'bg-light text-muted border' }}" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                                    {{ $i + 1 }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold text-truncate" style="max-width: 200px;">
                                                {{ $product->name }}
                                            </td>
                                            <td class="text-end fw-bold tabular-nums text-danger">
                                                <i class="bi bi-heart-fill me-1 small"></i>{{ $product->favorited_by_count }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3 small">{{ __('messages.admin.dashboard.no_favorites') }}</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Completed Orders --}}
            <div class="card border shadow-sm rounded-3">
                <div class="card-header bg-white py-3 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h2 class="h6 mb-0 fw-bold text-navy">
                        <i class="bi bi-check2-circle text-success me-2"></i>{{ __('messages.admin.dashboard.recent_completed') }}
                    </h2>
                    <a href="{{ route('admin.orders') }}" class="btn btn-link text-decoration-none text-muted small p-0">
                        {{ __('messages.admin.orders.title') }} &rarr;
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-admin align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>{{ __('messages.admin.orders.customer') }}</th>
                                    <th>{{ __('messages.admin.dashboard.date') }}</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">{{ __('messages.admin.dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCompleted as $order)
                                <tr>
                                    <td class="fw-bold tabular-nums text-primary">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $order->user->name }}</span>
                                    </td>
                                    <td class="text-muted small tabular-nums">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end fw-bold tabular-nums text-navy">{{ number_format($order->total_amount, 2) }}€</td>
                                    <td class="text-center">
                                        <span class="badge admin-badge admin-badge-{{ $order->status }}">
                                            {{ \App\Models\Order::getStatusLabel($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4 small">{{ __('messages.admin.dashboard.no_completed') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! $chartLabels->toJson() !!},
                datasets: [{
                    label: '{{ __('messages.admin.dashboard.sales_eur') }}',
                    data: {!! $chartData->toJson() !!},
                    backgroundColor: 'rgba(24, 36, 71, 0.85)',
                    hoverBackgroundColor: 'rgba(79, 70, 229, 0.95)',
                    borderWidth: 0,
                    borderRadius: 4,
                    maxBarThickness: 38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#182447',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(context) {
                                return ' ' + Number(context.raw).toLocaleString('es-ES', { minimumFractionDigits: 2 }) + '€';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#64748b' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748b',
                            callback: function(v) { return v + '€'; }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
