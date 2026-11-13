@extends('layouts.app')
@section('titulo', 'Recuperar contraseña')
@section('contenido')
<section class="auth-card">
<div class="auth-heading"><p class="eyebrow">Recupera el acceso</p><h1>Restablecer contraseña</h1><p>Te enviaremos instrucciones al correo asociado a tu cuenta.</p></div>
<form class="auth-form formulario" method="POST" action="{{ route('password.email') }}">
@csrf
<div class="campo"><label for="email">Correo electrónico</label><input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus><small class="error" role="alert">{{ $errors->first('email') }}</small></div>
<button class="boton auth-submit" type="submit">Enviar enlace de recuperación</button>
<p class="auth-switch"><a class="enlace" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
</form>
</section>
@endsection
