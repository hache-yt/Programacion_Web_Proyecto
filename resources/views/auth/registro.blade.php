@extends('layouts.app')
@section('titulo', 'Crear cuenta')
@section('contenido')
<section class="auth-card">
<div class="auth-heading"><p class="eyebrow">Empieza a cuidar el agua</p><h1>Crear una cuenta</h1><p>Completa tus datos para empezar a usar la plataforma.</p></div>
<form class="auth-form formulario registro-form" method="POST" action="{{ route('registro') }}" novalidate>
@csrf
<div class="campo">
<label for="name">Nombre completo</label>
<input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="100" required autofocus autocomplete="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('name') }}</small>
</div>
<div class="campo">
<label for="email">Correo electrónico</label>
<input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('email') }}</small>
</div>
<div class="campo">
<label for="password">Contraseña <span>(mínimo 8 caracteres)</span></label>
<div class="password-field"><input type="password" id="password" name="password" required autocomplete="new-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"><button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false" data-password-toggle="password">Ver</button></div>
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<div class="campo">
<label for="password_confirmation">Repetir contraseña</label>
<div class="password-field"><input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"><button class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false" data-password-toggle="password_confirmation">Ver</button></div>
</div>
<button class="boton auth-submit" type="submit">Registrarse</button>
<p class="auth-switch">¿Ya tienes una cuenta? <a class="enlace" href="{{ route('login') }}">Iniciar sesión</a></p>
</form>
</section>
@endsection
