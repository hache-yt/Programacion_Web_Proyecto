@extends('layouts.app')
@section('titulo', 'Historial')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Seguimiento</p><h1>Historial de riegos</h1><p>Consulta y administra tus registros de consumo de agua.</p></div>
<a class="boton" href="{{ route('riegos.create') }}">+ Registrar riego</a>
</div>
<form class="filtros" method="GET" action="{{ route('riegos.index') }}">
<div class="campo">
<label for="filtro-fecha">Filtrar por fecha</label>
<input type="date" id="filtro-fecha" name="fecha" value="{{ $filtros['fecha'] ?? '' }}">
</div>
<button class="boton" type="submit">Aplicar filtro</button>
<a class="boton boton-secundario" href="{{ route('riegos.index') }}">Limpiar</a>
</form>
<div class="panel tabla-panel">
<div class="tabla-scroll">
<table>
<thead><tr><th>Fecha</th><th>Duración</th><th>Agua utilizada</th><th>Observaciones</th><th>Acciones</th></tr></thead>
<tbody>
@forelse ($riegos as $riego)
<tr>
<td><strong>{{ $riego->fecha->format('d/m/Y') }}</strong></td>
<td>{{ $riego->duracion_min }} min</td>
<td @class(['alto' => $riego->esElevado()])>{{ number_format($riego->cantidad_litros, 2) }} L @if ($riego->esElevado()) <span class="etiqueta etiqueta-pendiente">Consumo elevado</span>@endif</td>
<td>{{ $riego->observaciones ?: '—' }}</td>
<td class="acciones">
<a class="enlace" href="{{ route('riegos.edit', $riego) }}">Editar</a>
<form method="POST" action="{{ route('riegos.destroy', $riego) }}" data-confirm="¿Eliminar este registro de riego?" data-confirm-title="Eliminar riego">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
</td>
</tr>
@empty
<tr><td class="vacio" colspan="5"><span class="vacio-icono" aria-hidden="true">◌</span><strong>Aún no hay riegos registrados</strong>Registra tu primer riego para comenzar a seguir el consumo.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
{{ $riegos->links() }}
@endsection
