@extends('layouts.app')
@section('titulo', 'Estadísticas')
@section('contenido')
<h1>Estadísticas de consumo</h1>
<p>Total en los últimos 14 días: <strong>{{ number_format($totalLitros, 2) }} L</strong></p>
<section class="graficos">
<article class="tarjeta">
<h2>Litros por día</h2>
<canvas id="grafico-dias" data-etiquetas="{{ json_encode($dias) }}" data-valores="{{ json_encode($litrosPorDia) }}"></canvas>
</article>
<article class="tarjeta">
<h2>Litros por parcela</h2>
<canvas id="grafico-parcelas" data-etiquetas="{{ json_encode($parcelas) }}" data-valores="{{ json_encode($litrosPorParcela) }}"></canvas>
</article>
</section>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/graficos.js') }}"></script>
@endpush
