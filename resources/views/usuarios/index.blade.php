@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Administración</p><h1>Usuarios</h1><p>Consulta las cuentas y el acceso de cada persona al sistema.</p></div>
</div>
<div class="panel tabla-panel">
<div class="tabla-scroll">
<table>
<thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Parcela principal</th><th>Registro</th><th>Acciones</th></tr></thead>
<tbody>
@forelse ($usuarios as $usuario)
<tr>
<td><strong>{{ $usuario->name }}</strong></td>
<td>{{ $usuario->email }}</td>
<td><span class="etiqueta">{{ ucfirst($usuario->rol) }}</span></td>
<td><span @class(['etiqueta', 'etiqueta-ok' => $usuario->parcelas_count > 0, 'etiqueta-pendiente' => $usuario->parcelas_count === 0])>{{ $usuario->parcelas_count > 0 ? 'Configurada' : 'Pendiente' }}</span></td>
<td>{{ $usuario->created_at->format('d/m/Y') }}</td>
<td class="acciones">
@unless ($usuario->is(auth()->user()))
<form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" data-confirm="Se eliminarán la cuenta y sus datos asociados. ¿Deseas continuar?" data-confirm-title="Eliminar usuario">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
@endunless
</td>
</tr>
@empty
<tr><td class="vacio" colspan="6"><strong>No hay usuarios registrados</strong></td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
@endsection
