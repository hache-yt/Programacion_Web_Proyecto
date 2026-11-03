@extends('layouts.app')
@section('titulo', 'Parcelas')
@section('contenido')
<h1>Mis parcelas</h1>
<div class="tabla-scroll">
<table>
<thead>
<tr>
<th>Nombre</th>
<th>Ubicación</th>
<th>Área (m²)</th>
<th>Cultivo</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@forelse ($parcelas as $parcela)
<tr>
<td>{{ $parcela->nombre }}</td>
<td>{{ $parcela->ubicacion }}</td>
<td>{{ $parcela->area_m2 }}</td>
<td>{{ $parcela->tipo_cultivo }}</td>
<td class="acciones">
<a class="enlace" href="{{ route('parcelas.edit', $parcela) }}">Editar</a>
<form method="POST" action="{{ route('parcelas.destroy', $parcela) }}" onsubmit="return confirm('Se eliminarán también sus riegos. ¿Continuar?')">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
</td>
</tr>
@empty
<tr><td colspan="5">No hay parcelas registradas.</td></tr>
@endforelse
</tbody>
</table>
</div>
<a class="boton" href="{{ route('parcelas.create') }}">Nueva parcela</a>
@endsection
