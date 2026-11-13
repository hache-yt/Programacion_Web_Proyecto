<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlertaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'in:todos,pendiente,atendida'],
        ]);

        $alertas = Alerta::visiblesPara($request->user())
            ->with('riego.parcela')
            ->when($filtros['q'] ?? null, fn ($consulta, $busqueda) => $consulta->where('mensaje', 'like', "%{$busqueda}%"))
            ->when(($filtros['estado'] ?? 'todos') !== 'todos', fn ($consulta) => $consulta->where('estado', $filtros['estado']))
            ->orderByRaw("estado = 'pendiente' desc")
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('alertas.index', [
            'alertas' => $alertas,
            'filtros' => array_merge(['estado' => 'todos'], $filtros),
        ]);
    }

    public function update(Request $request, Alerta $alerta): RedirectResponse
    {
        abort_unless($alerta->riego->perteneceA($request->user()), 403);

        $alerta->update(['estado' => 'atendida']);

        return redirect()->route('alertas.index')->with('ok', 'Alerta marcada como atendida.');
    }

    public function destroy(Request $request, Alerta $alerta): RedirectResponse
    {
        abort_unless($alerta->riego->perteneceA($request->user()), 403);

        $alerta->delete();

        return redirect()->route('alertas.index')->with('ok', 'Alerta eliminada correctamente.');
    }
}
