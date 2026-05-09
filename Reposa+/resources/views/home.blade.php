@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section text-center position-relative overflow-hidden">
        <div class="container position-relative z-1">
            <h1 class="display-3 fw-bold mb-3">Tu descanso, nuestra prioridad</h1>
            <p class="lead mb-5 opacity-75">Descubre la colección de almohadas ergonómicas diseñadas para transformar tus noches y cuidar tu salud cervical.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/catalog" class="btn btn-secondary btn-lg px-5">Ver Catálogo</a>
                <a href="#featured" class="btn btn-outline-light btn-lg px-5">Más Vendidos</a>
            </div>
        </div>
        <!-- Decorative elements could go here -->
    </section>

    <!-- Categories Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Explora por Categoría</h2>
                <div class="bg-secondary mx-auto" style="height: 3px; width: 60px;"></div>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($categories as $category)
                    <div class="col-6 col-md-3">
                        <a href="/catalog?category={{ $category->slug }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm text-center p-4 hover-lift">
                                <div class="mb-3 text-primary">
                                    <i class="bi bi-moon-stars fs-1"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-0">{{ $category->name }}</h5>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Nuestros Productos Destacados</h2>
                <p class="text-muted">La mejor tecnología para tu confort nocturno.</p>
            </div>
            <div class="row g-4">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3">
                        <div class="card card-product h-100 shadow-sm border-0 position-relative">
                            <img src="https://placehold.co/400x300/182447/ffffff?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-primary border">{{ $product->material }}</span>
                                    <span class="text-muted small"><i class="bi bi-star-fill text-warning"></i> 4.8</span>
                                </div>
                                <h5 class="card-title fw-bold">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none text-dark stretched-link">{{ $product->name }}</a>
                                </h5>
                                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3 position-relative" style="z-index: 2;">
                                    <span class="fs-4 fw-bold text-primary">{{ number_format($product->price, 2) }}€</span>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-cart-plus"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="/catalog" class="btn btn-primary px-5">Ver toda la tienda</a>
            </div>
        </div>
    </section>

    <!-- Value Propositions -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <i class="bi bi-truck fs-1 mb-3"></i>
                    <h5 class="fw-bold">Envío Express</h5>
                    <p class="opacity-75 small">Recibe tu almohada en 24/48h en la puerta de tu casa.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-shield-check fs-1 mb-3"></i>
                    <h5 class="fw-bold">Garantía de Descanso</h5>
                    <p class="opacity-75 small">Si no duermes mejor en 30 días, te devolvemos el dinero.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-heart-pulse fs-1 mb-3"></i>
                    <h5 class="fw-bold">Salud Certificada</h5>
                    <p class="opacity-75 small">Todos nuestros productos están avalados por expertos en ergonomía.</p>
                </div>
            </div>
        </div>
    </section>

    <style>
        .hover-lift {
            transition: transform 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-10px);
            background-color: #182447 !important;
        }
        .hover-lift:hover h5, .hover-lift:hover i {
            color: white !important;
        }
        .hero-section {
            background-image: linear-gradient(rgba(24, 36, 71, 0.8), rgba(24, 36, 71, 0.8)), url('/images/hero.png');
            background-size: cover;
            background-position: center;
        }
    </style>
@endsection
