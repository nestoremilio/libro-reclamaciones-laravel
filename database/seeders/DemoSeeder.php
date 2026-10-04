<?php

namespace Database\Seeders;

use App\Models\Reclamacion;
use App\Support\PlazoHabil;
use Illuminate\Database\Seeder;

/**
 * Datos ficticios para la demo pública: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 24 hojas en orden cronológico: las más antiguas atendidas, algunas vencidas y otras recientes
        for ($i = 0; $i < 24; $i++) {
            $creado = now()->subDays(48 - $i * 2)->setTime(rand(8, 19), rand(0, 59));
            $fechas = [
                'created_at'   => $creado,
                'updated_at'   => $creado,
                'fecha_limite' => PlazoHabil::sumar($creado, config('empresa.plazo_dias_habiles')),
            ];

            $factory = Reclamacion::factory();

            if ($i < 12) {
                $factory = $factory->atendida();
            }

            $factory->create($fechas);
        }
    }
}
