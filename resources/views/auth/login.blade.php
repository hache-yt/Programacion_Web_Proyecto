@extends('layouts.app')
@section('titulo', 'Ingresar')
@section('contenido')
<section class="auth-card">
<div class="auth-heading"><p class="eyebrow">Bienvenido de nuevo</p><h1>Iniciar sesión</h1><p>Accede a tu cuenta para gestionar tu sistema de riego.</p></div>
<form class="auth-form formulario" method="POST" action="{{ route('login') }}" novalidate>
@csrf
<div class="campo">
<label for="email">Correo electrónico o usuario</label>
<input type="text" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" required autofocus autocomplete="username" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('email') }}</small>
</div>
<div class="campo">
<label for="password">Contraseña</label>
<div class="password-field"><input type="password" id="password" name="password" required autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"><button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false" data-password-toggle="password">Ver</button></div>
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<div class="auth-options"><label class="casilla"><input type="checkbox" name="recordar"> Recordarme</label><a class="enlace" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></div>
<button class="boton auth-submit" type="submit">Ingresar</button>
<p class="auth-switch">¿No tienes una cuenta? <a class="enlace" href="{{ route('registro') }}">Regístrate</a></p>
</form>
</section>
@endsection
