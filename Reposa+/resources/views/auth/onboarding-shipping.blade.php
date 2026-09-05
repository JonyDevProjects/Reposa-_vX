@extends('layouts.app')

@section('title', __('messages.auth.onboarding_title'))

@section('content')
<div class="container py-4 py-lg-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                {{-- Cabecera Midnight Sanctuary --}}
                <div class="bg-primary p-4 p-md-5 text-center text-white">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-geo-alt-fill fs-3 text-white"></i>
                    </div>
                    <h1 class="h3 fw-bold mb-2">{{ __('messages.auth.onboarding_title') }}</h1>
                    <p class="opacity-75 small mb-0">{{ __('messages.auth.onboarding_subtitle') }}</p>

                    {{-- Badge de usuario verificado con Google --}}
                    <div class="d-inline-flex align-items-center gap-2 mt-3 px-3 py-1 rounded-pill bg-white bg-opacity-10 small">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle border border-white" width="22" height="22">
                        @else
                            <i class="bi bi-google"></i>
                        @endif
                        <span class="text-white">{{ $user->name }}</span>
                        <span class="opacity-50">|</span>
                        <span class="opacity-75">{{ $user->email }}</span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if(session('info'))
                        <div class="alert alert-info border-0 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning border-0 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            <span>{{ session('warning') }}</span>
                        </div>
                    @endif

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

                    <div class="mb-4">
                        <p class="text-muted small">
                            {{ __('messages.auth.onboarding_description') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('onboarding.shipping.store') }}" novalidate>
                        @csrf

                        <div class="p-3 p-md-4 rounded-3 bg-light border border-light-subtle mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h2 class="h6 fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-truck text-primary"></i>
                                    <span>{{ __('messages.auth.section_shipping') }}</span>
                                </h2>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small fw-semibold">
                                    Paso 2 / 2
                                </span>
                            </div>

                            <div class="mb-3">
                                <label for="street" class="form-label small fw-semibold text-muted">
                                    {{ __('messages.auth.street') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control border-light-subtle rounded-3 @error('street') is-invalid @enderror" 
                                       id="street" name="street" value="{{ old('street') }}" required autofocus
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

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>{{ __('messages.auth.onboarding_save_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
