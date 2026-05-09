@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                <li class="breadcrumb-item"><a href="/catalog">Catálogo</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden rounded-4">
                    <img src="/images/pillow-detail.png" class="img-fluid" alt="{{ $product->name }}">
                </div>
                <div class="row mt-3 g-2">
                    <div class="col-3">
                        <img src="https://placehold.co/200x200/182447/ffffff?text=Vista+1" class="img-fluid rounded border cursor-pointer" alt="thumbnail">
                    </div>
                    <div class="col-3">
                        <img src="https://placehold.co/200x200/182447/ffffff?text=Vista+2" class="img-fluid rounded border cursor-pointer" alt="thumbnail">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <div class="ps-md-4">
                    <div class="mb-3">
                        @foreach($product->categories as $category)
                            <span class="badge bg-light text-primary border me-1">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <h1 class="display-5 fw-bold mb-3">{{ $product->name }}</h1>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="text-warning me-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </div>
                        <span class="text-muted">(124 reseñas de clientes)</span>
                    </div>

                    <h2 class="display-6 fw-bold text-primary mb-4">{{ number_format($product->price, 2) }}€</h2>

                    <div class="mb-4">
                        <h6 class="fw-bold">Especificaciones clave:</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>Material:</strong> {{ $product->material }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>Firmeza:</strong> {{ $product->firmness }}</li>
                            <li><i class="bi bi-check2-circle text-success me-2"></i><strong>Dimensiones:</strong> {{ $product->dimensions }}</li>
                        </ul>
                    </div>

                    <p class="text-muted mb-5 lead">{{ $product->description }}</p>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-flex gap-3 mb-5">
                        @csrf
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                            <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="10">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary flex-grow-1 py-3 fw-bold">
                            <i class="bi bi-cart-plus me-2"></i>Añadir al Carrito
                        </button>
                        <button type="button" class="btn btn-outline-danger py-3 px-4" title="Añadir a Favoritos">
                            <i class="bi bi-heart"></i>
                        </button>
                    </form>

                    <div class="card bg-light border-0 p-4 rounded-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-truck fs-3 text-primary me-3"></i>
                                    <div>
                                        <small class="d-block fw-bold">Envío Gratis</small>
                                        <small class="text-muted">En pedidos > 50€</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-return-left fs-3 text-primary me-3"></i>
                                    <div>
                                        <small class="d-block fw-bold">30 Días Pruebas</small>
                                        <small class="text-muted">Devolución gratuita</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
