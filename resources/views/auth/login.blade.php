@extends('layouts.base')

@section('title', 'Acceso administrativo')

@section('body')
    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3 bg-brand">
        <div class="card p-4 p-sm-5 w-100" style="max-width: 420px">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 bg-brand-soft text-brand mb-3" style="width:56px;height:56px">
                    <i class="bi bi-shield-lock fs-3"></i>
                </div>
                <h1 class="h4 fw-bold mb-1">Acceso administrativo</h1>
                <p class="text-muted small mb-0">{{ config('empresa.razon_social') }}</p>
            </div>

            @error('email')
                <div class="alert alert-danger small py-2">{{ $message }}</div>
            @enderror

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Correo electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Contraseña</label>
                    <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
                </div>
                <div class="form-check mb-4">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small">Mantener sesión iniciada</label>
                </div>
                <button class="btn btn-brand w-100 rounded-pill py-2">Ingresar</button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('reclamo') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Volver al formulario</a>
            </div>
        </div>
    </main>
@endsection
