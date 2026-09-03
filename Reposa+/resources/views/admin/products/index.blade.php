@extends('layouts.app')

@section('title', __('messages.admin.products.title'))

@section('content')
<div class="container py-4">
    <div class="row g-4">
        {{-- Navigation Sidebar --}}
        <div class="col-lg-3">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main Products View --}}
        <div class="col-lg-9">
            {{-- Header & New Product CTA --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1 text-navy">{{ __('messages.admin.products.title') }}</h1>
                    <p class="text-muted small mb-0">{{ __('messages.admin.products.total_count', ['count' => $totalProductsCount ?? $products->total()]) }}</p>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('messages.admin.products.new') }}
                </a>
            </div>

            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            {{-- Operational Search Bar --}}
            <div class="card border shadow-sm rounded-3 mb-3">
                <div class="card-body p-3">
                    <form action="{{ route('admin.products') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0" 
                                       placeholder="Buscar por nombre o descripción..." 
                                       value="{{ request('q') }}">
                                @if(request('q'))
                                    <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary" aria-label="Limpiar búsqueda">
                                        <i class="bi bi-x"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 d-flex gap-2 justify-content-md-end">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1 flex-md-grow-0">
                                <i class="bi bi-funnel me-1"></i> Buscar
                            </button>
                            @if(request('q'))
                                <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-secondary" title="Limpiar">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Compact Products Table --}}
            <div class="card border shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-admin align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Img</th>
                                    <th>{{ __('messages.admin.products.name') }}</th>
                                    <th style="width: 140px;">Categorías</th>
                                    <th class="text-end" style="width: 100px;">{{ __('messages.admin.products.price') }}</th>
                                    <th class="text-center" style="width: 110px;">{{ __('messages.admin.products.stock') }}</th>
                                    <th class="text-end" style="width: 90px;">{{ __('messages.admin.products.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    {{-- Image --}}
                                    <td>
                                        <img src="{{ $product->image_url ?: '/images/product-placeholder.svg' }}" 
                                             onerror="this.onerror=null; this.src='/images/product-placeholder.svg';" 
                                             alt="{{ $product->name }}" 
                                             class="rounded border" 
                                             style="width: 36px; height: 36px; object-fit: cover;">
                                    </td>

                                    {{-- Name & Translatable Firmness / Material info --}}
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            @if($product->firmness)
                                                <span class="me-2"><i class="bi bi-sliders me-1"></i>{{ $product->firmness }}</span>
                                            @endif
                                            @if($product->material)
                                                <span><i class="bi bi-layers me-1"></i>{{ $product->material }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Categories --}}
                                    <td>
                                        @forelse($product->categories as $cat)
                                            <span class="badge bg-light text-muted border mb-1" style="font-size: 0.7rem;">
                                                {{ $cat->name }}
                                            </span>
                                        @empty
                                            <span class="text-muted small">&mdash;</span>
                                        @endforelse
                                    </td>

                                    {{-- Price --}}
                                    <td class="text-end fw-bold tabular-nums text-navy">
                                        {{ number_format($product->price, 2) }}€
                                    </td>

                                    {{-- Stock --}}
                                    <td class="text-center">
                                        @if($product->stock > 10)
                                            <span class="badge admin-badge admin-badge-completed tabular-nums">
                                                {{ $product->stock }} uds
                                            </span>
                                        @elseif($product->stock > 0)
                                            <span class="badge admin-badge admin-badge-pending tabular-nums">
                                                {{ $product->stock }} uds
                                            </span>
                                        @else
                                            <span class="badge admin-badge admin-badge-refunded tabular-nums">
                                                0 uds (Agotado)
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="btn btn-sm btn-outline-secondary py-1 px-2"
                                               title="Editar producto"
                                               aria-label="Editar producto {{ $product->name }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.products.delete', $product) }}" method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('messages.admin.products.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger py-1 px-2"
                                                        title="Eliminar producto"
                                                        aria-label="Eliminar producto {{ $product->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-box-seam fs-2 text-muted opacity-50 d-block mb-2"></i>
                                        <p class="mb-0 small">No se encontraron productos en el catálogo</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="text-muted small">
                    Mostrando {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} de {{ $products->total() }} productos
                </span>
                <div>
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
