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
        $alertas = Alerta::visiblesPara($request->user())
            ->with('riego.parcela')
            ->orderByRaw("estado = 'pendiente' desc")
            ->orderByDesc('id')
            ->get();

        return view('alertas.index', ['alertas' => $alertas]);
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
