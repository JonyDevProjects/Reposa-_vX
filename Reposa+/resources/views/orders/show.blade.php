@extends('layouts.app')

@section('title', 'Detalles del Pedido #' . $order->id)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Detalles del Pedido #{{ $order->id }}</h3>
                <a href="{{ route('profile') }}#orders" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver a Mis Pedidos</a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-5 mb-4">
                <div class="row mb-4">
                    <div class="col-sm-4">
                        <h6 class="text-muted small fw-bold">Fecha del Pedido</h6>
                        <p class="mb-0">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-sm-4">
                        <h6 class="text-muted small fw-bold">Estado</h6>
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'warning' }} px-3 py-2">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="col-sm-4 text-sm-end mt-3 mt-sm-0">
                        <h6 class="text-muted small fw-bold">Total</h6>
                        <h4 class="mb-0 fw-bold">{{ number_format($order->total_amount, 2) }}€</h4>
                    </div>
                </div>

                <hr class="text-muted">

                <h5 class="fw-bold mt-4 mb-4">Artículos Comprados</h5>
                <div class="table-responsive">
                    <table class="table align-middle text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Producto</th>
                                <th scope="col" class="text-center">Cantidad</th>
                                <th scope="col" class="text-end">Precio U.</th>
                                <th scope="col" class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex justify-content-center align-items-center text-muted" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                                                <small class="text-muted">Firmeza: {{ $item->product->firmness ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ number_format($item->price_at_purchase, 2) }}€</td>
                                    <td class="text-end fw-bold">{{ number_format($item->price_at_purchase * $item->quantity, 2) }}€</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end pt-3">
                                    <h5 class="mb-0 fw-bold text-muted">Total del Pedido:</h5>
                                </td>
                                <td class="text-end pt-3">
                                    <h4 class="mb-0 fw-bold text-primary">{{ number_format($order->total_amount, 2) }}€</h4>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
