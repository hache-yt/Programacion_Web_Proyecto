@extends('layouts.app')
@section('titulo', 'Alertas')
@section('contenido')
<h1>Alertas de consumo elevado</h1>
<div class="tabla-scroll">
<table>
<thead>
<tr>
<th>Fecha</th>
<th>Mensaje</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@forelse ($alertas as $alerta)
<tr>
<td>{{ $alerta->created_at->format('Y-m-d H:i') }}</td>
<td>{{ $alerta->mensaje }}</td>
<td @class(['alto' => $alerta->estado === 'pendiente'])>{{ ucfirst($alerta->estado) }}</td>
<td class="acciones">
@if ($alerta->estado === 'pendiente')
<form method="POST" action="{{ route('alertas.update', $alerta) }}">
@csrf
@method('PATCH')
<button class="enlace" type="submit">Marcar atendida</button>
</form>
@endif
<form method="POST" action="{{ route('alertas.destroy', $alerta) }}" onsubmit="return confirm('¿Eliminar esta alerta?')">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="4">No hay alertas registradas.</td></tr>
@endforelse
</tbody>
</table>
</div>
@endsection
