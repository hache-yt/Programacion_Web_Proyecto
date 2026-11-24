<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Riego;
use App\Models\SistemaRiego;
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
        $litrosAgrupados = Riego::visiblesPara($usuario)
            ->whereBetween('fecha', [$dias->first(), now()->endOfDay()])
            ->selectRaw('DATE(fecha) as dia, SUM(cantidad_litros) as total')
            ->groupByRaw('DATE(fecha)')
            ->pluck('total', 'dia');
        $riegosRecientes = Riego::visiblesPara($usuario)
            ->with('parcela')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(5)
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
                fn (string $dia) => round((float) $litrosAgrupados->get($dia, 0), 2)
            )->values(),
            'riegosRecientes' => $riegosRecientes,
            'sistemas' => SistemaRiego::visiblesPara($usuario)
                ->with(['parcela.usuario', 'configuracion', 'ultimaMedicion'])
                ->orderBy('nombre')
                ->get(),
        ]);
    }
}
