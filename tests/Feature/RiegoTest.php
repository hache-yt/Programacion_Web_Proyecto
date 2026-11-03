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

    public function test_guarda_un_riego_valido(): void
    {
        $parcela = $this->crearParcela();

        $this->post('/riegos', $this->datosRiego($parcela))->assertRedirect(route('riegos.index'));

        $this->assertDatabaseHas('riegos', ['parcela_id' => $parcela->id, 'duracion_min' => 30]);
        $this->assertDatabaseCount('alertas', 0);
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

    public function test_no_permite_registrar_riego_en_parcela_ajena(): void
    {
        $ajena = $this->crearParcela(User::factory()->create(), 'Ajena');

        $this->post('/riegos', $this->datosRiego($ajena))->assertSessionHasErrors('parcela_id');
        $this->assertDatabaseCount('riegos', 0);
    }

    public function test_historial_filtra_por_parcela(): void
    {
        $a = $this->crearParcela(null, 'Parcela A');
        $b = $this->crearParcela(null, 'Parcela B');
        Riego::create($this->datosRiego($a, ['observaciones' => 'riego-a']));
        Riego::create($this->datosRiego($b, ['observaciones' => 'riego-b']));

        $this->get('/riegos?parcela_id='.$a->id)
            ->assertOk()
            ->assertSee('riego-a')
            ->assertDontSee('riego-b');
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
        $parcela = $this->crearParcela();
        $riego = Riego::create($this->datosRiego($parcela));

        $this->put('/riegos/'.$riego->id, $this->datosRiego($parcela, ['duracion_min' => 50]))
            ->assertRedirect(route('riegos.index'));
        $this->assertDatabaseHas('riegos', ['id' => $riego->id, 'duracion_min' => 50]);

        $this->delete('/riegos/'.$riego->id)->assertRedirect(route('riegos.index'));
        $this->assertDatabaseMissing('riegos', ['id' => $riego->id]);
    }

    public function test_crud_de_parcelas_con_propietario(): void
    {
        $this->post('/parcelas', ['nombre' => 'Área verde Plaza', 'area_m2' => 400])
            ->assertRedirect(route('parcelas.index'));
        $parcela = Parcela::firstOrFail();
        $this->assertSame($this->usuario->id, $parcela->usuario_id);

        $this->put('/parcelas/'.$parcela->id, ['nombre' => 'Plaza Central'])
            ->assertRedirect(route('parcelas.index'));
        $this->assertDatabaseHas('parcelas', ['nombre' => 'Plaza Central']);

        $this->delete('/parcelas/'.$parcela->id)->assertRedirect(route('parcelas.index'));
        $this->assertDatabaseCount('parcelas', 0);
    }

    public function test_parcela_ajena_no_se_edita(): void
    {
        $ajena = $this->crearParcela(User::factory()->create(), 'Ajena');

        $this->get('/parcelas/'.$ajena->id.'/edit')->assertForbidden();
        $this->delete('/parcelas/'.$ajena->id)->assertForbidden();
    }

    public function test_parcela_requiere_nombre(): void
    {
        $this->post('/parcelas', ['nombre' => ''])->assertSessionHasErrors('nombre');
    }

    public function test_estadisticas_y_alertas_se_muestran(): void
    {
        $parcela = $this->crearParcela();
        Riego::create($this->datosRiego($parcela));

        $this->get('/estadisticas')->assertOk()->assertSee('Litros por día');
        $this->get('/alertas')->assertOk();
        $this->get('/')->assertOk()->assertSee('Riegos registrados');
    }
}
