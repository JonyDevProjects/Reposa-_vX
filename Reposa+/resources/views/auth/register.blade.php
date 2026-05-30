@extends('layouts.app')

@section('title', __('messages.auth.register_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center py-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <h2 class="fw-bold mb-0">{{ __('messages.auth.create_account') }}</h2>
                    <p class="opacity-75 small mt-2">{{ __('messages.auth.register_subtitle') }}</p>
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

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label for="name" class="form-label small fw-bold text-muted">{{ __('messages.auth.full_name') }}</label>
                                <input type="text" class="form-control form-control-lg border-light bg-light rounded-3" id="name" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-muted">{{ __('messages.auth.email') }}</label>
                            <input type="email" class="form-control form-control-lg border-light bg-light rounded-3" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="password" class="form-label small fw-bold text-muted">{{ __('messages.auth.password') }}</label>
                                <input type="password" class="form-control form-control-lg border-light bg-light rounded-3" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="password_confirmation" class="form-label small fw-bold text-muted">{{ __('messages.auth.confirm_password') }}</label>
                                <input type="password" class="form-control form-control-lg border-light bg-light rounded-3" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label small text-muted" for="terms">{{ __('messages.auth.accept_terms') }}</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3">
                            {{ __('messages.auth.register_btn') }}
                        </button>
                    </form>

                    <div class="text-center mt-5">
                        <p class="text-muted small">{{ __('messages.auth.has_account') }} <a href="{{ route('login') }}" class="fw-bold text-decoration-none">{{ __('messages.auth.login_link') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
