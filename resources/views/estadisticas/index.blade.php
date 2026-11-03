@extends('layouts.app')
@section('titulo', 'Estadísticas')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Datos de consumo</p><h1>Estadísticas</h1><p>Una mirada clara al agua utilizada en los últimos 14 días.</p></div>
</div>
<section class="graficos">
<article class="panel">
<div class="grafico-cabecera">
<div><h2>Consumo diario</h2><p>Litros registrados por día</p></div>
<span class="grafico-total">{{ number_format($totalLitros, 2) }} L</span>
</div>
<div class="grafico-contenedor">
<canvas id="grafico-dias" data-etiquetas="{{ json_encode($dias) }}" data-valores="{{ json_encode($litrosPorDia) }}" role="img" aria-label="Gráfico de litros utilizados por día"></canvas>
</div>
</article>
</section>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/graficos.js') }}"></script>
@endpush
