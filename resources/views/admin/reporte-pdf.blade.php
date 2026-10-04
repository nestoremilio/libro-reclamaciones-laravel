@php($r = $reclamacion)
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de reclamación {{ $r->numero }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1f2937; }
        .cabecera { border-bottom: 3px solid {{ config('empresa.color') }}; padding-bottom: 8px; margin-bottom: 12px; }
        .cabecera td { vertical-align: top; }
        .titulo { font-size: 16px; font-weight: bold; color: {{ config('empresa.color') }}; }
        .numero { border: 2px solid {{ config('empresa.color') }}; padding: 6px 10px; text-align: center; }
        .numero .n { font-size: 15px; font-weight: bold; }
        h2 { font-size: 11px; background: {{ config('empresa.color') }}; color: #fff; padding: 5px 8px; margin: 14px 0 0; }
        table.datos { width: 100%; border-collapse: collapse; }
        table.datos td { border: 1px solid #d1d5db; padding: 5px 7px; vertical-align: top; }
        table.datos td.l { width: 28%; background: #f3f4f6; font-weight: bold; }
        .texto { white-space: pre-line; }
        .estado { margin-top: 6px; font-weight: bold; }
        .pie { position: fixed; bottom: -10px; left: 0; right: 0; font-size: 8.5px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <table width="100%" class="cabecera">
        <tr>
            <td>
                <div class="titulo">HOJA DE RECLAMACIÓN</div>
                <div><strong>{{ config('empresa.razon_social') }}</strong> — RUC {{ config('empresa.ruc') }}</div>
                <div>{{ config('empresa.direccion') }}</div>
            </td>
            <td width="34%">
                <div class="numero">
                    <div>N°</div>
                    <div class="n">{{ $r->numero }}</div>
                    <div>{{ $r->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="estado">
        Estado: {{ $r->estaPendiente() ? 'PENDIENTE' : 'ATENDIDA' }} ·
        Fecha máxima de respuesta: {{ $r->fecha_limite->format('d/m/Y') }}
    </div>

    <h2>1. IDENTIFICACIÓN DEL CONSUMIDOR</h2>
    <table class="datos">
        <tr><td class="l">Nombres y apellidos</td><td>{{ $r->nombres_apellidos }}</td></tr>
        <tr><td class="l">Documento</td><td>{{ $r->tipo_documento }} {{ $r->numero_documento }}</td></tr>
        <tr><td class="l">Domicilio</td><td>{{ $r->domicilio }}</td></tr>
        <tr><td class="l">Teléfono / Correo</td><td>{{ $r->telefono ?: '—' }} / {{ $r->correo }}</td></tr>
        @if ($r->es_menor_edad)
            <tr><td class="l">Padre, madre o apoderado</td><td>{{ $r->apoderado_nombre }}</td></tr>
        @endif
    </table>

    <h2>2. IDENTIFICACIÓN DEL BIEN CONTRATADO</h2>
    <table class="datos">
        <tr><td class="l">Tipo</td><td>{{ ucfirst($r->tipo_bien) }}</td></tr>
        <tr><td class="l">Monto reclamado</td><td>{{ $r->monto_reclamado ? 'S/ '.number_format($r->monto_reclamado, 2) : '—' }}</td></tr>
        <tr><td class="l">Descripción</td><td>{{ $r->descripcion_bien }}</td></tr>
    </table>

    <h2>3. DETALLE DE LA RECLAMACIÓN Y PEDIDO DEL CONSUMIDOR</h2>
    <table class="datos">
        <tr><td class="l">Tipo</td><td>{{ ucfirst($r->tipo_registro) }}</td></tr>
        <tr><td class="l">Detalle</td><td class="texto">{{ $r->detalle }}</td></tr>
        <tr><td class="l">Pedido</td><td class="texto">{{ $r->pedido }}</td></tr>
        <tr><td class="l">Evidencia</td><td>{{ $r->evidencia_path ? 'Adjunta al final del documento' : 'No adjuntó' }}</td></tr>
    </table>

    <h2>4. OBSERVACIONES Y ACCIONES ADOPTADAS POR EL PROVEEDOR</h2>
    <table class="datos">
        <tr><td class="l">Respuesta</td><td class="texto">{{ $r->respuesta ?: 'Pendiente de respuesta.' }}</td></tr>
        <tr><td class="l">Fecha de respuesta</td><td>{{ $r->respondido_at?->format('d/m/Y H:i') ?? '—' }}</td></tr>
    </table>

    <p style="margin-top:14px; font-size:9px; color:#4b5563">
        * La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para
        interponer una denuncia ante el INDECOPI. * El proveedor deberá dar respuesta al reclamo en un plazo no mayor a
        {{ config('empresa.plazo_dias_habiles') }} días hábiles.
    </p>

    <div class="pie">{{ config('empresa.razon_social') }} · Libro de Reclamaciones Virtual · Generado el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
