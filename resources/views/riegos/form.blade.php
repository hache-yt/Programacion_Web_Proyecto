@extends('layouts.app')
@section('titulo', $riego->exists ? 'Editar riego' : 'Registrar riego')
@section('contenido')
<h1>{{ $riego->exists ? 'Editar riego' : 'Registrar riego' }}</h1>
<form id="form-riego" class="tarjeta formulario" method="POST" action="{{ $riego->exists ? route('riegos.update', $riego) : route('riegos.store') }}" novalidate>
@csrf
@if ($riego->exists)
@method('PUT')
@endif
<div class="campo">
<label for="parcela_id">Parcela o área</label>
<select id="parcela_id" name="parcela_id" required>
<option value="">Selecciona...</option>
@foreach ($parcelas as $parcela)
<option value="{{ $parcela->id }}" @selected(old('parcela_id', $riego->parcela_id) == $parcela->id)>{{ $parcela->nombre }}</option>
@endforeach
</select>
<small class="error" role="alert">{{ $errors->first('parcela_id') }}</small>
</div>
<div class="campo">
<label for="fecha">Fecha</label>
<input type="date" id="fecha" name="fecha" max="{{ now()->toDateString() }}" value="{{ old('fecha', optional($riego->fecha)->format('Y-m-d')) }}" required>
<small class="error" role="alert">{{ $errors->first('fecha') }}</small>
</div>
<div class="campo">
<label for="duracion_min">Duración (minutos)</label>
<input type="number" id="duracion_min" name="duracion_min" min="1" step="1" value="{{ old('duracion_min', $riego->duracion_min) }}" required>
<small class="error" role="alert">{{ $errors->first('duracion_min') }}</small>
</div>
<div class="campo">
<label for="cantidad_litros">Agua utilizada (litros)</label>
<input type="number" id="cantidad_litros" name="cantidad_litros" min="0.01" step="0.01" value="{{ old('cantidad_litros', $riego->cantidad_litros) }}" required>
<small class="error" role="alert">{{ $errors->first('cantidad_litros') }}</small>
</div>
<div class="campo">
<label for="observaciones">Observaciones (opcional)</label>
<input type="text" id="observaciones" name="observaciones" maxlength="255" value="{{ old('observaciones', $riego->observaciones) }}">
<small class="error" role="alert">{{ $errors->first('observaciones') }}</small>
</div>
<button class="boton" type="submit">Guardar riego</button>
</form>
@endsection
@push('scripts')
<script src="{{ asset('js/validaciones.js') }}"></script>
@endpush
