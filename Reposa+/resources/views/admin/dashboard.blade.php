@extends('layouts.app')

@section('title', __('messages.admin.dashboard.title'))

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold mb-4">{{ __('messages.admin.dashboard.system_summary') }}</h2>

        {{-- KPI Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase opacity-75 small">{{ __('messages.admin.dashboard.revenue') }}</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($totalRevenue, 2) }}€</h3>
                            </div>
                            <i class="bi bi-currency-euro fs-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase opacity-75 small">{{ __('messages.admin.dashboard.orders') }}</h6>
                                <h3 class="fw-bold mb-0">{{ $totalOrders }}</h3>
                            </div>
                            <i class="bi bi-receipt fs-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase opacity-75 small">{{ __('messages.admin.dashboard.products') }}</h6>
                                <h3 class="fw-bold mb-0">{{ $totalProducts }}</h3>
                            </div>
                            <i class="bi bi-box-seam fs-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase opacity-75 small">{{ __('messages.admin.dashboard.avg_ticket') }}</h6>
                                <h3 class="fw-bold mb-0">{{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00' }}€</h3>
                            </div>
                            <i class="bi bi-graph-up-arrow fs-1 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- Sales Chart --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('messages.admin.dashboard.monthly_sales') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            {{-- Orders by Status --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">{{ __('messages.admin.dashboard.orders_by_status') }}</h5>
                    </div>
                    <div class="card-body">
                        @forelse($ordersByStatus as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-{{ \App\Models\Order::getStatusColor($status) }} me-2">
                                        {{ \App\Models\Order::getStatusLabel($status) }}
                                    </span>
                                </div>
                                <span class="fw-bold">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-muted text-center mb-0">{{ __('messages.admin.dashboard.no_orders') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- Top Selling Products --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>{{ __('messages.admin.dashboard.top_selling') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th class="text-end">Unidades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topSellingProducts as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold">{{ $item->total_sold }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('messages.admin.dashboard.no_sales') }}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Expected (Favorites) --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-heart-fill text-danger me-2"></i>{{ __('messages.admin.dashboard.top_favorites') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th class="text-end">Favoritos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topExpectedProducts as $i => $product)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-end fw-bold">{{ $product->favorited_by_count }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('messages.admin.dashboard.no_favorites') }}</td>
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('messages.admin.dashboard.recent_completed') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>{{ __('messages.admin.dashboard.date') }}</th>
                                <th>Total</th>
                                <th>{{ __('messages.admin.dashboard.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCompleted as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->total_amount, 2) }}€</td>
                                <td>
                                    <span class="badge bg-{{ \App\Models\Order::getStatusColor($order->status) }}">
                                        {{ \App\Models\Order::getStatusLabel($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">{{ __('messages.admin.dashboard.no_completed') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                    backgroundColor: 'rgba(67, 56, 202, 0.8)',
                    borderColor: 'rgba(67, 56, 202, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v + '€' }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
