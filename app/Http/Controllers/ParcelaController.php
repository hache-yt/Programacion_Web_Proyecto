<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParcelaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $parcelas = $usuario->esAdmin()
            ? Parcela::with('usuario')
                ->whereIn('id', Parcela::query()->selectRaw('MIN(id)')->groupBy('usuario_id'))
                ->orderBy('usuario_id')
                ->get()
            : collect([$usuario->parcelaPrincipal()]);

        return view('parcelas.index', [
            'parcelas' => $parcelas,
            'esAdmin' => $usuario->esAdmin(),
        ]);
    }
}
