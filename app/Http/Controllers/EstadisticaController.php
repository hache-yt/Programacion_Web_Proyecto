<?php

namespace App\Http\Controllers;

use App\Models\Medicion;
use App\Models\Riego;
use App\Models\SistemaRiego;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstadisticaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $filtros = $request->validate([
            'sistema_riego_id' => ['nullable', 'integer', 'exists:sistemas_riego,id'],
            'dias' => ['nullable', 'integer', 'in:7,30,90'],
        ]);
        $sistemas = SistemaRiego::visiblesPara($usuario)->orderBy('nombre')->get();
        $sistema = isset($filtros['sistema_riego_id'])
            ? $sistemas->firstWhere('id', (int) $filtros['sistema_riego_id'])
            : $sistemas->first();

        abort_if(isset($filtros['sistema_riego_id']) && ! $sistema, 404);

        $cantidadDias = (int) ($filtros['dias'] ?? 30);
        $dias = collect(range($cantidadDias - 1, 0))->map(fn ($atras) => now()->subDays($atras)->toDateString());

        $litrosAgrupados = Riego::visiblesPara($usuario)
            ->when($sistema, fn ($consulta) => $consulta->where('sistema_riego_id', $sistema->id))
            ->whereBetween('fecha', [$dias->first(), now()->endOfDay()])
            ->selectRaw('DATE(fecha) as dia, SUM(cantidad_litros) as total')
            ->groupByRaw('DATE(fecha)')
            ->pluck('total', 'dia');

        $litrosPorDia = $dias->map(
            fn (string $dia) => round((float) $litrosAgrupados->get($dia, 0), 2)
        )->values();

        $tiposLectura = ['humedad_suelo', 'temperatura', 'humedad_ambiental'];
        $promediosAgrupados = $sistema
            ? Medicion::query()
                ->where('sistema_riego_id', $sistema->id)
                ->whereIn('tipo', $tiposLectura)
                ->whereBetween('fecha_lectura', [$dias->first(), now()->endOfDay()])
                ->selectRaw('tipo, DATE(fecha_lectura) as dia, AVG(valor) as promedio')
                ->groupBy('tipo')
                ->groupByRaw('DATE(fecha_lectura)')
                ->get()
                ->groupBy('tipo')
                ->map(fn ($lecturas) => $lecturas->pluck('promedio', 'dia'))
            : collect();
        $lecturasPorTipo = collect($tiposLectura)->mapWithKeys(fn (string $tipo) => [
            $tipo => $dias->map(function (string $dia) use ($promediosAgrupados, $tipo) {
                $promedio = $promediosAgrupados->get($tipo)?->get($dia);

                return $promedio === null ? null : round((float) $promedio, 2);
            })->values(),
        ]);

        $comandos = $sistema
            ? $sistema->comandos()
                ->with('usuario')
                ->where('solicitado_en', '>=', $dias->first())
                ->latest('solicitado_en')
                ->limit(100)
                ->get()
            : collect();
        $riegosAsociados = $sistema
            ? $sistema->riegos()
                ->where('fecha', '>=', $dias->first())
                ->latest('fecha')
                ->limit(100)
                ->get()
            : collect();

        return view('estadisticas.index', [
            'dias' => $dias->values(),
            'litrosPorDia' => $litrosPorDia->values(),
            'totalLitros' => $litrosPorDia->sum(),
            'sistemas' => $sistemas,
            'sistema' => $sistema,
            'diasSeleccionados' => $cantidadDias,
            'lecturasPorTipo' => $lecturasPorTipo,
            'comandos' => $comandos,
            'riegosAsociados' => $riegosAsociados,
        ]);
    }
}
