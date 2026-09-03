@extends('layouts.app')

@section('title', __('messages.admin.categories.create_title'))

@section('content')
<div class="container py-4">
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">{{ __('messages.admin.categories.create_subtitle') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('messages.admin.categories.name_label') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.categories') }}" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">{{ __('messages.admin.categories.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
