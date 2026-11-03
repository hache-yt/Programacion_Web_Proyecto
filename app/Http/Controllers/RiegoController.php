<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiegoRequest;
use App\Models\Parcela;
use App\Models\Riego;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiegoController extends Controller
{
    private function parcelasDe(Request $request)
    {
        return Parcela::visiblesPara($request->user())->orderBy('nombre')->get();
    }

    public function index(Request $request): View
    {
        $filtros = $request->only(['fecha', 'parcela_id']);

        $riegos = Riego::visiblesPara($request->user())
            ->with('parcela')
            ->filtrar($filtros)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('riegos.index', [
            'riegos' => $riegos,
            'parcelas' => $this->parcelasDe($request),
            'filtros' => $filtros,
        ]);
    }

    public function create(Request $request): View
    {
        return view('riegos.form', [
            'riego' => new Riego(['fecha' => now()->toDateString()]),
            'parcelas' => $this->parcelasDe($request),
        ]);
    }

    public function store(RiegoRequest $request): RedirectResponse
    {
        $riego = Riego::create($request->validated());
        $riego->generarAlerta();

        return redirect()->route('riegos.index')->with('ok', 'Riego guardado correctamente.');
    }

    public function edit(Request $request, Riego $riego): View
    {
        abort_unless($riego->perteneceA($request->user()), 403);

        return view('riegos.form', [
            'riego' => $riego,
            'parcelas' => $this->parcelasDe($request),
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
