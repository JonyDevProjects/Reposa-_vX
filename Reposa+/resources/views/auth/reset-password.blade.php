@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center py-5">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <h2 class="fw-bold mb-0">Nueva Contraseña</h2>
                    <p class="opacity-75 small mt-2">Crea una nueva contraseña segura</p>
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

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ request()->route('token') }}">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico</label>
                            <input type="email" class="form-control form-control-lg border-light bg-light rounded-3" id="email" name="email" value="{{ old('email', request()->email) }}" required readonly>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                            <input type="password" class="form-control form-control-lg border-light bg-light rounded-3" id="password" name="password" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label small fw-bold text-muted">Confirmar Contraseña</label>
                            <input type="password" class="form-control form-control-lg border-light bg-light rounded-3" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3">
                            Restablecer Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
