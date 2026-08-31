@extends('layouts.app')

@section('title', __('messages.admin.products.title'))

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">{{ __('messages.admin.products.title') }}</h2>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> {{ __('messages.admin.products.new') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>{{ __('messages.admin.products.name') }}</th>
                                <th>{{ __('messages.admin.products.price') }}</th>
                                <th>{{ __('messages.admin.products.stock') }}</th>
                                <th class="text-end">{{ __('messages.admin.products.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/50' }}" alt="{{ $product->name }}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td class="fw-bold">{{ $product->name }}</td>
                                <td>{{ number_format($product->price, 2) }}€</td>
                                <td>
                                    <span class="badge {{ $product->stock > 10 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->stock }} uds
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.products.delete', $product) }}" method="POST" onsubmit="return confirm('{{ __('messages.admin.products.confirm_delete') }}')">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
