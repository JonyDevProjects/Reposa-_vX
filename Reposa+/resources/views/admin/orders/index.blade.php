@extends('layouts.app')

@section('title', 'Historial Global de Pedidos')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group shadow-sm mb-4">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-tags me-2"></i> Categorías
            </a>
            <a href="{{ route('admin.products') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-box-seam me-2"></i> Productos
            </a>
            <a href="{{ route('admin.orders') }}" class="list-group-item list-group-item-action active">
                <i class="bi bi-cart-check me-2"></i> Pedidos Globales
            </a>
        </div>
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold mb-4">Historial Global de Transacciones</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <small class="text-muted">{{ $order->user->email }}</small>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach($order->orderItems as $item)
                                        <li>{{ $item->quantity }}x {{ $item->product->name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="fw-bold text-primary">{{ number_format($order->total_amount, 2) }}€</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <select class="form-select form-select-sm" disabled>
                                        <option selected>{{ ucfirst($order->status) }}</option>
                                    </select>
                                </td>
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
