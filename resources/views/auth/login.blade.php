@extends('layouts.app')
@section('titulo', 'Ingresar')
@section('contenido')
<div class="auth-wrap">
<section class="auth-card">
<div class="page-heading"><div><p class="eyebrow">Qué bueno verte</p><h1>Ingresar</h1><p>Accede a tu espacio de gestión de riego.</p></div></div>
<form class="panel formulario" method="POST" action="{{ route('login') }}" novalidate>
@csrf
<div class="campo">
<label for="email">Correo electrónico</label>
<input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" required autofocus autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('email') }}</small>
</div>
<div class="campo">
<label for="password">Contraseña</label>
<input type="password" id="password" name="password" required autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<label class="casilla"><input type="checkbox" name="recordar"> Recordarme</label>
<button class="boton" type="submit">Ingresar</button>
<p class="auth-switch">¿No tienes cuenta? <a class="enlace" href="{{ route('registro') }}">Crear una cuenta</a></p>
</form>
</section>
</div>
@endsection
