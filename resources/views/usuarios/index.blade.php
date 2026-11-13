@extends('layouts.app')
@section('titulo', 'Usuarios')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Administración</p><h1>Usuarios</h1><p>Gestiona cuentas, permisos y acceso a la plataforma.</p></div>
<a class="boton" href="{{ route('usuarios.create') }}">+ Registrar usuario</a>
</div>
<form class="filtros user-filters" method="GET" action="{{ route('usuarios.index') }}">
<div class="campo"><label for="q">Buscar usuario</label><input id="q" name="q" value="{{ $filtros['q'] ?? '' }}" placeholder="Nombre o correo electrónico"></div>
<div class="campo"><label for="estado">Estado</label><select id="estado" name="estado"><option value="todos" @selected(($filtros['estado'] ?? 'todos') === 'todos')>Todos</option><option value="activos" @selected(($filtros['estado'] ?? '') === 'activos')>Activos</option><option value="inactivos" @selected(($filtros['estado'] ?? '') === 'inactivos')>Inactivos</option></select></div>
<button class="boton" type="submit">Buscar</button>
<a class="boton boton-secundario" href="{{ route('usuarios.index') }}">Limpiar</a>
</form>
@if ($errors->has('usuario'))<p class="aviso aviso-error" role="alert">{{ $errors->first('usuario') }}</p>@endif
<div class="panel tabla-panel">
<div class="tabla-scroll">
<table>
<thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Parcela principal</th><th>Registro</th><th>Acciones</th></tr></thead>
<tbody>
@forelse ($usuarios as $usuario)
<tr>
<td><strong>{{ $usuario->name }}</strong></td>
<td>{{ $usuario->email }}</td>
<td><span class="etiqueta">{{ ucfirst($usuario->rol) }}</span></td>
<td><span @class(['etiqueta', 'etiqueta-ok' => $usuario->activo, 'etiqueta-pendiente' => ! $usuario->activo])>{{ $usuario->activo ? 'Activa' : 'Inactiva' }}</span></td>
<td><span @class(['etiqueta', 'etiqueta-ok' => $usuario->parcelas_count > 0, 'etiqueta-pendiente' => $usuario->parcelas_count === 0])>{{ $usuario->parcelas_count > 0 ? 'Configurada' : 'Pendiente' }}</span></td>
<td>{{ $usuario->created_at->format('d/m/Y') }}</td>
<td class="acciones">
<a class="enlace" href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
@unless ($usuario->is(auth()->user()))
<form method="POST" action="{{ route('usuarios.estado', $usuario) }}">
@csrf
@method('PATCH')
<input type="hidden" name="activo" value="{{ $usuario->activo ? 0 : 1 }}">
<button class="enlace" type="submit">{{ $usuario->activo ? 'Desactivar' : 'Activar' }}</button>
</form>
<form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" data-confirm="Se eliminarán la cuenta y sus datos asociados. ¿Deseas continuar?" data-confirm-title="Eliminar usuario">
@csrf
@method('DELETE')
<button class="enlace" type="submit">Eliminar</button>
</form>
@endunless
</td>
</tr>
@empty
<tr><td class="vacio" colspan="7"><strong>No hay usuarios que coincidan</strong>Ajusta los filtros o registra una cuenta nueva.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
{{ $usuarios->links() }}
@endsection
