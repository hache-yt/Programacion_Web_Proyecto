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
        $filtros = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $parcelas = $usuario->esAdmin()
            ? Parcela::with('usuario')
                ->whereIn('id', Parcela::query()->selectRaw('MIN(id)')->groupBy('usuario_id'))
                ->orderBy('usuario_id')
                ->when($filtros['q'] ?? null, fn ($consulta, $busqueda) => $consulta->whereHas(
                    'usuario',
                    fn ($q) => $q->where('name', 'like', "%{$busqueda}%")->orWhere('email', 'like', "%{$busqueda}%")
                ))
                ->paginate(15)
                ->withQueryString()
            : collect([$usuario->parcelaPrincipal()]);

        return view('parcelas.index', [
            'parcelas' => $parcelas,
            'esAdmin' => $usuario->esAdmin(),
            'filtros' => $filtros,
        ]);
    }
}
