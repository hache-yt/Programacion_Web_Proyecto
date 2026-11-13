<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Riego;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        if (! $usuario) {
            return view('inicio', ['resumen' => null]);
        }

        $parcela = $usuario->parcelaPrincipal();
        $dias = collect(range(13, 0))->map(fn ($atras) => now()->subDays($atras)->toDateString());
        $riegosRecientes = Riego::visiblesPara($usuario)
            ->where('fecha', '>=', $dias->first())
            ->get();

        return view('inicio', [
            'resumen' => [
                'parcela' => $parcela->nombre,
                'riegos' => Riego::visiblesPara($usuario)->count(),
                'litrosSemana' => Riego::visiblesPara($usuario)
                    ->where('fecha', '>=', now()->subDays(6)->toDateString())
                    ->sum('cantidad_litros'),
                'alertas' => Alerta::visiblesPara($usuario)->where('estado', 'pendiente')->count(),
            ],
            'dias' => $dias->values(),
            'litrosPorDia' => $dias->map(
                fn ($dia) => round($riegosRecientes->filter(fn ($riego) => $riego->fecha->toDateString() === $dia)->sum('cantidad_litros'), 2)
            )->values(),
            'riegosRecientes' => $riegosRecientes->sortByDesc('fecha')->take(5)->values(),
        ]);
    }
}
