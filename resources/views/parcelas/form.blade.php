@extends('layouts.app')
@section('titulo', $parcela->exists ? 'Editar parcela' : 'Nueva parcela')
@section('contenido')
<h1>{{ $parcela->exists ? 'Editar parcela' : 'Nueva parcela' }}</h1>
<form class="tarjeta formulario" method="POST" action="{{ $parcela->exists ? route('parcelas.update', $parcela) : route('parcelas.store') }}" novalidate>
@csrf
@if ($parcela->exists)
@method('PUT')
@endif
<div class="campo">
<label for="nombre">Nombre</label>
<input type="text" id="nombre" name="nombre" maxlength="100" value="{{ old('nombre', $parcela->nombre) }}" required>
<small class="error" role="alert">{{ $errors->first('nombre') }}</small>
</div>
<div class="campo">
<label for="ubicacion">Ubicación</label>
<input type="text" id="ubicacion" name="ubicacion" maxlength="150" value="{{ old('ubicacion', $parcela->ubicacion) }}">
<small class="error" role="alert">{{ $errors->first('ubicacion') }}</small>
</div>
<div class="campo">
<label for="area_m2">Área (m²)</label>
<input type="number" id="area_m2" name="area_m2" min="0.01" step="0.01" value="{{ old('area_m2', $parcela->area_m2) }}">
<small class="error" role="alert">{{ $errors->first('area_m2') }}</small>
</div>
<div class="campo">
<label for="tipo_cultivo">Tipo de cultivo</label>
<input type="text" id="tipo_cultivo" name="tipo_cultivo" maxlength="80" value="{{ old('tipo_cultivo', $parcela->tipo_cultivo) }}">
<small class="error" role="alert">{{ $errors->first('tipo_cultivo') }}</small>
</div>
<button class="boton" type="submit">Guardar parcela</button>
</form>
@endsection
