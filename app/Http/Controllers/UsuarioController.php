<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->esAdmin(), 403);

        return view('usuarios.index', [
            'usuarios' => User::withCount('parcelas')->orderBy('name')->get(),
        ]);
    }

    public function destroy(Request $request, User $usuario): RedirectResponse
    {
        abort_unless($request->user()->esAdmin(), 403);

        if ($usuario->is($request->user())) {
            return redirect()->route('usuarios.index')->withErrors(['usuario' => 'No puedes eliminar tu propia cuenta.']);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario eliminado correctamente.');
    }
}
