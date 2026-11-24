<?php

namespace Tests\Feature;

use App\Models\Medicion;
use App\Models\SistemaRiego;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemaRiegoTest extends TestCase
{
    use RefreshDatabase;

    private function crearSistema(User $usuario, string $nombre = 'Sistema de prueba'): SistemaRiego
    {
        $parcela = $usuario->parcelas()->create(['nombre' => 'Parcela principal']);
        $sistema = $parcela->sistemasRiego()->create(['nombre' => $nombre]);
        $sistema->configuracion()->create(['modo' => 'manual']);

        return $sistema;
    }

    public function test_cliente_no_puede_ver_ni_controlar_sistemas_de_otra_cuenta(): void
    {
        $cliente = User::factory()->create();
        $ajeno = $this->crearSistema(User::factory()->create(), 'Sistema privado');

        $this->actingAs($cliente)
            ->get(route('sistemas.show', $ajeno))
            ->assertForbidden();

        $this->get(route('estadisticas.index', ['sistema_riego_id' => $ajeno->id]))
            ->assertNotFound();
        $this->post(route('sistemas.iniciar', $ajeno))->assertForbidden();
        $this->post(route('sistemas.mediciones.store', $ajeno), [
            'tipo' => 'humedad_suelo',
            'valor' => 25,
            'unidad' => '%',
            'fecha_lectura' => now()->format('Y-m-d\TH:i'),
        ])->assertForbidden();

        $this->get(route('sistemas.index'))->assertDontSee('Sistema privado');
    }

    public function test_estadisticas_agregan_litros_y_promedios_diarios_en_la_consulta(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $parcela = $sistema->parcela;

        $parcela->riegos()->create([
            'sistema_riego_id' => $sistema->id,
            'fecha' => now()->toDateString(),
            'duracion_min' => 20,
            'cantidad_litros' => 125,
        ]);
        $parcela->riegos()->create([
            'sistema_riego_id' => $sistema->id,
            'fecha' => now()->toDateString(),
            'duracion_min' => 30,
            'cantidad_litros' => 275,
        ]);
        foreach ([30, 50] as $valor) {
            Medicion::create([
                'sistema_riego_id' => $sistema->id,
                'tipo' => 'humedad_suelo',
                'valor' => $valor,
                'unidad' => '%',
                'fecha_lectura' => now(),
                'es_prueba' => true,
            ]);
        }

        $this->actingAs($cliente)
            ->get(route('estadisticas.index', ['sistema_riego_id' => $sistema->id]))
            ->assertOk()
            ->assertViewHas('totalLitros', 400.0)
            ->assertViewHas('lecturasPorTipo', function ($lecturasPorTipo) {
                return $lecturasPorTipo['humedad_suelo']->last() === 40.0;
            });
    }

    public function test_cliente_puede_crear_sistema_en_su_parcela_principal(): void
    {
        $cliente = User::factory()->create();

        $this->actingAs($cliente)
            ->post(route('sistemas.store'), ['nombre' => 'Invernadero', 'descripcion' => 'Zona norte'])
            ->assertRedirect();

        $sistema = SistemaRiego::where('nombre', 'Invernadero')->firstOrFail();
        $this->assertSame($cliente->id, $sistema->parcela->usuario_id);
        $this->assertSame('manual', $sistema->configuracion->modo);
        $this->assertFalse($sistema->riego_activo);
        $this->get(route('sistemas.show', $sistema))
            ->assertOk()
            ->assertSee('NO CONECTADA')
            ->assertSee('Sin datos')
            ->assertSee('Control manual');
    }

    public function test_cliente_puede_registrar_un_equipo_fisico_simulado(): void
    {
        $cliente = User::factory()->create();

        $this->actingAs($cliente)
            ->post(route('sistemas.store'), [
                'nombre' => 'Invernadero norte',
                'descripcion' => 'Equipo principal',
                'registro_fisico' => '1',
                'tipo_conexion' => 'wifi',
                'direccion_ip' => '192.168.1.120',
                'identificador_dispositivo' => 'ESP32-RIEGO-01',
                'dispositivo_encontrado' => '1',
            ])
            ->assertRedirect();

        $sistema = SistemaRiego::where('nombre', 'Invernadero norte')->firstOrFail();
        $this->assertTrue($sistema->conexion_simulada);
        $this->assertSame('wifi', $sistema->tipo_conexion);
        $this->assertSame('192.168.1.120', $sistema->direccion_ip);
        $this->assertSame('ESP32-RIEGO-01', $sistema->identificador_dispositivo);

        $this->get(route('sistemas.show', $sistema))
            ->assertOk()
            ->assertSee('ENCONTRADO (SIMULADO)')
            ->assertSee('ESP32-RIEGO-01');
    }

    public function test_registro_fisico_requiere_tipo_ip_e_identificador(): void
    {
        $cliente = User::factory()->create();

        $this->actingAs($cliente)
            ->post(route('sistemas.store'), [
                'nombre' => 'Equipo incompleto',
                'registro_fisico' => '1',
                'tipo_conexion' => '',
                'direccion_ip' => 'direccion-invalida',
                'identificador_dispositivo' => '',
            ])
            ->assertSessionHasErrors(['tipo_conexion', 'direccion_ip', 'identificador_dispositivo']);
    }

    public function test_lecturas_de_prueba_validan_tipo_unidad_y_rango(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $url = route('sistemas.mediciones.store', $sistema);
        $datos = [
            'tipo' => 'humedad_suelo',
            'valor' => 42.5,
            'unidad' => '%',
            'sensor_id' => 'sensor-prueba-1',
            'fecha_lectura' => now()->format('Y-m-d\TH:i'),
        ];

        $this->actingAs($cliente)->post($url, $datos)->assertRedirect();

        $this->assertDatabaseHas('medicions', [
            'sistema_riego_id' => $sistema->id,
            'tipo' => 'humedad_suelo',
            'valor' => 42.5,
            'es_prueba' => true,
        ]);

        $this->post($url, array_merge($datos, ['valor' => 120]))
            ->assertSessionHasErrors('valor');
        $this->post($url, array_merge($datos, ['unidad' => 'L']))
            ->assertSessionHasErrors('unidad');
        $this->post($url, array_merge($datos, ['fecha_lectura' => now()->addDay()->format('Y-m-d\TH:i')]))
            ->assertSessionHasErrors('fecha_lectura');

        $this->assertSame(1, Medicion::count());
    }
}
