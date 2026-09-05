@extends('layouts.app')

@section('title', __('messages.auth.register_title'))

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-4 p-md-5 text-center text-white">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-person-plus fs-3"></i>
                    </div>
                    <h1 class="h3 fw-bold mb-1">{{ __('messages.auth.create_account') }}</h1>
                    <p class="opacity-75 small mb-0">{{ __('messages.auth.register_subtitle') }}</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 small mb-4" role="alert">
                            <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>{{ __('messages.cart.error') }}</span>
                            </div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        {{-- Bloque 1: Credenciales de Acceso --}}
                        <div class="p-3 p-md-4 rounded-3 bg-light border border-light-subtle mb-4">
                            <h2 class="h6 fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-person-badge text-primary"></i>
                                <span>{{ __('messages.auth.section_credentials') }}</span>
                            </h2>

                            <div class="mb-3">
                                <label for="name" class="form-label small fw-semibold text-muted">
                                    {{ __('messages.auth.full_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control border-light-subtle rounded-3 @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required autofocus
                                       placeholder="Ej. María García López">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold text-muted">
                                    {{ __('messages.auth.email') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control border-light-subtle rounded-3 @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required
                                       placeholder="nombre@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.password') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control border-light-subtle rounded-3 @error('password') is-invalid @enderror" 
                                           id="password" name="password" required
                                           placeholder="••••••••">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.confirm_password') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control border-light-subtle rounded-3" 
                                           id="password_confirmation" name="password_confirmation" required
                                           placeholder="••••••••">
                                </div>
                            </div>
                        </div>

                        {{-- Bloque 2: Dirección de Envío y Contacto --}}
                        <div class="p-3 p-md-4 rounded-3 bg-light border border-light-subtle mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h2 class="h6 fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-truck text-primary"></i>
                                    <span>{{ __('messages.auth.section_shipping') }}</span>
                                </h2>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-semibold">
                                    Obligatorio
                                </span>
                            </div>
                            <p class="text-muted small mb-3">{{ __('messages.auth.shipping_subtitle') }}</p>

                            <div class="mb-3">
                                <label for="street" class="form-label small fw-semibold text-muted">
                                    {{ __('messages.auth.street') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control border-light-subtle rounded-3 @error('street') is-invalid @enderror" 
                                       id="street" name="street" value="{{ old('street') }}" required
                                       placeholder="{{ __('messages.auth.street_placeholder') }}">
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="city" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.city') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control border-light-subtle rounded-3 @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city') }}" required
                                           placeholder="{{ __('messages.auth.city_placeholder') }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="zip_code" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.zip_code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control border-light-subtle rounded-3 @error('zip_code') is-invalid @enderror" 
                                           id="zip_code" name="zip_code" value="{{ old('zip_code') }}" required
                                           placeholder="{{ __('messages.auth.zip_code_placeholder') }}">
                                    @error('zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="province" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.province') }}
                                    </label>
                                    <input type="text" class="form-control border-light-subtle rounded-3 @error('province') is-invalid @enderror" 
                                           id="province" name="province" value="{{ old('province') }}"
                                           placeholder="{{ __('messages.auth.province_placeholder') }}">
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label small fw-semibold text-muted">
                                        {{ __('messages.auth.phone') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control border-light-subtle rounded-3 @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required
                                           placeholder="{{ __('messages.auth.phone_placeholder') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-text small text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i>{{ __('messages.auth.phone_hint') }}
                            </div>
                        </div>

                        {{-- Aceptación de Términos --}}
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="terms">
                                {{ __('messages.auth.accept_terms') }} <span class="text-danger">*</span>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i>{{ __('messages.auth.register_btn') }}
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-2 border-top">
                        <p class="text-muted small mb-0">
                            {{ __('messages.auth.has_account') }} 
                            <a href="{{ route('login') }}" class="fw-bold text-primary text-decoration-none hover-underline">
                                {{ __('messages.auth.login_link') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
