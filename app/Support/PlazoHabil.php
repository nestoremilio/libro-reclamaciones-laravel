<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Calcula fechas en días hábiles (lunes a viernes).
 *
 * No considera feriados: si necesitas incluirlos, agrégalos en $feriados
 * con formato Y-m-d.
 */
class PlazoHabil
{
    /** @var array<int, string> */
    public static array $feriados = [];

    public static function sumar(CarbonInterface $desde, int $dias): Carbon
    {
        $fecha = Carbon::instance($desde)->startOfDay();
        $agregados = 0;

        while ($agregados < $dias) {
            $fecha->addDay();

            if (self::esHabil($fecha)) {
                $agregados++;
            }
        }

        return $fecha;
    }

    public static function esHabil(CarbonInterface $fecha): bool
    {
        return ! $fecha->isWeekend()
            && ! in_array($fecha->format('Y-m-d'), self::$feriados, true);
    }
}
