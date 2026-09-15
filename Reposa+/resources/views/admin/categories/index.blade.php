@extends('layouts.app')

@section('title', __('messages.admin.categories.title'))

@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">
    <div class="row g-4">
        {{-- Navigation Sidebar --}}
        <div class="col-lg-3 col-xl-2">
            @include('admin.partials.sidebar')
        </div>

        {{-- Main Categories View --}}
        <div class="col-lg-9 col-xl-10">
            {{-- Header & New Category CTA --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h1 class="h4 fw-bold mb-1 text-navy">{{ __('messages.admin.categories.title') }}</h1>
                    <p class="text-muted small mb-0">{{ __('messages.admin.categories.total_count', ['count' => $categories->count()]) }}</p>
                </div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> {{ __('messages.admin.categories.new') }}
                </a>
            </div>

            @if(session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="danger">{{ session('error') }}</x-alert>
            @endif

            {{-- Compact Categories Table --}}
            <div class="card border shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-admin align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>{{ __('messages.admin.categories.name') }}</th>
                                    <th>{{ __('messages.admin.categories.slug') }}</th>
                                    <th class="text-center" style="width: 140px;">{{ __('messages.admin.categories.products_count') }}</th>
                                    <th class="text-end" style="width: 100px;">{{ __('messages.admin.categories.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td class="fw-bold tabular-nums text-primary">#{{ $category->id }}</td>
                                    <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                    <td class="text-muted small font-monospace">{{ $category->slug }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-navy border tabular-nums px-2 py-1">
                                            {{ $category->products_count }} productos
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-sm btn-outline-secondary py-1 px-2"
                                               title="Editar categoría"
                                               aria-label="Editar categoría {{ $category->name }}">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.delete', $category) }}" method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ __('messages.admin.categories.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger py-1 px-2"
                                                        title="Eliminar categoría"
                                                        aria-label="Eliminar categoría {{ $category->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-tags fs-2 text-muted opacity-50 d-block mb-2"></i>
                                        <p class="mb-0 small">{{ __('messages.admin.categories.no_categories') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
