@extends('layouts.base')

@section('title', 'Libro de Reclamaciones')

@push('styles')
<style>
    .hero { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: #fff; padding: 2.5rem 0 5.5rem; }
    .hero .icono { width: 64px; height: 64px; border-radius: 1rem; background: rgba(255,255,255,.15); display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; }
    .form-card { margin-top: -4rem; }
    .paso { width: 30px; height: 30px; border-radius: 50%; background: var(--brand); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 700; flex-shrink: 0; }
    .opcion { cursor: pointer; border: 2px solid #e9ecef; transition: all .15s; }
    .opcion.activa { border-color: var(--brand); background: var(--brand-soft); }
    @media (max-width: 576px) { .hero { padding: 1.75rem 0 4.5rem; } .hero h1 { font-size: 1.5rem; } }
</style>
@endpush

@section('body')
    <header class="hero text-center">
        <div class="container">
            <div class="icono mb-3"><i class="bi bi-journal-text"></i></div>
            <h1 class="fw-bold h2 mb-1">Libro de Reclamaciones Virtual</h1>
            <p class="mb-0 opacity-75">{{ config('empresa.razon_social') }} · RUC {{ config('empresa.ruc') }}</p>
            <p class="mb-0 opacity-75 small">{{ config('empresa.direccion') }}</p>
        </div>
    </header>

    <main class="container pb-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card form-card p-3 p-sm-4 p-md-5">
                    <livewire:reclamo-publico />
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 small text-muted">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span>Conforme al Código de Protección y Defensa del Consumidor (Ley N° 29571).</span>
            <a href="{{ route('login') }}" class="text-muted text-decoration-none"><i class="bi bi-shield-lock me-1"></i>Acceso administrativo</a>
        </div>
    </footer>
@endsection
