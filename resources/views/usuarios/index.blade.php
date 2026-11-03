@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('contenido')
<h1>Usuarios</h1>
<div class="tabla-scroll">
<table>
<thead>
<tr>
<th>Nombre</th>
<th>Correo</th>
<th>Rol</th>
<th>Parcelas</th>
<th>Registro</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@foreach ($usuarios as $usuario)
<tr>
<td>{{ $usuario->name }}</td>
<td>{{ $usuario->email }}</td>
<td>{{ ucfirst($usuario->rol) }}</td>
<td>{{ $usuario->parcelas_count }}</td>
<td>{{ $usuario->created_at->format('Y-m-d') }}</td>
<td class="acciones">
@unless ($usuario->is(auth()->user()))
<form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" onsubmit="return confirm('Se eliminarán sus parcelas y riegos. ¿Continuar?')">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
@endunless
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
