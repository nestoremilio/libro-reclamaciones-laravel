<?php

namespace App\Models;

use App\Support\PlazoHabil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Reclamacion extends Model
{
    use HasFactory;

    public const PENDIENTE = 'pendiente';
    public const ATENDIDO = 'atendido';

    protected $table = 'reclamaciones';

    protected $fillable = [
        'numero',
        'nombres_apellidos', 'tipo_documento', 'numero_documento', 'domicilio', 'telefono', 'correo',
        'es_menor_edad', 'apoderado_nombre',
        'tipo_bien', 'monto_reclamado', 'descripcion_bien',
        'tipo_registro', 'detalle', 'pedido', 'evidencia_path',
        'acepta_politicas', 'declaracion_veracidad',
        'estado', 'fecha_limite', 'respuesta', 'respondido_at', 'respondido_por',
    ];

    protected function casts(): array
    {
        return [
            'es_menor_edad'         => 'boolean',
            'acepta_politicas'      => 'boolean',
            'declaracion_veracidad' => 'boolean',
            'monto_reclamado'       => 'decimal:2',
            'fecha_limite'          => 'date',
            'respondido_at'         => 'datetime',
        ];
    }

    /**
     * Registra una reclamación asignando el número correlativo (AAAA-000001)
     * y la fecha límite de respuesta dentro de una transacción.
     */
    public static function registrar(array $datos): self
    {
        return DB::transaction(function () use ($datos) {
            $anio = now()->format('Y');

            $ultimo = static::where('numero', 'like', $anio.'-%')
                ->lockForUpdate()
                ->orderByDesc('numero')
                ->value('numero');

            $siguiente = $ultimo ? ((int) substr($ultimo, 5)) + 1 : 1;

            return static::create(array_merge($datos, [
                'numero'       => $anio.'-'.str_pad((string) $siguiente, 6, '0', STR_PAD_LEFT),
                'estado'       => self::PENDIENTE,
                'fecha_limite' => PlazoHabil::sumar(now(), config('empresa.plazo_dias_habiles')),
            ]));
        });
    }

    public function responder(string $respuesta, User $usuario): void
    {
        $this->update([
            'respuesta'      => $respuesta,
            'estado'         => self::ATENDIDO,
            'respondido_at'  => now(),
            'respondido_por' => $usuario->id,
        ]);
    }

    public function respondidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::PENDIENTE;
    }

    public function estaVencida(): bool
    {
        return $this->estaPendiente() && $this->fecha_limite->lt(Carbon::today());
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', self::PENDIENTE);
    }

    public function scopeVencidas(Builder $query): Builder
    {
        return $query->pendientes()->whereDate('fecha_limite', '<', Carbon::today());
    }
}
