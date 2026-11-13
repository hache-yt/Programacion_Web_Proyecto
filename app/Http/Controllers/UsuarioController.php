<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:todos,activos,inactivos'],
        ]);

        return view('usuarios.index', [
            'usuarios' => User::withCount('parcelas')
                ->when($filtros['q'] ?? null, fn ($consulta, $busqueda) => $consulta->where(
                    fn ($q) => $q->where('name', 'like', "%{$busqueda}%")
                        ->orWhere('email', 'like', "%{$busqueda}%")
                ))
                ->when(($filtros['estado'] ?? 'todos') !== 'todos', fn ($consulta) => $consulta->where(
                    'activo',
                    ($filtros['estado'] ?? 'todos') === 'activos'
                ))
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
            'filtros' => array_merge(['estado' => 'todos'], $filtros),
        ]);
    }

    public function create(): View
    {
        return view('usuarios.form', ['usuario' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'in:usuario,administrador'],
            'activo' => ['required', 'boolean'],
        ]);

        $usuario = User::create($datos);
        $usuario->parcelaPrincipal();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.form', ['usuario' => $usuario]);
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$usuario->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'in:usuario,administrador'],
            'activo' => ['required', 'boolean'],
        ]);

        if ($usuario->is($request->user()) && ($datos['rol'] !== $usuario->rol || ! $datos['activo'])) {
            return back()->withErrors(['usuario' => 'No puedes cambiar tu propio rol ni desactivar tu cuenta.']);
        }

        if (
            $usuario->esAdmin()
            && $usuario->activo
            && ($datos['rol'] !== 'administrador' || ! $datos['activo'])
            && User::where('rol', 'administrador')->where('activo', true)->count() <= 1
        ) {
            return back()->withErrors(['usuario' => 'Debe permanecer al menos un administrador activo.']);
        }

        if (empty($datos['password'])) {
            unset($datos['password']);
        } else {
            $datos['password'] = Hash::make($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado correctamente.');
    }

    public function estado(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate(['activo' => ['required', 'boolean']]);
        $activo = (bool) $datos['activo'];

        if ($usuario->is($request->user()) && ! $activo) {
            return back()->withErrors(['usuario' => 'No puedes desactivar tu propia cuenta.']);
        }

        if (
            $usuario->esAdmin()
            && $usuario->activo
            && ! $activo
            && User::where('rol', 'administrador')->where('activo', true)->count() <= 1
        ) {
            return back()->withErrors(['usuario' => 'Debe permanecer al menos un administrador activo.']);
        }

        $usuario->update(['activo' => $activo]);

        return redirect()->route('usuarios.index')->with('ok', 'Estado de la cuenta actualizado.');
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return redirect()->route('usuarios.index')->withErrors(['usuario' => 'No puedes eliminar tu propia cuenta.']);
        }

        if (
            $usuario->esAdmin()
            && $usuario->activo
            && User::where('rol', 'administrador')->where('activo', true)->count() <= 1
        ) {
            return redirect()->route('usuarios.index')->withErrors(['usuario' => 'Debe permanecer al menos un administrador activo.']);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario eliminado correctamente.');
    }
}
