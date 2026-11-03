@extends('layouts.app')
@section('titulo', 'Alertas')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Atención al consumo</p><h1>Alertas</h1><p>Revisa los registros que superaron el umbral de consumo.</p></div>
</div>
<div class="panel tabla-panel">
<div class="tabla-scroll">
<table>
<thead><tr><th>Fecha</th><th>Detalle</th><th>Estado</th><th>Acciones</th></tr></thead>
<tbody>
@forelse ($alertas as $alerta)
<tr>
<td><strong>{{ $alerta->created_at->format('d/m/Y') }}</strong><br><span class="tarjeta-detalle">{{ $alerta->created_at->format('H:i') }}</span></td>
<td>{{ $alerta->mensaje }}</td>
<td><span @class(['etiqueta', 'etiqueta-pendiente' => $alerta->estado === 'pendiente', 'etiqueta-atendida' => $alerta->estado !== 'pendiente'])>{{ ucfirst($alerta->estado) }}</span></td>
<td class="acciones">
@if ($alerta->estado === 'pendiente')
<form method="POST" action="{{ route('alertas.update', $alerta) }}">
@csrf
@method('PATCH')
<button class="enlace" type="submit">Marcar atendida</button>
</form>
@endif
<form method="POST" action="{{ route('alertas.destroy', $alerta) }}" data-confirm="¿Eliminar esta alerta?" data-confirm-title="Eliminar alerta">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
</td>
</tr>
@empty
<tr><td class="vacio" colspan="4"><span class="vacio-icono" aria-hidden="true">✓</span><strong>No hay alertas registradas</strong>Cuando haya un consumo elevado, lo verás aquí.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
@endsection
