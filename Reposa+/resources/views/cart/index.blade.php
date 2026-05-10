@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-cart3 me-2"></i>Tu Carrito de Almohadas
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($cartItems->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <p class="mt-3 fs-5 text-muted">Vaya, parece que tu descanso aún no ha empezado. ¡Añade alguna almohada!</p>
                            <a href="/catalog" class="btn btn-primary px-4 rounded-pill">Ver Catálogo</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end pe-4">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="bi bi-archive text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $item->product->name }}</h6>
                                                    <small class="text-muted">{{ $item->product->material }} - {{ $item->product->firmness }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 150px;">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center justify-content-center">
                                                @csrf
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm text-center me-2" style="width: 60px;">
                                                <button type="submit" class="btn btn-sm btn-outline-primary border-0">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end">{{ number_format($item->product->price, 2) }}€</td>
                                        <td class="text-end fw-bold">{{ number_format($item->product->price * $item->quantity, 2) }}€</td>
                                        <td class="text-center">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 100px; z-index: 10;">
                <div class="card-body py-4">
                    <h5 class="fw-bold mb-4">Resumen del Pedido</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>{{ number_format($total, 2) }}€</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Envío</span>
                        <span class="text-success fw-bold">GRATIS</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Total</span>
                        <span class="h4 fw-bold text-primary">{{ number_format($total, 2) }}€</span>
                    </div>

                    @if(!$cartItems->isEmpty())
                        @guest
                        <a href="{{ route('cart.login') }}" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm text-decoration-none">
                            FINALIZAR COMPRA <i class="bi bi-chevron-right ms-2"></i>
                        </a>
                        @endguest

                        @auth
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm">
                                FINALIZAR COMPRA <i class="bi bi-chevron-right ms-2"></i>
                            </button>
                        </form>
                        @endauth
                    @endif
                    
                    <div class="mt-4 text-center">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-shield-check text-success me-1"></i> Pago 100% Seguro
                        </p>
                        <p class="small text-muted">
                            Envío en 24-48h por Reposa+ Logística
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
