<?php

namespace Database\Factories;

use App\Models\Reclamacion;
use App\Support\PlazoHabil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reclamacion>
 */
class ReclamacionFactory extends Factory
{
    protected $model = Reclamacion::class;

    private static int $correlativo = 0;

    public function definition(): array
    {
        $creado = now()->subDays(fake()->numberBetween(0, 5));
        $tipoBien = fake()->randomElement(['producto', 'servicio']);

        return [
            'numero'                => now()->format('Y').'-'.str_pad((string) ++self::$correlativo, 6, '0', STR_PAD_LEFT),
            'nombres_apellidos'     => fake()->firstName().' '.fake()->lastName().' '.fake()->lastName(),
            'tipo_documento'        => 'DNI',
            'numero_documento'      => fake()->numerify('########'),
            'domicilio'             => fake()->streetAddress().', Lima',
            'telefono'              => fake()->numerify('9########'),
            'correo'                => fake()->safeEmail(),
            'es_menor_edad'         => false,
            'apoderado_nombre'      => null,
            'tipo_bien'             => $tipoBien,
            'monto_reclamado'       => fake()->randomFloat(2, 20, 1500),
            'descripcion_bien'      => $tipoBien === 'producto'
                ? fake()->randomElement(['Laptop modelo X14', 'Zapatillas talla 42', 'Licuadora 600W', 'Celular gama media'])
                : fake()->randomElement(['Instalación de internet', 'Servicio de delivery', 'Mantenimiento de equipo', 'Atención en tienda']),
            'tipo_registro'         => fake()->randomElement(['reclamo', 'reclamo', 'queja']),
            'detalle'               => fake()->randomElement([
                'El producto llegó con la caja dañada y una de las piezas no funciona.',
                'El técnico no se presentó en la fecha y hora acordadas para la instalación.',
                'Se realizó el cobro dos veces en la misma compra con tarjeta.',
                'El pedido llegó incompleto: faltan dos unidades de lo comprado.',
                'La atención en caja fue descortés y demoraron más de 40 minutos.',
                'El equipo dejó de funcionar a la semana de la compra y aún está en garantía.',
            ]),
            'pedido'                => fake()->randomElement(['Cambio del producto', 'Devolución del dinero', 'Reprogramación del servicio', 'Una disculpa formal']),
            'acepta_politicas'      => true,
            'declaracion_veracidad' => true,
            'estado'                => Reclamacion::PENDIENTE,
            'fecha_limite'          => PlazoHabil::sumar($creado, 15),
            'created_at'            => $creado,
            'updated_at'            => $creado,
        ];
    }

    public function atendida(): static
    {
        return $this->state(fn () => [
            'estado'        => Reclamacion::ATENDIDO,
            'respuesta'     => 'Se coordinó con el cliente y se aplicó la solución solicitada.',
            'respondido_at' => fn (array $atributos) => \Illuminate\Support\Carbon::parse($atributos['created_at'])->addDays(fake()->numberBetween(1, 6)),
        ]);
    }
}
