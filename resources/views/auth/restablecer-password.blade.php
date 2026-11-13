@extends('layouts.app')
@section('titulo', 'Nueva contraseña')
@section('contenido')
<section class="auth-card">
<div class="auth-heading"><p class="eyebrow">Seguridad de la cuenta</p><h1>Crear nueva contraseña</h1><p>Elige una contraseña segura de al menos 8 caracteres.</p></div>
<form class="auth-form formulario" method="POST" action="{{ route('password.reset.submit') }}">
@csrf
<input type="hidden" name="token" value="{{ $token }}">
<div class="campo"><label for="email">Correo electrónico</label><input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"><small class="error" role="alert">{{ $errors->first('email') }}</small></div>
<div class="campo"><label for="password">Nueva contraseña</label><input type="password" id="password" name="password" required autocomplete="new-password"><small class="error" role="alert">{{ $errors->first('password') }}</small></div>
<div class="campo"><label for="password_confirmation">Confirmar contraseña</label><input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
<button class="boton auth-submit" type="submit">Actualizar contraseña</button>
</form>
</section>
@endsection
