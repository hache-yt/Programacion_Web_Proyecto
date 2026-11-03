<?php

namespace App\Http\Controllers;

use App\Models\Riego;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstadisticaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        $dias = collect(range(13, 0))->map(fn ($atras) => now()->subDays($atras)->toDateString());

        $riegos = Riego::visiblesPara($usuario)
            ->where('fecha', '>=', $dias->first())
            ->get();

        $litrosPorDia = $dias->map(
            fn ($dia) => round($riegos->filter(fn ($r) => $r->fecha->toDateString() === $dia)->sum('cantidad_litros'), 2)
        );

        return view('estadisticas.index', [
            'dias' => $dias->values(),
            'litrosPorDia' => $litrosPorDia->values(),
            'totalLitros' => $riegos->sum('cantidad_litros'),
        ]);
    }
}
