<?php

namespace App\Livewire;

use App\Mail\ConstanciaReclamacion;
use App\Models\Reclamacion;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReclamoPublico extends Component
{
    use WithFileUploads;

    // 1. Consumidor
    public $nombres_apellidos = '';
    public $tipo_documento = 'DNI';
    public $numero_documento = '';
    public $domicilio = '';
    public $telefono = '';
    public $correo = '';
    public $es_menor_edad = false;
    public $apoderado_nombre = '';

    // 2. Bien contratado
    public $tipo_bien = 'producto';
    public $monto_reclamado = '';
    public $descripcion_bien = '';

    // 3. Detalle
    public $tipo_registro = 'reclamo';
    public $detalle = '';
    public $pedido = '';
    public $evidencia;

    // Declaraciones
    public $acepta_politicas = false;
    public $declaracion_veracidad = false;

    // Estado de la vista
    public $numeroGenerado = null;
    public $fechaLimite = null;

    protected function rules(): array
    {
        return [
            'nombres_apellidos'     => 'required|string|min:3|max:200',
            'tipo_documento'        => 'required|in:DNI,CE,Pasaporte',
            'numero_documento'      => 'required|alpha_num|min:8|max:12',
            'domicilio'             => 'required|string|max:300',
            'telefono'              => 'nullable|digits:9',
            'correo'                => 'required|email|max:150',
            'es_menor_edad'         => 'boolean',
            'apoderado_nombre'      => 'required_if_accepted:es_menor_edad|nullable|string|max:200',
            'tipo_bien'             => 'required|in:producto,servicio',
            'monto_reclamado'       => 'nullable|numeric|min:0|max:99999999',
            'descripcion_bien'      => 'required|string|max:500',
            'tipo_registro'         => 'required|in:reclamo,queja',
            'detalle'               => 'required|string|min:10|max:5000',
            'pedido'                => 'required|string|max:2000',
            'evidencia'             => 'nullable|file|mimes:pdf|max:5120',
            'acepta_politicas'      => 'accepted',
            'declaracion_veracidad' => 'accepted',
        ];
    }

    protected function messages(): array
    {
        return [
            'apoderado_nombre.required_if_accepted' => 'Indique el nombre del padre, madre o apoderado.',
            'acepta_politicas.accepted'             => 'Debe aceptar la política de privacidad.',
            'declaracion_veracidad.accepted'        => 'Debe confirmar la veracidad de la información.',
        ];
    }


    public function updated($propiedad): void
    {
        $this->validateOnly($propiedad);
    }

    public function guardar(): void
    {
        $datos = $this->validate();

        $reclamacion = Reclamacion::registrar([
            'nombres_apellidos'     => $datos['nombres_apellidos'],
            'tipo_documento'        => $datos['tipo_documento'],
            'numero_documento'      => $datos['numero_documento'],
            'domicilio'             => $datos['domicilio'],
            'telefono'              => $datos['telefono'] ?: null,
            'correo'                => $datos['correo'],
            'es_menor_edad'         => (bool) $datos['es_menor_edad'],
            'apoderado_nombre'      => $datos['es_menor_edad'] ? $datos['apoderado_nombre'] : null,
            'tipo_bien'             => $datos['tipo_bien'],
            'monto_reclamado'       => $datos['monto_reclamado'] !== '' ? $datos['monto_reclamado'] : null,
            'descripcion_bien'      => $datos['descripcion_bien'],
            'tipo_registro'         => $datos['tipo_registro'],
            'detalle'               => $datos['detalle'],
            'pedido'                => $datos['pedido'],
            'evidencia_path'        => $this->evidencia?->store('evidencias'),
            'acepta_politicas'      => true,
            'declaracion_veracidad' => true,
        ]);

        // Copia de la hoja de reclamación al consumidor
        Mail::to($reclamacion->correo)->send(new ConstanciaReclamacion($reclamacion));

        $this->numeroGenerado = $reclamacion->numero;
        $this->fechaLimite = $reclamacion->fecha_limite->format('d/m/Y');

        $this->reset([
            'nombres_apellidos', 'numero_documento', 'domicilio', 'telefono', 'correo',
            'es_menor_edad', 'apoderado_nombre', 'monto_reclamado', 'descripcion_bien',
            'detalle', 'pedido', 'evidencia', 'acepta_politicas', 'declaracion_veracidad',
        ]);
    }

    public function nuevo(): void
    {
        $this->numeroGenerado = null;
        $this->fechaLimite = null;
    }

    public function render()
    {
        return view('livewire.reclamo-publico');
    }
}
