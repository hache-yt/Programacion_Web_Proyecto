@extends('layouts.app')
@section('titulo', 'Cambiar contraseña')
@section('contenido')
<div class="page-heading"><div><p class="eyebrow">Seguridad de la cuenta</p><h1>Cambia tu contraseña</h1><p>Por seguridad, actualiza tu contraseña antes de continuar.</p></div></div>
<form class="panel formulario password-change-form" method="POST" action="{{ route('password.update') }}">
@csrf
@method('PUT')
<div class="campo"><label for="current_password">Contraseña actual</label><input type="password" id="current_password" name="current_password" required autocomplete="current-password"><small class="error" role="alert">{{ $errors->first('current_password') }}</small></div>
<div class="campo"><label for="password">Nueva contraseña <span>(mínimo 8 caracteres)</span></label><input type="password" id="password" name="password" required autocomplete="new-password"><small class="error" role="alert">{{ $errors->first('password') }}</small></div>
<div class="campo"><label for="password_confirmation">Confirmar nueva contraseña</label><input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
<button class="boton" type="submit">Actualizar contraseña</button>
</form>
@endsection
