@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold mb-4">Resumen del Sistema</h2>
        
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase opacity-75">Ventas Totales</h6>
                        <h3 class="fw-bold mb-0">{{ number_format($totalRevenue, 2) }}€</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase opacity-75">Pedidos Realizados</h6>
                        <h3 class="fw-bold mb-0">{{ $totalOrders }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <h6 class="text-uppercase opacity-75">Productos Activos</h6>
                        <h3 class="fw-bold mb-0">{{ $totalProducts }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Pedidos Recientes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->total_amount, 2) }}€</td>
                                <td><span class="badge bg-warning text-dark">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
