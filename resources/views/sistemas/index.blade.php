@extends('layouts.app')
@section('titulo', 'Sistemas de riego')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Administración por cliente</p><h1>Sistemas de riego</h1><p>Cada sistema conserva su propio modo, configuración, lecturas e historial.</p></div>
</div>
<section class="panel system-create-panel">
<div class="panel-heading"><h2>Agregar sistema</h2><p class="panel-copy">El sistema se asociará a tu parcela principal. No requiere conectar hardware para registrarlo.</p></div>
<form class="system-create-form" method="POST" action="{{ route('sistemas.store') }}">
@csrf
<div class="campo"><label for="nombre">Nombre del sistema</label><input id="nombre" name="nombre" maxlength="100" value="{{ old('nombre') }}" required aria-invalid="{{ $errors->has('nombre') ? 'true' : 'false' }}"><small class="error" role="alert">{{ $errors->first('nombre') }}</small></div>
<div class="campo"><label for="descripcion">Descripción <span>(opcional)</span></label><input id="descripcion" name="descripcion" maxlength="1000" value="{{ old('descripcion') }}"><small class="error" role="alert">{{ $errors->first('descripcion') }}</small></div>
<fieldset class="campo system-registration-type">
<legend>¿Qué quieres hacer?</legend>
<label class="registration-option"><input type="radio" name="registro_fisico" value="0" @checked(old('registro_fisico', '0') === '0')>Solo registrar el sistema</label>
<label class="registration-option"><input type="radio" name="registro_fisico" value="1" @checked(old('registro_fisico') === '1')>Registrar un equipo físico</label>
<small class="error" role="alert">{{ $errors->first('registro_fisico') }}</small>
</fieldset>
<div class="physical-device-fields" data-physical-device-fields @if(old('registro_fisico') !== '1') hidden @endif>
<div class="device-search-box">
<div>
<strong>Buscar equipo</strong>
<p>Busca un equipo compatible en esta demostración. No se contacta una dirección real.</p>
</div>
<button class="boton boton-secundario" type="button" data-search-device>Buscar equipo</button>
</div>
<p class="device-search-status" data-device-status role="status">Sin búsqueda realizada.</p>
<input type="hidden" name="dispositivo_encontrado" value="{{ old('dispositivo_encontrado', '0') }}" data-device-found>
<div class="form-grid">
<div class="campo"><label for="tipo_conexion">Tipo de conexión</label><select id="tipo_conexion" name="tipo_conexion"><option value="">Selecciona una opción</option><option value="wifi" @selected(old('tipo_conexion') === 'wifi')>Wi-Fi inalámbrica</option><option value="ethernet" @selected(old('tipo_conexion') === 'ethernet')>Cable de red</option></select><small class="error" role="alert">{{ $errors->first('tipo_conexion') }}</small></div>
<div class="campo"><label for="direccion_ip">Dirección IP del equipo</label><input id="direccion_ip" name="direccion_ip" inputmode="decimal" maxlength="45" placeholder="Ej. 192.168.1.120" value="{{ old('direccion_ip') }}"><small class="error" role="alert">{{ $errors->first('direccion_ip') }}</small></div>
<div class="campo"><label for="identificador_dispositivo">Nombre o identificador encontrado</label><input id="identificador_dispositivo" name="identificador_dispositivo" maxlength="100" placeholder="Ej. ESP32-RIEGO-01" value="{{ old('identificador_dispositivo') }}"><small class="error" role="alert">{{ $errors->first('identificador_dispositivo') }}</small></div>
</div>
</div>
<button class="boton" type="submit">Crear sistema</button>
</form>
</section>
<section class="system-card-grid" aria-label="Sistemas registrados">
@forelse ($sistemas as $sistema)
<article class="panel system-list-card">
<div class="system-list-heading"><div><p class="eyebrow">{{ $sistema->parcela->usuario->name }}</p><h2>{{ $sistema->nombre }}</h2></div><span class="status-pill {{ $sistema->conexion_simulada ? 'status-test' : 'status-offline' }}">{{ $sistema->conexion_simulada ? 'Equipo encontrado (simulado)' : 'Sin conexión' }}</span></div>
<p>{{ $sistema->descripcion ?: 'Sin descripción.' }}</p>
@if ($sistema->conexion_simulada)
<p class="device-summary"><strong>{{ $sistema->identificador_dispositivo }}</strong> · {{ $sistema->tipo_conexion === 'wifi' ? 'Wi-Fi' : 'Cable de red' }} · {{ $sistema->direccion_ip }}</p>
@endif
<dl class="system-facts">
<div><dt>Modo</dt><dd>{{ strtoupper($sistema->configuracion->modo ?? 'manual') }}</dd></div>
<div><dt>Riego</dt><dd>{{ $sistema->riego_activo ? 'ACTIVO' : 'CERRADO' }}</dd></div>
<div><dt>Última lectura de prueba</dt><dd>{{ $sistema->ultimaMedicion?->fecha_lectura?->format('d/m/Y H:i') ?? 'Sin lecturas' }}</dd></div>
</dl>
<a class="boton boton-secundario" href="{{ route('sistemas.show', $sistema) }}">Abrir panel del sistema</a>
</article>
@empty
<div class="panel empty-note"><strong>Aún no hay sistemas registrados.</strong><p>Crea uno para administrar su configuración e ingresar lecturas de prueba.</p></div>
@endforelse
</section>
@endsection
