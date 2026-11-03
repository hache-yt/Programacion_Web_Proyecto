@extends('layouts.app')
@section('titulo', $riego->exists ? 'Editar riego' : 'Registrar riego')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Registro de consumo</p><h1>{{ $riego->exists ? 'Editar riego' : 'Registrar riego' }}</h1><p>Completa los datos para mantener actualizado el historial de tu parcela principal.</p></div>
</div>
<form id="form-riego" class="panel formulario" method="POST" action="{{ $riego->exists ? route('riegos.update', $riego) : route('riegos.store') }}" novalidate>
@csrf
@if ($riego->exists)
@method('PUT')
@endif
<div class="form-grid">
<div class="campo">
<label for="fecha">Fecha del riego</label>
<input type="date" id="fecha" name="fecha" max="{{ now()->toDateString() }}" value="{{ old('fecha', optional($riego->fecha)->format('Y-m-d')) }}" required aria-invalid="{{ $errors->has('fecha') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('fecha') }}</small>
</div>
<div class="campo">
<label for="duracion_min">Duración <span aria-hidden="true">(minutos)</span></label>
<input type="number" id="duracion_min" name="duracion_min" min="1" step="1" value="{{ old('duracion_min', $riego->duracion_min) }}" placeholder="Ej. 30" required aria-invalid="{{ $errors->has('duracion_min') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('duracion_min') }}</small>
</div>
<div class="campo">
<label for="cantidad_litros">Agua utilizada <span aria-hidden="true">(litros)</span></label>
<input type="number" id="cantidad_litros" name="cantidad_litros" min="0.01" step="0.01" value="{{ old('cantidad_litros', $riego->cantidad_litros) }}" placeholder="Ej. 650" required aria-invalid="{{ $errors->has('cantidad_litros') ? 'true' : 'false' }}">
<small class="error" role="alert">{{ $errors->first('cantidad_litros') }}</small>
</div>
<div class="campo">
<label for="observaciones">Observaciones <span>(opcional)</span></label>
<input type="text" id="observaciones" name="observaciones" maxlength="255" value="{{ old('observaciones', $riego->observaciones) }}" placeholder="Método de riego, notas…">
<small class="error" role="alert">{{ $errors->first('observaciones') }}</small>
</div>
</div>
<div class="form-acciones">
<button class="boton" type="submit">{{ $riego->exists ? 'Guardar cambios' : 'Guardar riego' }}</button>
<a class="boton boton-secundario" href="{{ route('riegos.index') }}">Cancelar</a>
</div>
</form>
@endsection
@push('scripts')
<script src="{{ asset('js/validaciones.js') }}"></script>
@endpush
