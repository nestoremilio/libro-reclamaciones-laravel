@extends('layouts.base')

@section('body')
    <nav class="navbar navbar-dark bg-brand sticky-top">
        <div class="container-xl">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-journal-text"></i>
                <span class="d-none d-sm-inline">Libro de Reclamaciones</span>
                <span class="d-sm-none">Reclamaciones</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-md-inline">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-sm btn-light rounded-pill px-3"><i class="bi bi-box-arrow-right me-1"></i>Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container-xl py-4 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
