@extends('layouts.app')
@section('titulo', 'Registrarse')
@section('contenido')
<h1>Crear cuenta</h1>
<form class="tarjeta formulario" method="POST" action="{{ route('registro') }}" novalidate>
@csrf
<div class="campo">
<label for="name">Nombre</label>
<input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="100" required autofocus>
<small class="error" role="alert">{{ $errors->first('name') }}</small>
</div>
<div class="campo">
<label for="email">Correo</label>
<input type="email" id="email" name="email" value="{{ old('email') }}" required>
<small class="error" role="alert">{{ $errors->first('email') }}</small>
</div>
<div class="campo">
<label for="password">Contraseña (mínimo 8 caracteres)</label>
<input type="password" id="password" name="password" required>
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<div class="campo">
<label for="password_confirmation">Repetir contraseña</label>
<input type="password" id="password_confirmation" name="password_confirmation" required>
</div>
<button class="boton" type="submit">Crear cuenta</button>
<p>¿Ya tienes cuenta? <a class="enlace" href="{{ route('login') }}">Ingresa</a></p>
</form>
@endsection
