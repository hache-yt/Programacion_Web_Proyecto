@extends('layouts.app')
@section('titulo', 'Estadísticas')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Lecturas e historial</p><h1>Estadísticas</h1><p>Consulta datos registrados; no hay lecturas inventadas ni recibidas de hardware en esta versión.</p></div>
</div>
<form class="filtros" method="GET" action="{{ route('estadisticas.index') }}">
<div class="campo"><label for="sistema_riego_id">Sistema</label><select id="sistema_riego_id" name="sistema_riego_id"><option value="">Selecciona un sistema</option>@foreach ($sistemas as $opcion)<option value="{{ $opcion->id }}" @selected($sistema?->id === $opcion->id)>{{ $opcion->nombre }}</option>@endforeach</select></div>
<div class="campo"><label for="dias">Periodo</label><select id="dias" name="dias"><option value="7" @selected($diasSeleccionados === 7)>Últimos 7 días</option><option value="30" @selected($diasSeleccionados === 30)>Últimos 30 días</option><option value="90" @selected($diasSeleccionados === 90)>Últimos 90 días</option></select></div>
<button class="boton" type="submit">Filtrar</button>
</form>
<section class="graficos">
<article class="panel">
<div class="grafico-cabecera">
<div><h2>Consumo diario · histórico registrado</h2><p>{{ $sistema ? 'Litros asociados a '.$sistema->nombre : 'Registros históricos sin filtro por sistema' }}</p></div>
<span class="grafico-total">{{ number_format($totalLitros, 2) }} L</span>
</div>
<div class="grafico-contenedor">
<canvas id="grafico-dias" data-etiquetas="{{ json_encode($dias) }}" data-valores="{{ json_encode($litrosPorDia) }}" role="img" aria-label="Gráfico de litros utilizados por día"></canvas>
</div>
</article>
@foreach ([
    'humedad_suelo' => ['Humedad del suelo', '%'],
    'temperatura' => ['Temperatura ambiente', '°C'],
    'humedad_ambiental' => ['Humedad ambiental', '%'],
] as $tipo => [$titulo, $unidad])
<article class="panel">
<div class="grafico-cabecera"><div><h2>{{ $titulo }}</h2><p>Promedio de lecturas guardadas por día</p></div><span class="grafico-total">{{ $unidad }}</span></div>
@if ($lecturasPorTipo[$tipo]->filter(fn ($valor) => $valor !== null)->isNotEmpty())
<div class="grafico-contenedor"><canvas class="metric-chart" data-titulo="{{ $titulo }}" data-unidad="{{ $unidad }}" data-etiquetas="{{ json_encode($dias) }}" data-valores="{{ json_encode($lecturasPorTipo[$tipo]) }}" role="img" aria-label="Gráfico de {{ $titulo }} para el periodo seleccionado"></canvas></div>
@else
<p class="empty-note">Sin lecturas guardadas de este tipo durante el periodo seleccionado.</p>
@endif
</article>
@endforeach
</section>
<section class="panel">
<div class="panel-heading"><h2>Historial de riego y cambios</h2><p class="panel-copy">Las solicitudes sin conexión no representan riego ejecutado. Los registros históricos se asocian a un sistema solo cuando se eligió expresamente al crearlos.</p></div>
<div class="table-section-heading"><h3>Riegos registrados</h3></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha</th><th>Duración</th><th>Agua utilizada</th><th>Observaciones</th></tr></thead><tbody>
@forelse ($riegosAsociados as $riego)
<tr><td>{{ $riego->fecha->format('d/m/Y') }}</td><td>{{ $riego->duracion_min }} min</td><td>{{ number_format($riego->cantidad_litros, 2) }} L</td><td>{{ $riego->observaciones ?: '—' }}</td></tr>
@empty
<tr><td class="vacio" colspan="4"><strong>Sin riegos asociados a este sistema</strong>Los registros anteriores no se asignan automáticamente para evitar atribuciones incorrectas.</td></tr>
@endforelse
</tbody></table></div>
<div class="table-section-heading"><h3>Órdenes y cambios</h3></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha</th><th>Acción</th><th>Usuario</th><th>Resultado</th><th>Detalle</th></tr></thead><tbody>
@forelse ($comandos as $comando)
<tr><td>{{ $comando->solicitado_en->format('d/m/Y H:i') }}</td><td>{{ str_replace('_', ' ', ucfirst($comando->accion)) }}</td><td>{{ $comando->usuario?->name ?? 'Usuario no disponible' }}</td><td>{{ str_replace('_', ' ', $comando->resultado) }}</td><td>{{ $comando->detalle }}</td></tr>
@empty
<tr><td class="vacio" colspan="5"><strong>Sin órdenes ni cambios en el periodo</strong>No se registraron acciones asociadas a este sistema.</td></tr>
@endforelse
</tbody></table></div>
</section>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/graficos.js') }}"></script>
@endpush
