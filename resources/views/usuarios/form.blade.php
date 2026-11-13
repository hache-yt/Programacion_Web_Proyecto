@extends('layouts.app')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Registrar usuario')
@section('contenido')
<div class="page-heading"><div><p class="eyebrow">Administración de cuentas</p><h1>{{ $usuario->exists ? 'Editar usuario' : 'Registrar usuario' }}</h1><p>Los permisos y el estado de cada cuenta se verifican en el servidor.</p></div></div>
<form class="panel formulario admin-user-form" method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}">
@csrf
@if ($usuario->exists)
@method('PUT')
@endif
<div class="form-grid">
<div class="campo"><label for="name">Nombre completo</label><input id="name" name="name" value="{{ old('name', $usuario->name) }}" maxlength="100" required aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"><small class="error" role="alert">{{ $errors->first('name') }}</small></div>
<div class="campo"><label for="email">Correo electrónico</label><input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" maxlength="255" required aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"><small class="error" role="alert">{{ $errors->first('email') }}</small></div>
<div class="campo"><label for="password">Contraseña @if ($usuario->exists)<span>(dejar en blanco para conservarla)</span>@else<span>(mínimo 8 caracteres)</span>@endif</label><input type="password" id="password" name="password" {{ $usuario->exists ? '' : 'required' }} autocomplete="new-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"><small class="error" role="alert">{{ $errors->first('password') }}</small></div>
<div class="campo"><label for="password_confirmation">Confirmar contraseña</label><input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"></div>
<div class="campo"><label for="rol">Rol</label><select id="rol" name="rol" required @disabled($usuario->is(auth()->user()))><option value="usuario" @selected(old('rol', $usuario->rol ?: 'usuario') === 'usuario')>Usuario</option><option value="administrador" @selected(old('rol', $usuario->rol) === 'administrador')>Administrador</option></select>@if ($usuario->is(auth()->user()))<input type="hidden" name="rol" value="{{ $usuario->rol }}">@endif<small class="error" role="alert">{{ $errors->first('rol') }}</small></div>
<div class="campo"><label for="activo">Estado de la cuenta</label><select id="activo" name="activo" required @disabled($usuario->is(auth()->user()))><option value="1" @selected((string) old('activo', $usuario->exists ? (int) $usuario->activo : 1) === '1')>Activa</option><option value="0" @selected((string) old('activo', $usuario->exists ? (int) $usuario->activo : 1) === '0')>Inactiva</option></select>@if ($usuario->is(auth()->user()))<input type="hidden" name="activo" value="1">@endif<small class="error" role="alert">{{ $errors->first('activo') }}</small></div>
</div>
@if ($errors->has('usuario'))<p class="aviso aviso-error" role="alert">{{ $errors->first('usuario') }}</p>@endif
<div class="form-acciones"><button class="boton" type="submit">{{ $usuario->exists ? 'Guardar cambios' : 'Crear usuario' }}</button><a class="boton boton-secundario" href="{{ route('usuarios.index') }}">Cancelar</a></div>
</form>
@endsection
