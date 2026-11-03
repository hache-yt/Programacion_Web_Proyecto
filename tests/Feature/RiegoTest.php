<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Parcela;
use App\Models\Riego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiegoTest extends TestCase
{
    use RefreshDatabase;

    private User $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = User::factory()->create();
        $this->actingAs($this->usuario);
    }

    private function crearParcela(?User $dueno = null, string $nombre = 'Parcela Saylla 1'): Parcela
    {
        return ($dueno ?? $this->usuario)->parcelas()->create([
            'nombre' => $nombre,
            'ubicacion' => 'Saylla, Cusco',
            'area_m2' => 1200,
            'tipo_cultivo' => 'Maíz',
        ]);
    }

    private function datosRiego(Parcela $parcela, array $cambios = []): array
    {
        return array_merge([
            'parcela_id' => $parcela->id,
            'fecha' => now()->toDateString(),
            'duracion_min' => 30,
            'cantidad_litros' => 650,
            'observaciones' => 'Riego por goteo',
        ], $cambios);
    }

    public function test_guarda_un_riego_valido_en_la_parcela_principal(): void
    {
        $principal = $this->crearParcela();
        $historica = $this->crearParcela(null, 'Parcela histórica');

        $this->post('/riegos', $this->datosRiego($historica))->assertRedirect(route('riegos.index'));

        $this->assertDatabaseHas('riegos', ['parcela_id' => $principal->id, 'duracion_min' => 30]);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_formulario_de_riego_no_muestra_selector_de_parcela(): void
    {
        $this->get('/riegos/create')
            ->assertOk()
            ->assertDontSee('parcela_id')
            ->assertDontSee('Selecciona una parcela');
    }

    public function test_rechaza_fecha_futura(): void
    {
        $parcela = $this->crearParcela();

        $this->post('/riegos', $this->datosRiego($parcela, ['fecha' => now()->addDay()->toDateString()]))
            ->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('riegos', 0);
    }

    public function test_rechaza_duracion_y_litros_invalidos(): void
    {
        $parcela = $this->crearParcela();

        $this->post('/riegos', $this->datosRiego($parcela, ['duracion_min' => 0, 'cantidad_litros' => -5]))
            ->assertSessionHasErrors(['duracion_min', 'cantidad_litros']);
    }

    public function test_historial_conserva_registros_historicos_y_filtra_por_fecha(): void
    {
        $a = $this->crearParcela(null, 'Parcela A');
        $b = $this->crearParcela(null, 'Parcela B');
        Riego::create($this->datosRiego($a, ['observaciones' => 'riego-a', 'fecha' => now()->toDateString()]));
        Riego::create($this->datosRiego($b, ['observaciones' => 'riego-b', 'fecha' => now()->subDay()->toDateString()]));

        $this->get('/riegos?fecha='.now()->toDateString())
            ->assertOk()
            ->assertSee('riego-a')
            ->assertDontSee('riego-b');

        $this->assertDatabaseCount('riegos', 2);
        $this->get('/riegos')->assertSee('riego-a')->assertSee('riego-b');
    }

    public function test_usuario_no_ve_ni_edita_riegos_ajenos(): void
    {
        $ajena = $this->crearParcela(User::factory()->create(), 'Ajena');
        $riego = Riego::create($this->datosRiego($ajena, ['observaciones' => 'riego-ajeno']));

        $this->get('/riegos')->assertDontSee('riego-ajeno');
        $this->get('/riegos/'.$riego->id.'/edit')->assertForbidden();
        $this->put('/riegos/'.$riego->id, $this->datosRiego($ajena))->assertForbidden();
        $this->delete('/riegos/'.$riego->id)->assertForbidden();
    }

    public function test_administrador_ve_todos_los_riegos(): void
    {
        $ajena = $this->crearParcela(User::factory()->create(), 'Ajena');
        Riego::create($this->datosRiego($ajena, ['observaciones' => 'riego-ajeno']));

        $this->actingAs(User::factory()->create(['rol' => 'administrador']))
            ->get('/riegos')
            ->assertSee('riego-ajeno');
    }

    public function test_consumo_elevado_genera_alerta_y_se_marca_atendida(): void
    {
        $parcela = $this->crearParcela();

        $this->post('/riegos', $this->datosRiego($parcela, ['cantidad_litros' => 5000]));

        $alerta = Alerta::firstOrFail();
        $this->assertSame('pendiente', $alerta->estado);
        $this->get('/riegos')->assertSee('Consumo elevado');

        $this->patch('/alertas/'.$alerta->id)->assertRedirect(route('alertas.index'));
        $this->assertSame('atendida', $alerta->fresh()->estado);
    }

    public function test_actualiza_y_elimina_un_riego(): void
    {
        $principal = $this->crearParcela();
        $historica = $this->crearParcela(null, 'Parcela histórica');
        $riego = Riego::create($this->datosRiego($historica));

        $this->put('/riegos/'.$riego->id, $this->datosRiego($principal, ['duracion_min' => 50]))
            ->assertRedirect(route('riegos.index'));
        $this->assertDatabaseHas('riegos', [
            'id' => $riego->id,
            'duracion_min' => 50,
            'parcela_id' => $historica->id,
        ]);

        $this->delete('/riegos/'.$riego->id)->assertRedirect(route('riegos.index'));
        $this->assertDatabaseMissing('riegos', ['id' => $riego->id]);
    }

    public function test_la_interfaz_muestra_una_parcela_y_no_expone_gestion_multiple(): void
    {
        $principal = $this->crearParcela(null, 'Parcela principal existente');
        $historica = $this->crearParcela(null, 'Parcela histórica');
        $riego = Riego::create($this->datosRiego($historica));

        $this->get('/parcelas')
            ->assertOk()
            ->assertSee('Parcela principal existente')
            ->assertDontSee('Parcela histórica')
            ->assertDontSee('Nueva parcela');

        $this->post('/parcelas')->assertStatus(405);
        $this->get('/parcelas/create')->assertNotFound();
        $this->put('/parcelas/'.$principal->id)->assertNotFound();
        $this->delete('/parcelas/'.$principal->id)->assertNotFound();
        $this->assertDatabaseHas('parcelas', ['id' => $principal->id]);
        $this->assertDatabaseHas('parcelas', ['id' => $historica->id]);
        $this->assertDatabaseHas('riegos', ['id' => $riego->id, 'parcela_id' => $historica->id]);
    }

    public function test_admin_ve_solo_la_parcela_principal_de_cada_cuenta(): void
    {
        $otro = User::factory()->create();
        $this->crearParcela($otro, 'Principal de otro usuario');
        $this->crearParcela($otro, 'Parcela histórica de otro usuario');

        $this->actingAs(User::factory()->create(['rol' => 'administrador']))
            ->get('/parcelas')
            ->assertOk()
            ->assertSee('Principal de otro usuario')
            ->assertDontSee('Parcela histórica de otro usuario');
    }

    public function test_estadisticas_y_alertas_se_muestran(): void
    {
        $parcela = $this->crearParcela();
        Riego::create($this->datosRiego($parcela));

        $this->get('/estadisticas')->assertOk()->assertSee('Consumo diario');
        $this->get('/alertas')->assertOk();
        $this->get('/')->assertOk()->assertSee('Riegos registrados');
    }
}
