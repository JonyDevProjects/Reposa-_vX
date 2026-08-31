@extends('layouts.app')

@section('title', __('messages.auth.forgot_title'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center py-5">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <h2 class="fw-bold mb-0">{{ __('messages.auth.forgot_title') }}</h2>
                    <p class="opacity-75 small mt-2">{{ __('messages.auth.forgot_subtitle') }}</p>
                </div>
                <div class="card-body p-5">
                    @if (session('status'))
                        <div class="alert alert-success border-0 rounded-3 small">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 small">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="form-label small fw-bold text-muted">{{ __('messages.auth.email') }}</label>
                            <input type="email" class="form-control form-control-lg border-light bg-light rounded-3" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3">
                            {{ __('messages.auth.send_reset_link') }}
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="text-muted small text-decoration-none">{{ __('messages.auth.back_to_login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
