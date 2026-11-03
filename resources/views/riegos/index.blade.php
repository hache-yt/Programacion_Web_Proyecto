@extends('layouts.app')
@section('titulo', 'Historial')
@section('contenido')
<h1>Historial de riegos</h1>
<form class="filtros" method="GET" action="{{ route('riegos.index') }}">
<div class="campo">
<label for="filtro-fecha">Fecha</label>
<input type="date" id="filtro-fecha" name="fecha" value="{{ $filtros['fecha'] ?? '' }}">
</div>
<div class="campo">
<label for="filtro-parcela">Parcela</label>
<select id="filtro-parcela" name="parcela_id">
<option value="">Todas</option>
@foreach ($parcelas as $parcela)
<option value="{{ $parcela->id }}" @selected(($filtros['parcela_id'] ?? '') == $parcela->id)>{{ $parcela->nombre }}</option>
@endforeach
</select>
</div>
<button class="boton" type="submit">Filtrar</button>
<a class="enlace" href="{{ route('riegos.index') }}">Limpiar</a>
</form>
<div class="tabla-scroll">
<table>
<thead>
<tr>
<th>Fecha</th>
<th>Parcela</th>
<th>Duración (min)</th>
<th>Litros</th>
<th>Observaciones</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@forelse ($riegos as $riego)
<tr>
<td>{{ $riego->fecha->format('Y-m-d') }}</td>
<td>{{ $riego->parcela->nombre }}</td>
<td>{{ $riego->duracion_min }}</td>
<td @class(['alto' => $riego->esElevado()])>{{ number_format($riego->cantidad_litros, 2) }}@if ($riego->esElevado()) <span class="etiqueta">Consumo elevado</span>@endif</td>
<td>{{ $riego->observaciones }}</td>
<td class="acciones">
<a class="enlace" href="{{ route('riegos.edit', $riego) }}">Editar</a>
<form method="POST" action="{{ route('riegos.destroy', $riego) }}" onsubmit="return confirm('¿Eliminar este riego?')">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="6">No hay riegos registrados.</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $riegos->links() }}
@endsection
