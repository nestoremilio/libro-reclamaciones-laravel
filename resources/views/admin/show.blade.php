@extends('layouts.admin')

@section('title', 'Hoja '.$reclamacion->numero)

@section('content')
    @php($r = $reclamacion)

    <a href="{{ route('admin.dashboard') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Volver a la bandeja</a>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Hoja N° <span class="font-monospace">{{ $r->numero }}</span></h1>
            <div class="d-flex flex-wrap gap-2 align-items-center small text-muted">
                <span class="badge rounded-pill text-bg-light text-capitalize border">{{ $r->tipo_registro }}</span>
                @if ($r->estaVencida())
                    <span class="badge rounded-pill text-bg-danger">Vencida</span>
                @elseif ($r->estaPendiente())
                    <span class="badge rounded-pill text-bg-warning">Pendiente</span>
                @else
                    <span class="badge rounded-pill text-bg-success">Atendida</span>
                @endif
                <span>Registrada el {{ $r->created_at->format('d/m/Y H:i') }}</span>
                <span>· Responder hasta el <strong class="{{ $r->estaVencida() ? 'text-danger' : '' }}">{{ $r->fecha_limite->format('d/m/Y') }}</strong></span>
            </div>
        </div>
        <a href="{{ route('admin.reporte', $r) }}" class="btn btn-outline-brand rounded-pill"><i class="bi bi-file-earmark-pdf me-1"></i>Descargar PDF</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card p-4 mb-4">
                <h2 class="h6 fw-bold text-brand mb-3">1. Consumidor</h2>
                <dl class="row small mb-0">
                    <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">{{ $r->nombres_apellidos }}</dd>
                    <dt class="col-sm-4">Documento</dt><dd class="col-sm-8">{{ $r->tipo_documento }} {{ $r->numero_documento }}</dd>
                    <dt class="col-sm-4">Domicilio</dt><dd class="col-sm-8">{{ $r->domicilio }}</dd>
                    <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">{{ $r->telefono ?: '—' }}</dd>
                    <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">{{ $r->correo }}</dd>
                    @if ($r->es_menor_edad)
                        <dt class="col-sm-4">Apoderado</dt><dd class="col-sm-8">{{ $r->apoderado_nombre }}</dd>
                    @endif
                </dl>
            </div>

            <div class="card p-4 mb-4">
                <h2 class="h6 fw-bold text-brand mb-3">2. Bien contratado</h2>
                <dl class="row small mb-0">
                    <dt class="col-sm-4">Tipo</dt><dd class="col-sm-8 text-capitalize">{{ $r->tipo_bien }}</dd>
                    <dt class="col-sm-4">Descripción</dt><dd class="col-sm-8">{{ $r->descripcion_bien }}</dd>
                    <dt class="col-sm-4">Monto reclamado</dt><dd class="col-sm-8">{{ $r->monto_reclamado ? 'S/ '.number_format($r->monto_reclamado, 2) : '—' }}</dd>
                </dl>
            </div>

            <div class="card p-4">
                <h2 class="h6 fw-bold text-brand mb-3">3. Detalle y pedido</h2>
                <p class="small fw-semibold mb-1">Detalle</p>
                <p class="small" style="white-space: pre-line">{{ $r->detalle }}</p>
                <p class="small fw-semibold mb-1">Pedido</p>
                <p class="small mb-0" style="white-space: pre-line">{{ $r->pedido }}</p>
                @if ($r->evidencia_path)
                    <a href="{{ route('admin.evidencia', $r) }}" target="_blank" class="btn btn-sm btn-light border mt-3 align-self-start">
                        <i class="bi bi-paperclip me-1"></i>Ver evidencia adjunta
                    </a>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-4">
                <h2 class="h6 fw-bold text-brand mb-3">4. Observaciones y acciones del proveedor</h2>

                @if ($r->estaPendiente())
                    <form action="{{ route('admin.responder', $r) }}" method="POST">
                        @csrf
                        <textarea name="respuesta" rows="7" class="form-control @error('respuesta') is-invalid @enderror" placeholder="Describa la respuesta y las acciones adoptadas" required>{{ old('respuesta') }}</textarea>
                        @error('respuesta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text mb-3">Se enviará al correo {{ $r->correo }} y la hoja quedará como atendida.</div>
                        <button class="btn btn-brand w-100 rounded-pill"><i class="bi bi-send me-1"></i>Registrar y enviar respuesta</button>
                    </form>
                @else
                    <p class="small" style="white-space: pre-line">{{ $r->respuesta }}</p>
                    <p class="small text-muted mb-0">
                        Respondida el {{ $r->respondido_at?->format('d/m/Y H:i') }}
                        @if ($r->respondidoPor) por {{ $r->respondidoPor->name }} @endif
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
