<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiegoRequest;
use App\Models\Riego;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiegoController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only(['fecha']);

        $riegos = Riego::visiblesPara($request->user())
            ->with('parcela')
            ->filtrar($filtros)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('riegos.index', [
            'riegos' => $riegos,
            'filtros' => $filtros,
        ]);
    }

    public function create(): View
    {
        return view('riegos.form', [
            'riego' => new Riego(['fecha' => now()->toDateString()]),
        ]);
    }

    public function store(RiegoRequest $request): RedirectResponse
    {
        $riego = $request->user()->parcelaPrincipal()->riegos()->create($request->validated());
        $riego->generarAlerta();

        return redirect()->route('riegos.index')->with('ok', 'Riego guardado correctamente.');
    }

    public function edit(Request $request, Riego $riego): View
    {
        abort_unless($riego->perteneceA($request->user()), 403);

        return view('riegos.form', [
            'riego' => $riego,
        ]);
    }

    public function update(RiegoRequest $request, Riego $riego): RedirectResponse
    {
        abort_unless($riego->perteneceA($request->user()), 403);

        $riego->update($request->validated());
        $riego->generarAlerta();

        return redirect()->route('riegos.index')->with('ok', 'Riego actualizado correctamente.');
    }

    public function destroy(Request $request, Riego $riego): RedirectResponse
    {
        abort_unless($riego->perteneceA($request->user()), 403);

        $riego->delete();

        return redirect()->route('riegos.index')->with('ok', 'Riego eliminado correctamente.');
    }
}
