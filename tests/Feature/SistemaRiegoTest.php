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
        $sistema->configuracion()->create(['modo' => 'manual', 'unidad_nivel_agua' => '%']);

        return $sistema;
    }

    public function test_cliente_no_puede_ver_ni_controlar_sistemas_de_otra_cuenta(): void
    {
        $cliente = User::factory()->create();
        $ajeno = $this->crearSistema(User::factory()->create(), 'Sistema privado');

        $this->actingAs($cliente)
            ->get(route('sistemas.show', $ajeno))
            ->assertForbidden();

        $this->post(route('sistemas.iniciar', $ajeno))->assertForbidden();
        $this->post(route('sistemas.mediciones.store', $ajeno), [
            'tipo' => 'humedad_suelo',
            'valor' => 25,
            'unidad' => '%',
            'fecha_lectura' => now()->format('Y-m-d\TH:i'),
        ])->assertForbidden();

        $this->get(route('sistemas.index'))->assertDontSee('Sistema privado');
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

    public function test_nivel_de_agua_respeta_la_unidad_configurada_por_sistema(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $sistema->configuracion->update(['unidad_nivel_agua' => 'L']);

        $this->actingAs($cliente)->post(route('sistemas.mediciones.store', $sistema), [
            'tipo' => 'nivel_agua',
            'valor' => 80,
            'unidad' => '%',
            'fecha_lectura' => now()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('unidad');

        $this->post(route('sistemas.mediciones.store', $sistema), [
            'tipo' => 'nivel_agua',
            'valor' => 80,
            'unidad' => 'L',
            'fecha_lectura' => now()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('medicions', [
            'sistema_riego_id' => $sistema->id,
            'tipo' => 'nivel_agua',
            'unidad' => 'L',
        ]);
    }

    public function test_cambio_a_modo_automatico_registra_usuario_y_reglas_por_sistema(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $this->actingAs($cliente)
            ->put(route('sistemas.configuracion.update', $sistema), [
                'modo' => 'automatico',
                'unidad_nivel_agua' => '%',
                'humedad_suelo_minima' => 25,
                'humedad_suelo_maxima' => 45,
                'nivel_agua_minimo' => 20,
                'dias_permitidos' => [1, 3, 5],
                'hora_inicio' => '05:00',
                'hora_fin' => '07:30',
                'duracion_maxima_min' => 20,
                'pausa_minima_min' => 60,
            ])->assertRedirect();

        $this->assertDatabaseHas('configuracions', [
            'sistema_riego_id' => $sistema->id,
            'modo' => 'automatico',
            'humedad_suelo_minima' => 25,
            'humedad_suelo_maxima' => 45,
            'nivel_agua_minimo' => 20,
        ]);
        $this->assertDatabaseHas('comandos', [
            'sistema_riego_id' => $sistema->id,
            'user_id' => $cliente->id,
            'accion' => 'cambio_modo',
            'resultado' => 'registrado',
        ]);

        $this->post(route('sistemas.iniciar', $sistema))
            ->assertSessionHasErrors('control');
        $this->assertFalse($sistema->fresh()->riego_activo);
    }

    public function test_orden_manual_se_registra_como_no_conectada_y_no_activa_el_riego(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $this->actingAs($cliente);

        $this->post(route('sistemas.iniciar', $sistema))
            ->assertSessionHas('ok');
        $this->post(route('sistemas.iniciar', $sistema))
            ->assertSessionHas('ok');

        $this->assertDatabaseCount('comandos', 1);
        $this->assertDatabaseHas('comandos', [
            'sistema_riego_id' => $sistema->id,
            'accion' => 'iniciar',
            'resultado' => 'no_conectado',
        ]);

        $this->post(route('sistemas.detener', $sistema))
            ->assertSessionHas('ok');
        $this->assertDatabaseHas('comandos', [
            'sistema_riego_id' => $sistema->id,
            'accion' => 'detener',
            'resultado' => 'no_conectado',
        ]);
        $this->assertDatabaseCount('comandos', 2);
        $this->assertFalse($sistema->fresh()->riego_activo);
    }

    public function test_datos_automaticos_incoherentes_no_se_guardan(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);

        $this->actingAs($cliente)
            ->put(route('sistemas.configuracion.update', $sistema), [
                'modo' => 'automatico',
                'unidad_nivel_agua' => '%',
                'humedad_suelo_minima' => 50,
                'humedad_suelo_maxima' => 40,
                'nivel_agua_minimo' => 20,
                'dias_permitidos' => [1],
                'hora_inicio' => '05:00',
                'hora_fin' => '07:30',
                'duracion_maxima_min' => 20,
                'pausa_minima_min' => 60,
            ])->assertSessionHasErrors('humedad_suelo_maxima');

        $this->assertSame('manual', $sistema->configuracion->fresh()->modo);
    }

    public function test_panel_marca_una_lectura_antigua_como_desactualizada(): void
    {
        $cliente = User::factory()->create();
        $sistema = $this->crearSistema($cliente);
        $sistema->mediciones()->create([
            'tipo' => 'humedad_suelo',
            'valor' => 35,
            'unidad' => '%',
            'fecha_lectura' => now()->subMinutes(31),
            'es_prueba' => true,
        ]);

        $this->actingAs($cliente)
            ->get(route('sistemas.show', $sistema))
            ->assertOk()
            ->assertSee('Dato desactualizado')
            ->assertSee('Desactualizada');
    }
}
