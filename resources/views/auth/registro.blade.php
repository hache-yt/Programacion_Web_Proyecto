@extends('layouts.app')
@section('titulo', 'Crear cuenta')
@section('contenido')
<div class="auth-wrap">
<section class="auth-card">
<div class="page-heading"><div><p class="eyebrow">Empieza a cuidar el agua</p><h1>Crear cuenta</h1><p>Tu cuenta incluye una parcela principal para empezar a registrar riegos.</p></div></div>
<form class="panel formulario" method="POST" action="{{ route('registro') }}" novalidate>
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
<input type="password" id="password" name="password" required autocomplete="new-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('password') }}</small>
</div>
<div class="campo">
<label for="password_confirmation">Repetir contraseña</label>
<input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
</div>
<button class="boton" type="submit">Crear cuenta</button>
<p class="auth-switch">¿Ya tienes cuenta? <a class="enlace" href="{{ route('login') }}">Ingresar</a></p>
</form>
</section>
</div>
@endsection
