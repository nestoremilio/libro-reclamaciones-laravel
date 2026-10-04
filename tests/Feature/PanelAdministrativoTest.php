<?php

namespace Tests\Feature;

use App\Mail\ConstanciaReclamacion;
use App\Mail\RespuestaReclamacion;
use App\Models\Reclamacion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PanelAdministrativoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_requiere_iniciar_sesion(): void
    {
        $reclamacion = Reclamacion::factory()->create();

        $this->get('/admin')->assertRedirect('/login');
        $this->get(route('admin.show', $reclamacion))->assertRedirect('/login');
        $this->get(route('admin.reporte', $reclamacion))->assertRedirect('/login');
    }

    public function test_login_correcto_e_incorrecto(): void
    {
        $user = User::factory()->create(['password' => 'secreto-123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'otra'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'secreto-123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_bloquea_el_login_tras_varios_intentos(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'mal']);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'mal'])->assertStatus(429);
    }

    public function test_la_bandeja_lista_y_filtra(): void
    {
        $pendiente = Reclamacion::factory()->create(['nombres_apellidos' => 'Ana Pendiente']);
        $atendida = Reclamacion::factory()->atendida()->create(['nombres_apellidos' => 'Luis Atendido']);
        $vencida = Reclamacion::factory()->create([
            'nombres_apellidos' => 'Rosa Vencida',
            'fecha_limite'      => now()->subDays(3),
        ]);

        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertOk()->assertSee(['Ana Pendiente', 'Luis Atendido', 'Rosa Vencida']);

        $this->get('/admin?estado=atendido')->assertSee('Luis Atendido')->assertDontSee('Ana Pendiente');

        $this->get('/admin?estado=vencidas')->assertSee('Rosa Vencida')->assertDontSee('Ana Pendiente');

        $this->get('/admin?buscar='.$pendiente->numero)->assertSee('Ana Pendiente')->assertDontSee('Luis Atendido');
    }

    public function test_responder_marca_como_atendida_y_notifica(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $reclamacion = Reclamacion::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.responder', $reclamacion), ['respuesta' => 'Se realizó el cambio del producto.'])
            ->assertRedirect(route('admin.show', $reclamacion));

        $reclamacion->refresh();

        $this->assertSame(Reclamacion::ATENDIDO, $reclamacion->estado);
        $this->assertSame($user->id, $reclamacion->respondido_por);
        $this->assertNotNull($reclamacion->respondido_at);

        Mail::assertSent(RespuestaReclamacion::class, fn ($mail) => $mail->hasTo($reclamacion->correo));
    }

    public function test_la_respuesta_es_obligatoria(): void
    {
        $reclamacion = Reclamacion::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('admin.responder', $reclamacion), ['respuesta' => ''])
            ->assertSessionHasErrors('respuesta');

        $this->assertTrue($reclamacion->fresh()->estaPendiente());
    }

    public function test_descarga_la_hoja_en_pdf(): void
    {
        $reclamacion = Reclamacion::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('admin.reporte', $reclamacion))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_no_existen_rutas_de_mantenimiento_publicas(): void
    {
        foreach (['/limpiar', '/actualizar-bd', '/crear-admin', '/instalar-dependencias'] as $ruta) {
            $this->get($ruta)->assertNotFound();
        }
    }

    public function test_el_detalle_muestra_formulario_o_respuesta(): void
    {
        $this->actingAs(User::factory()->create());

        $pendiente = Reclamacion::factory()->create();
        $this->get(route('admin.show', $pendiente))->assertOk()->assertSee('Registrar y enviar respuesta');

        $atendida = Reclamacion::factory()->atendida()->create();
        $this->get(route('admin.show', $atendida))->assertOk()->assertSee($atendida->respuesta)->assertDontSee('Registrar y enviar respuesta');
    }

    public function test_los_correos_se_generan_con_los_datos_de_la_hoja(): void
    {
        $reclamacion = Reclamacion::factory()->atendida()->create();

        (new ConstanciaReclamacion($reclamacion))
            ->assertSeeInHtml($reclamacion->numero)
            ->assertSeeInHtml(config('empresa.razon_social'));

        (new RespuestaReclamacion($reclamacion))
            ->assertSeeInHtml($reclamacion->numero)
            ->assertSeeInHtml($reclamacion->respuesta);
    }
}
