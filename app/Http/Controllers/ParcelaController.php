<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParcelaRequest;
use App\Models\Parcela;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParcelaController extends Controller
{
    public function index(Request $request): View
    {
        return view('parcelas.index', [
            'parcelas' => Parcela::visiblesPara($request->user())->orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('parcelas.form', ['parcela' => new Parcela]);
    }

    public function store(ParcelaRequest $request): RedirectResponse
    {
        $request->user()->parcelas()->create($request->validated());

        return redirect()->route('parcelas.index')->with('ok', 'Parcela creada correctamente.');
    }

    public function edit(Request $request, Parcela $parcela): View
    {
        abort_unless($parcela->perteneceA($request->user()), 403);

        return view('parcelas.form', ['parcela' => $parcela]);
    }

    public function update(ParcelaRequest $request, Parcela $parcela): RedirectResponse
    {
        abort_unless($parcela->perteneceA($request->user()), 403);

        $parcela->update($request->validated());

        return redirect()->route('parcelas.index')->with('ok', 'Parcela actualizada correctamente.');
    }

    public function destroy(Request $request, Parcela $parcela): RedirectResponse
    {
        abort_unless($parcela->perteneceA($request->user()), 403);

        $parcela->delete();

        return redirect()->route('parcelas.index')->with('ok', 'Parcela eliminada correctamente.');
    }
}
