<div>
    @if ($numeroGenerado)
        {{-- Confirmación --}}
        <div class="text-center py-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-3" style="width:72px;height:72px">
                <i class="bi bi-check-lg fs-1"></i>
            </div>
            <h2 class="fw-bold h3">Hoja de reclamación registrada</h2>
            <p class="text-muted">Enviamos una copia a su correo electrónico.</p>

            <div class="bg-brand-soft rounded-3 d-inline-block px-4 px-sm-5 py-3 my-2">
                <small class="text-uppercase text-muted fw-semibold">N° de hoja de reclamación</small>
                <div class="fs-2 fw-bold text-brand font-monospace">{{ $numeroGenerado }}</div>
                <small class="text-muted">Respuesta como máximo el <strong>{{ $fechaLimite }}</strong></small>
            </div>

            <div class="mt-4">
                <button type="button" wire:click="nuevo" class="btn btn-outline-brand rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Registrar otra hoja
                </button>
            </div>
        </div>
    @else
        <form wire:submit="guardar" novalidate>
            <div class="alert bg-brand-soft border-0 small mb-4">
                <i class="bi bi-info-circle text-brand me-1"></i>
                Responderemos en un plazo máximo de <strong>{{ config('empresa.plazo_dias_habiles') }} días hábiles</strong>.
                Fecha de registro: <strong>{{ now()->format('d/m/Y') }}</strong>.
            </div>

            {{-- 1. Consumidor --}}
            <h2 class="h6 fw-bold d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                <span class="paso">1</span> Identificación del consumidor
            </h2>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label for="nombres" class="form-label small fw-semibold">Nombres y apellidos *</label>
                    <input id="nombres" type="text" wire:model.blur="nombres_apellidos" class="form-control @error('nombres_apellidos') is-invalid @enderror" autocomplete="name">
                    @error('nombres_apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-5 col-md-4">
                    <label for="tipo_doc" class="form-label small fw-semibold">Documento *</label>
                    <select id="tipo_doc" wire:model="tipo_documento" class="form-select">
                        <option value="DNI">DNI</option>
                        <option value="CE">Carné de extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div class="col-7 col-md-8">
                    <label for="num_doc" class="form-label small fw-semibold">Número *</label>
                    <input id="num_doc" type="text" inputmode="numeric" maxlength="12" wire:model.blur="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror">
                    @error('numero_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="domicilio" class="form-label small fw-semibold">Domicilio *</label>
                    <input id="domicilio" type="text" wire:model.blur="domicilio" class="form-control @error('domicilio') is-invalid @enderror" autocomplete="street-address">
                    @error('domicilio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-5">
                    <label for="telefono" class="form-label small fw-semibold">Teléfono</label>
                    <input id="telefono" type="tel" maxlength="9" wire:model.blur="telefono" class="form-control @error('telefono') is-invalid @enderror" placeholder="999888777">
                    @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-7">
                    <label for="correo" class="form-label small fw-semibold">Correo electrónico *</label>
                    <input id="correo" type="email" wire:model.blur="correo" class="form-control @error('correo') is-invalid @enderror" autocomplete="email">
                    @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input id="menor" type="checkbox" wire:model.live="es_menor_edad" class="form-check-input">
                        <label for="menor" class="form-check-label small">El consumidor es menor de edad</label>
                    </div>
                </div>
                @if ($es_menor_edad)
                    <div class="col-12">
                        <label for="apoderado" class="form-label small fw-semibold">Padre, madre o apoderado *</label>
                        <input id="apoderado" type="text" wire:model.blur="apoderado_nombre" class="form-control @error('apoderado_nombre') is-invalid @enderror">
                        @error('apoderado_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                @endif
            </div>

            {{-- 2. Bien contratado --}}
            <h2 class="h6 fw-bold d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                <span class="paso">2</span> Identificación del bien contratado
            </h2>
            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold d-block">Tipo *</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" id="producto" value="producto" wire:model.live="tipo_bien">
                        <label class="btn btn-outline-brand" for="producto"><i class="bi bi-box-seam me-1"></i>Producto</label>
                        <input type="radio" class="btn-check" id="servicio" value="servicio" wire:model.live="tipo_bien">
                        <label class="btn btn-outline-brand" for="servicio"><i class="bi bi-tools me-1"></i>Servicio</label>
                    </div>
                </div>
                <div class="col-md-7">
                    <label for="monto" class="form-label small fw-semibold">Monto reclamado (S/)</label>
                    <input id="monto" type="number" step="0.01" min="0" wire:model.blur="monto_reclamado" class="form-control @error('monto_reclamado') is-invalid @enderror" placeholder="0.00">
                    @error('monto_reclamado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="desc_bien" class="form-label small fw-semibold">Descripción *</label>
                    <input id="desc_bien" type="text" wire:model.blur="descripcion_bien" class="form-control @error('descripcion_bien') is-invalid @enderror" placeholder="Ej.: Laptop modelo X14, servicio de instalación, etc.">
                    @error('descripcion_bien') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 3. Detalle --}}
            <h2 class="h6 fw-bold d-flex align-items-center gap-2 border-bottom pb-2 mb-3">
                <span class="paso">3</span> Detalle de la reclamación y pedido
            </h2>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="opcion card h-100 p-3 text-center {{ $tipo_registro === 'reclamo' ? 'activa' : '' }}">
                        <input type="radio" class="d-none" value="reclamo" wire:model.live="tipo_registro">
                        <i class="bi bi-exclamation-circle fs-3 text-brand"></i>
                        <span class="fw-bold">Reclamo</span>
                        <small class="text-muted">Disconformidad con el producto o servicio</small>
                    </label>
                </div>
                <div class="col-6">
                    <label class="opcion card h-100 p-3 text-center {{ $tipo_registro === 'queja' ? 'activa' : '' }}">
                        <input type="radio" class="d-none" value="queja" wire:model.live="tipo_registro">
                        <i class="bi bi-chat-left-dots fs-3 text-brand"></i>
                        <span class="fw-bold">Queja</span>
                        <small class="text-muted">Malestar con la atención recibida</small>
                    </label>
                </div>
                <div class="col-12">
                    <label for="detalle" class="form-label small fw-semibold">Detalle *</label>
                    <textarea id="detalle" rows="4" wire:model.blur="detalle" class="form-control @error('detalle') is-invalid @enderror" placeholder="Describa lo sucedido"></textarea>
                    @error('detalle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="pedido" class="form-label small fw-semibold">Pedido *</label>
                    <textarea id="pedido" rows="2" wire:model.blur="pedido" class="form-control @error('pedido') is-invalid @enderror" placeholder="¿Qué solución espera?"></textarea>
                    @error('pedido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="evidencia" class="form-label small fw-semibold">Evidencia en PDF (opcional, máx. 5 MB)</label>
                    <input id="evidencia" type="file" accept="application/pdf" wire:model="evidencia" class="form-control @error('evidencia') is-invalid @enderror">
                    <div wire:loading wire:target="evidencia" class="small text-brand mt-1">
                        <span class="spinner-border spinner-border-sm"></span> Subiendo archivo...
                    </div>
                    @error('evidencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Declaraciones --}}
            <div class="mb-4">
                <div class="form-check mb-2">
                    <input id="politicas" type="checkbox" wire:model="acepta_politicas" class="form-check-input @error('acepta_politicas') is-invalid @enderror">
                    <label for="politicas" class="form-check-label small">Acepto la política de privacidad y el tratamiento de mis datos personales (Ley N° 29733). *</label>
                    @error('acepta_politicas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-check">
                    <input id="veracidad" type="checkbox" wire:model="declaracion_veracidad" class="form-check-input @error('declaracion_veracidad') is-invalid @enderror">
                    <label for="veracidad" class="form-check-label small">Declaro que la información proporcionada es verdadera. *</label>
                    @error('declaracion_veracidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-brand btn-lg w-100 rounded-pill py-3" wire:loading.attr="disabled" wire:target="guardar">
                <span wire:loading.remove wire:target="guardar">Enviar hoja de reclamación <i class="bi bi-send ms-1"></i></span>
                <span wire:loading wire:target="guardar"><span class="spinner-border spinner-border-sm me-1"></span> Enviando...</span>
            </button>

            <p class="small text-muted mt-3 mb-0">
                La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo
                para interponer una denuncia ante el INDECOPI. El proveedor deberá dar respuesta en un plazo no mayor a
                {{ config('empresa.plazo_dias_habiles') }} días hábiles.
            </p>
        </form>
    @endif
</div>
