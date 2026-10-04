@extends('layouts.admin')

@section('title', 'Bandeja de reclamaciones')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Bandeja de reclamaciones</h1>
            <p class="text-muted mb-0 small">Plazo legal de respuesta: {{ config('empresa.plazo_dias_habiles') }} días hábiles</p>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="row g-3 mb-4">
        @foreach ([
            ['Total', $resumen['total'], 'bi-journal-text', 'text-brand', null],
            ['Pendientes', $resumen['pendientes'], 'bi-hourglass-split', 'text-warning', 'pendiente'],
            ['Vencidas', $resumen['vencidas'], 'bi-exclamation-triangle', 'text-danger', 'vencidas'],
            ['Atendidas', $resumen['atendidas'], 'bi-check-circle', 'text-success', 'atendido'],
        ] as [$etiqueta, $valor, $icono, $color, $filtro])
            <div class="col-6 col-lg-3">
                <a href="{{ route('admin.dashboard', array_filter(['estado' => $filtro])) }}" class="card p-3 h-100 text-decoration-none text-body {{ $estado === $filtro ? 'border border-2' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">{{ $etiqueta }}</div>
                            <div class="fs-3 fw-bold">{{ $valor }}</div>
                        </div>
                        <i class="bi {{ $icono }} fs-2 {{ $color }}"></i>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Búsqueda --}}
    <form method="GET" class="card p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-7">
                <input type="search" name="buscar" value="{{ $buscar }}" class="form-control" placeholder="Buscar por N° de hoja, nombre o documento">
            </div>
            <div class="col-8 col-md-3">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" @selected($estado === 'pendiente')>Pendientes</option>
                    <option value="vencidas" @selected($estado === 'vencidas')>Vencidas</option>
                    <option value="atendido" @selected($estado === 'atendido')>Atendidas</option>
                </select>
            </div>
            <div class="col-4 col-md-2 d-grid">
                <button class="btn btn-brand"><i class="bi bi-search me-1"></i>Filtrar</button>
            </div>
        </div>
    </form>

    {{-- Listado --}}
    <div class="card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-3">N° hoja</th>
                        <th>Consumidor</th>
                        <th class="d-none d-md-table-cell">Tipo</th>
                        <th class="d-none d-lg-table-cell">Registro</th>
                        <th>Plazo</th>
                        <th>Estado</th>
                        <th class="text-end pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reclamaciones as $r)
                        <tr>
                            <td class="ps-3 font-monospace fw-semibold">{{ $r->numero }}</td>
                            <td>
                                <div class="fw-semibold">{{ $r->nombres_apellidos }}</div>
                                <div class="small text-muted">{{ $r->tipo_documento }} {{ $r->numero_documento }}</div>
                            </td>
                            <td class="d-none d-md-table-cell text-capitalize">{{ $r->tipo_registro }}</td>
                            <td class="d-none d-lg-table-cell small">{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="small {{ $r->estaVencida() ? 'text-danger fw-semibold' : '' }}">{{ $r->fecha_limite->format('d/m/Y') }}</td>
                            <td>
                                @if ($r->estaVencida())
                                    <span class="badge rounded-pill text-bg-danger">Vencida</span>
                                @elseif ($r->estaPendiente())
                                    <span class="badge rounded-pill text-bg-warning">Pendiente</span>
                                @else
                                    <span class="badge rounded-pill text-bg-success">Atendida</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.show', $r) }}" class="btn btn-sm btn-outline-brand rounded-pill">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No hay hojas de reclamación con estos filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $reclamaciones->links() }}
    </div>
@endsection
