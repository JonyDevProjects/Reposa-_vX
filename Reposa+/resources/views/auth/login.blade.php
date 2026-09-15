@extends('layouts.app')

@section('title', __('messages.auth.login_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center py-5">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <h2 class="fw-bold mb-0">{{ __('messages.auth.welcome_back') }}</h2>
                    <p class="opacity-75 small mt-2">{{ __('messages.auth.login_subtitle') }}</p>
                </div>
                <div class="card-body p-5">
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 small mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 small mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Botón Continuar con Google --}}
                    <div class="mb-4">
                        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark btn-lg w-100 py-3 fw-semibold rounded-3 d-flex align-items-center justify-content-center gap-2 border border-light-subtle shadow-2xs bg-white text-dark">
                            <svg width="20" height="20" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z"/>
                                <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                                <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.173 0 7.547 0 9s.348 2.827.957 4.039l3.007-2.332z"/>
                                <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z"/>
                            </svg>
                            <span>{{ __('messages.auth.continue_with_google') }}</span>
                        </a>
                    </div>

                    {{-- Separador Visual --}}
                    <div class="position-relative text-center my-4">
                        <hr class="text-muted opacity-25">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            {{ __('messages.auth.or_divider') }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-muted">{{ __('messages.auth.email') }}</label>
                            <input type="email" class="form-control form-control-lg border-light bg-light rounded-3" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label small fw-bold text-muted">{{ __('messages.auth.password') }}</label>
                                <a href="{{ route('password.request') }}" class="small text-decoration-none">{{ __('messages.auth.forgot_password') }}</a>
                            </div>
                            <input type="password" class="form-control form-control-lg border-light bg-light rounded-3" id="password" name="password" required>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label small text-muted" for="remember">{{ __('messages.auth.remember_me') }}</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3">
                            {{ __('messages.auth.login_btn') }}
                        </button>
                    </form>

                    <div class="text-center mt-5">
                        <p class="text-muted small">{{ __('messages.auth.no_account') }} <a href="{{ route('register') }}" class="fw-bold text-decoration-none">{{ __('messages.auth.register_now') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
