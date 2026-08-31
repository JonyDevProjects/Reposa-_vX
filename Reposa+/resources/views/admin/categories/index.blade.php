@extends('layouts.app')

@section('title', __('messages.admin.categories.title'))

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">{{ __('messages.admin.categories.title') }}</h2>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> {{ __('messages.admin.categories.new') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>{{ __('messages.admin.categories.name') }}</th>
                                <th>{{ __('messages.admin.categories.slug') }}</th>
                                <th>{{ __('messages.admin.categories.products_count') }}</th>
                                <th class="text-end">{{ __('messages.admin.categories.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td class="text-muted">{{ $category->slug }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.delete', $category) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin.categories.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($categories->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    {{ __('messages.admin.categories.no_categories') }}
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
