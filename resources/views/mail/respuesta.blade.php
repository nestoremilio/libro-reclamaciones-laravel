<x-mail::message>
# Respuesta a su hoja de reclamación

Estimado(a) **{{ $reclamacion->nombres_apellidos }}**:

En relación con su hoja de reclamación **N° {{ $reclamacion->numero }}**, le informamos lo siguiente:

<x-mail::panel>
{{ $reclamacion->respuesta }}
</x-mail::panel>

Fecha de respuesta: {{ $reclamacion->respondido_at->format('d/m/Y') }}

{{ config('empresa.razon_social') }} — RUC {{ config('empresa.ruc') }}<br>
{{ config('empresa.direccion') }}
</x-mail::message>
