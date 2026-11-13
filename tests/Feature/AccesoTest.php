<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_inicio_es_publico(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Sabe cuánta agua usas al regar')
            ->assertSee('presentation-screen')
            ->assertSee('Universidad Continental')
            ->assertSee('2026-II')
            ->assertSee('cookie-banner');
    }

    public function test_paginas_publicas_de_privacidad_ayuda_y_autoria(): void
    {
        $this->get('/privacidad')
            ->assertOk()
            ->assertSee('Política de privacidad')
            ->assertSee('no se transmite al servidor');

        $this->get('/terminos')
            ->assertOk()
            ->assertSee('Términos de uso')
            ->assertSee('No controla bombas');

        $this->get('/ayuda')
            ->assertOk()
            ->assertSee('Centro de ayuda')
            ->assertSee('data-location-request');

        $this->get('/acerca-de')
            ->assertOk()
            ->assertSee('Said Torres Lozano')
            ->assertSee('Rey Kino Choquichampi Mendoza');
    }

    public function test_invitado_es_enviado_a_ingresar(): void
    {
        $this->get('/riegos')->assertRedirect(route('login'));
        $this->get('/parcelas')->assertRedirect(route('login'));
    }

    public function test_registro_crea_usuario_con_rol_usuario(): void
    {
        $this->post('/registro', [
            'name' => 'Rey Kino',
            'email' => 'kino@correo.com',
            'password' => 'clave12345',
            'password_confirmation' => 'clave12345',
        ])->assertRedirect(route('inicio'));

        $this->assertDatabaseHas('users', ['email' => 'kino@correo.com', 'rol' => 'usuario']);
        $this->assertDatabaseHas('parcelas', [
            'usuario_id' => User::where('email', 'kino@correo.com')->value('id'),
            'nombre' => 'Parcela principal',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registro_rechaza_correo_repetido_y_clave_corta(): void
    {
        User::factory()->create(['email' => 'kino@correo.com']);

        $this->post('/registro', [
            'name' => 'Otro',
            'email' => 'kino@correo.com',
            'password' => 'corta',
            'password_confirmation' => 'corta',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_correcto_e_incorrecto(): void
    {
        $usuario = User::factory()->create(['password' => 'clave12345']);

        $this->post('/ingresar', ['email' => $usuario->email, 'password' => 'mala'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/ingresar', ['email' => $usuario->email, 'password' => 'clave12345'])
            ->assertRedirect(route('inicio'));
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_logout(): void
    {
        $this->actingAs(User::factory()->create())->post('/salir')->assertRedirect(route('inicio'));
        $this->assertGuest();
    }

    public function test_usuarios_solo_para_administrador(): void
    {
        $this->actingAs(User::factory()->create())->get('/usuarios')->assertForbidden();
        $admin = User::factory()->create(['rol' => 'administrador']);
        $this->actingAs($admin)->get('/usuarios')->assertOk();
        $this->actingAs($admin)->get('/administracion')->assertOk()->assertSee('Panel administrativo');
    }

    public function test_administrador_elimina_usuario_pero_no_a_si_mismo(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $otro = User::factory()->create();

        $this->actingAs($admin)->delete('/usuarios/'.$otro->id)->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseMissing('users', ['id' => $otro->id]);

        $this->actingAs($admin)->delete('/usuarios/'.$admin->id)->assertSessionHasErrors('usuario');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_rutas_administrativas_requieren_rol_de_administrador(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get('/administracion')->assertForbidden();
        $this->actingAs($usuario)->get('/usuarios/create')->assertForbidden();
        $this->actingAs($usuario)->post('/usuarios', [
            'name' => 'Elevación de permisos',
            'email' => 'escalada@correo.com',
            'password' => 'clave-segura-123',
            'password_confirmation' => 'clave-segura-123',
            'rol' => 'administrador',
            'activo' => 1,
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'escalada@correo.com']);
    }

    public function test_administrador_puede_crear_y_desactivar_cuentas(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);

        $this->actingAs($admin)->get('/usuarios/create')->assertOk()->assertSee('Registrar usuario');
        $this->actingAs($admin)->post('/usuarios', [
            'name' => 'Nueva cuenta',
            'email' => 'nueva@correo.com',
            'password' => 'clave-segura-123',
            'password_confirmation' => 'clave-segura-123',
            'rol' => 'usuario',
            'activo' => 1,
        ])->assertRedirect(route('usuarios.index'));

        $cuenta = User::where('email', 'nueva@correo.com')->firstOrFail();
        $this->assertTrue(password_verify('clave-segura-123', $cuenta->password));
        $this->assertDatabaseHas('parcelas', [
            'usuario_id' => $cuenta->id,
            'nombre' => 'Parcela principal',
        ]);
        $this->actingAs($admin)->get('/usuarios')->assertOk()->assertSee('nueva@correo.com');
        $this->actingAs($admin)->get('/usuarios/'.$cuenta->id.'/edit')->assertOk()->assertSee('Editar usuario');

        $this->actingAs($admin)->patch('/usuarios/'.$cuenta->id.'/estado', ['activo' => 0])
            ->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('users', ['id' => $cuenta->id, 'activo' => false]);
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->create(['activo' => false, 'password' => 'clave-segura-123']);

        $this->post('/ingresar', ['email' => $usuario->email, 'password' => 'clave-segura-123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_admin_de_desarrollo_puede_usar_alias_solo_en_local(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@riego.test',
            'password' => 'admin',
            'rol' => 'administrador',
            'activo' => true,
        ]);

        $this->post('/ingresar', ['email' => 'admin', 'password' => 'admin'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->app->detectEnvironment(fn () => 'local');
        $this->post('/ingresar', ['email' => 'admin', 'password' => 'admin'])
            ->assertRedirect(route('inicio'));

        $this->assertAuthenticated();
    }

    public function test_cuenta_marcada_cambia_contrasena_antes_de_usar_el_sistema_en_produccion(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        $usuario = User::factory()->create([
            'password' => 'clave-temporal-123',
            'debe_cambiar_password' => true,
        ]);
        $this->app->detectEnvironment(fn () => 'production');

        $this->post('/ingresar', ['email' => $usuario->email, 'password' => 'clave-temporal-123'])
            ->assertRedirect(route('password.change'));
        $this->get('/riegos')->assertRedirect(route('password.change'));
        $this->get('/')->assertRedirect(route('password.change'));

        $this->put('/cuenta/cambiar-contrasena', [
            'current_password' => 'clave-temporal-123',
            'password' => 'una-clave-nueva-123',
            'password_confirmation' => 'una-clave-nueva-123',
        ])->assertRedirect(route('inicio'));

        $this->assertDatabaseHas('users', ['id' => $usuario->id, 'debe_cambiar_password' => false]);
        $this->assertTrue(password_verify('una-clave-nueva-123', $usuario->fresh()->password));
    }

    public function test_recuperacion_de_contrasena_envia_enlace_y_actualiza_la_clave(): void
    {
        $usuario = User::factory()->create();
        Notification::fake();

        $this->get('/recuperar-contrasena')->assertOk();
        $this->post('/recuperar-contrasena', ['email' => $usuario->email])
            ->assertSessionHas('ok');

        Notification::assertSentTo($usuario, ResetPassword::class);
        $notificacion = Notification::sent($usuario, ResetPassword::class)->first();
        $this->get('/restablecer-contrasena/'.$notificacion->token.'?email='.$usuario->email)
            ->assertOk()
            ->assertSee('Crear nueva contraseña');

        $this->post('/restablecer-contrasena', [
            'token' => $notificacion->token,
            'email' => $usuario->email,
            'password' => 'otra-clave-segura-123',
            'password_confirmation' => 'otra-clave-segura-123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(password_verify('otra-clave-segura-123', $usuario->fresh()->password));
    }

    public function test_el_seeder_crea_admin_hashed_solo_en_entorno_local(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        config(['app.admin_development_password' => 'clave-solo-para-prueba']);
        $this->seed();

        $admin = User::where('email', 'admin@riego.test')->firstOrFail();

        $this->assertSame('administrador', $admin->rol);
        $this->assertTrue(Hash::check('clave-solo-para-prueba', $admin->password));
        $this->assertTrue($admin->debe_cambiar_password);
    }

    public function test_el_seeder_no_crea_la_cuenta_por_defecto_en_produccion(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $this->app->make(DatabaseSeeder::class)->run();

        $this->assertDatabaseMissing('users', ['email' => 'admin@riego.test']);
        $this->assertDatabaseMissing('users', ['email' => 'usuario@riego.test']);
    }
}
