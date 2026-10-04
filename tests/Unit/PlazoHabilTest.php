<?php

namespace Tests\Unit;

use App\Support\PlazoHabil;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class PlazoHabilTest extends TestCase
{
    protected function tearDown(): void
    {
        PlazoHabil::$feriados = [];
        parent::tearDown();
    }

    public function test_suma_dias_habiles_saltando_fines_de_semana(): void
    {
        // Viernes 2 de octubre de 2026 + 1 día hábil = lunes 5
        $this->assertSame('2026-10-05', PlazoHabil::sumar(Carbon::parse('2026-10-02'), 1)->toDateString());

        // Lunes 5 de octubre + 15 días hábiles = lunes 26 de octubre
        $this->assertSame('2026-10-26', PlazoHabil::sumar(Carbon::parse('2026-10-05'), 15)->toDateString());
    }

    public function test_respeta_feriados_configurados(): void
    {
        PlazoHabil::$feriados = ['2026-10-08'];

        // Miércoles 7 + 1 día hábil, con jueves 8 feriado = viernes 9
        $this->assertSame('2026-10-09', PlazoHabil::sumar(Carbon::parse('2026-10-07'), 1)->toDateString());
    }
}
