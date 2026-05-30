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
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
