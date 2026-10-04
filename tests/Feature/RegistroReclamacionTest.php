<?php

namespace Tests\Feature;

use App\Livewire\ReclamoPublico;
use App\Mail\ConstanciaReclamacion;
use App\Models\Reclamacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RegistroReclamacionTest extends TestCase
{
    use RefreshDatabase;

    private function formularioValido()
    {
        return Livewire::test(ReclamoPublico::class)
            ->set('nombres_apellidos', 'María Quispe Ramos')
            ->set('tipo_documento', 'DNI')
            ->set('numero_documento', '45678912')
            ->set('domicilio', 'Av. Los Olivos 456, Lima')
            ->set('telefono', '987654321')
            ->set('correo', 'maria@example.com')
            ->set('tipo_bien', 'producto')
            ->set('monto_reclamado', '350.50')
            ->set('descripcion_bien', 'Licuadora 600W')
            ->set('tipo_registro', 'reclamo')
            ->set('detalle', 'El producto llegó con la jarra rota.')
            ->set('pedido', 'Cambio del producto.')
            ->set('acepta_politicas', true)
            ->set('declaracion_veracidad', true);
    }

    public function test_la_pagina_del_formulario_carga(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Libro de Reclamaciones Virtual')
            ->assertSeeLivewire(ReclamoPublico::class);
    }

    public function test_registra_la_hoja_con_correlativo_y_fecha_limite(): void
    {
        Mail::fake();

        $this->formularioValido()
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertSet('numeroGenerado', now()->format('Y').'-000001');

        $reclamacion = Reclamacion::sole();

        $this->assertSame(Reclamacion::PENDIENTE, $reclamacion->estado);
        $this->assertSame('María Quispe Ramos', $reclamacion->nombres_apellidos);
        $this->assertSame('350.50', $reclamacion->monto_reclamado);
        $this->assertTrue($reclamacion->fecha_limite->gt(now()));
    }

    public function test_el_correlativo_aumenta_en_cada_registro(): void
    {
        Mail::fake();

        $this->formularioValido()->call('guardar');
        $this->formularioValido()->call('guardar')
            ->assertSet('numeroGenerado', now()->format('Y').'-000002');

        // El mismo documento puede registrar varias hojas
        $this->assertSame(2, Reclamacion::where('numero_documento', '45678912')->count());
    }

    public function test_envia_copia_de_la_hoja_al_consumidor(): void
    {
        Mail::fake();

        $this->formularioValido()->call('guardar');

        Mail::assertSent(ConstanciaReclamacion::class, fn ($mail) => $mail->hasTo('maria@example.com'));
    }

    public function test_guarda_la_evidencia_en_disco_privado(): void
    {
        Mail::fake();
        Storage::fake('local');

        $this->formularioValido()
            ->set('evidencia', UploadedFile::fake()->create('boleta.pdf', 120, 'application/pdf'))
            ->call('guardar')
            ->assertHasNoErrors();

        Storage::disk('local')->assertExists(Reclamacion::sole()->evidencia_path);
    }

    public function test_valida_campos_obligatorios(): void
    {
        Livewire::test(ReclamoPublico::class)
            ->call('guardar')
            ->assertHasErrors([
                'nombres_apellidos' => 'required',
                'numero_documento'  => 'required',
                'correo'            => 'required',
                'descripcion_bien'  => 'required',
                'detalle'           => 'required',
                'pedido'            => 'required',
                'acepta_politicas'  => 'accepted',
            ]);

        $this->assertSame(0, Reclamacion::count());
    }

    public function test_exige_apoderado_si_es_menor_de_edad(): void
    {
        $this->formularioValido()
            ->set('es_menor_edad', true)
            ->call('guardar')
            ->assertHasErrors(['apoderado_nombre']);
    }

    public function test_rechaza_evidencia_que_no_es_pdf(): void
    {
        $this->formularioValido()
            ->set('evidencia', UploadedFile::fake()->create('foto.exe', 10))
            ->call('guardar')
            ->assertHasErrors(['evidencia']);
    }
}
