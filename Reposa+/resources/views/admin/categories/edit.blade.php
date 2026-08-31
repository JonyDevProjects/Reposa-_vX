@extends('layouts.app')

@section('title', __('messages.admin.categories.edit_title'))

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group shadow-sm mb-4">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories') }}" class="list-group-item list-group-item-action active">
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Editar Categoría: {{ $category->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.admin.categories.name_label') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">{{ __('messages.admin.categories.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
