@extends('layouts.app')
@section('titulo', 'Ingresar')
@section('contenido')
<h1>Ingresar</h1>
<form class="tarjeta formulario" method="POST" action="{{ route('login') }}" novalidate>
@csrf
<div class="campo">
<label for="email">Correo</label>
<input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" required autofocus>
<small class="error" role="alert">{{ $errors->first('email') }}</small>
</div>
<div class="campo">
<label for="password">Contraseña</label>
<input type="password" id="password" name="password" required>
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<label class="casilla"><input type="checkbox" name="recordar"> Recordarme</label>
<button class="boton" type="submit">Ingresar</button>
<p>¿No tienes cuenta? <a class="enlace" href="{{ route('registro') }}">Regístrate</a></p>
</form>
@endsection
