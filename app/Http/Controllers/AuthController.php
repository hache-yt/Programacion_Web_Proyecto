<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function formLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ], [
            'email.required' => 'Ingresa tu correo o usuario.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $identificador = trim($datos['email']);
        if ($identificador === 'admin' && app()->environment('local')) {
            $identificador = 'admin@riego.test';
        }

        if (! Auth::attempt(['email' => $identificador, 'password' => $datos['password'], 'activo' => true], $request->boolean('recordar'))) {
            return back()->withErrors(['email' => 'Usuario, correo o contraseña incorrectos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        if (app()->environment('production') && $request->user()->debe_cambiar_password) {
            return redirect()->route('password.change');
        }

        return redirect()->intended(route('inicio'));
    }

    public function formRegistro(): View
    {
        return view('auth.registro');
    }

    public function registro(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Ingresa tu nombre.',
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'Ingresa una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario = User::create($datos + ['rol' => 'usuario']);
        $usuario->parcelaPrincipal();

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('inicio')->with('ok', 'Cuenta creada correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }

    public function formCambiarPassword(): View
    {
        return view('auth.cambiar-password');
    }

    public function cambiarPassword(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $request->user()->update([
            'password' => Hash::make($datos['password']),
            'debe_cambiar_password' => false,
        ]);

        return redirect()->route('inicio')->with('ok', 'Tu contraseña se actualizó correctamente.');
    }

    public function formRecuperarPassword(): View
    {
        return view('auth.recuperar-password');
    }

    public function enviarEnlaceRecuperacion(Request $request): RedirectResponse
    {
        $datos = $request->validate(['email' => ['required', 'email']]);
        $estado = Password::sendResetLink($datos);

        if ($estado === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'Ya se solicitó un enlace recientemente. Espera un momento antes de intentarlo de nuevo.',
            ]);
        }

        return back()->with('ok', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.');
    }

    public function formRestablecerPassword(string $token): View
    {
        return view('auth.restablecer-password', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    }

    public function restablecerPassword(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $estado = Password::reset($datos, function (User $usuario, string $password): void {
            $usuario->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
                'debe_cambiar_password' => false,
            ])->save();
        });

        if ($estado !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => 'El enlace no es válido o ya venció. Solicita uno nuevo.',
            ]);
        }

        return redirect()->route('login')->with('ok', 'Contraseña restablecida. Ya puedes iniciar sesión.');
    }
}
