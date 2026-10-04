<x-mail::message>
# Hoja de reclamación registrada

Estimado(a) **{{ $reclamacion->nombres_apellidos }}**:

Hemos recibido su {{ $reclamacion->tipo_registro }}. Esta es la copia de su hoja de reclamación.

<x-mail::panel>
**N° de hoja:** {{ $reclamacion->numero }}
**Fecha de registro:** {{ $reclamacion->created_at->format('d/m/Y H:i') }}
**Fecha máxima de respuesta:** {{ $reclamacion->fecha_limite->format('d/m/Y') }}
</x-mail::panel>

**Bien contratado:** {{ ucfirst($reclamacion->tipo_bien) }} — {{ $reclamacion->descripcion_bien }}
@if($reclamacion->monto_reclamado)
**Monto reclamado:** S/ {{ number_format($reclamacion->monto_reclamado, 2) }}
@endif

**Detalle:**
{{ $reclamacion->detalle }}

**Pedido:**
{{ $reclamacion->pedido }}

Le responderemos en un plazo máximo de {{ config('empresa.plazo_dias_habiles') }} días hábiles.

<small>La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</small>

{{ config('empresa.razon_social') }} — RUC {{ config('empresa.ruc') }}<br>
{{ config('empresa.direccion') }}
</x-mail::message>
