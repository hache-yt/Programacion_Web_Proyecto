<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_inicio_es_publico(): void
    {
        $this->get('/')->assertOk()->assertSee('Sabe cuánta agua usas al regar');
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
        $this->actingAs(User::factory()->create(['rol' => 'administrador']))->get('/usuarios')->assertOk();
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
}
