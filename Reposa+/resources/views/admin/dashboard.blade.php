@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group shadow-sm mb-4">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action active">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-tags me-2"></i> Categorías
            </a>
            <a href="{{ route('admin.products') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-box-seam me-2"></i> Productos
            </a>
            <a href="{{ route('admin.orders') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-cart-check me-2"></i> Pedidos Globales
            </a>
        </div>
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

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Pedidos Recientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
            
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Top Almohadas Más Deseadas</h5>
                    </div>
                    <div class="card-body">
                        @if($topFavoritedProducts->isEmpty())
                            <p class="text-muted text-center py-4">No hay datos de favoritos aún.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($topFavoritedProducts as $product)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded me-3 text-center" style="width: 45px; height: 40px;">
                                                <i class="bi bi-heart-fill text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $product->name }}</h6>
                                                <small class="text-muted">{{ number_format($product->price, 2) }}€</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            {{ $product->favorited_by_count }} <i class="bi bi-person-heart ms-1"></i>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
