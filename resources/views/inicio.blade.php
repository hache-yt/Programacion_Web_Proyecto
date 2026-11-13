@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
@if ($resumen)
<div class="page-heading dashboard-heading">
<div><p class="eyebrow">Panel de control</p><h1>Hola, {{ auth()->user()->name }}</h1><p>Resumen del estado de tu sistema de riego.</p></div>
<a class="boton" href="{{ route('riegos.create') }}">+ Registrar riego</a>
</div>
<section class="tarjetas dashboard-cards" aria-label="Indicadores de actividad">
<article class="tarjeta"><div class="tarjeta-top"><h2>Riegos registrados</h2><span class="tarjeta-icono">◷</span></div><p class="cifra">{{ number_format($resumen['riegos']) }}</p><p class="tarjeta-detalle">En el historial de tu cuenta</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Agua utilizada · 7 días</h2><span class="tarjeta-icono">◉</span></div><p class="cifra">{{ number_format($resumen['litrosSemana'], 2) }} <small>L</small></p><p class="tarjeta-detalle">Consumo registrado en la última semana</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Alertas pendientes</h2><span class="tarjeta-icono">♧</span></div><p class="cifra">{{ number_format($resumen['alertas']) }}</p><p class="tarjeta-detalle"><a class="enlace" href="{{ route('alertas.index') }}">Consultar alertas →</a></p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Parcela principal</h2><span class="tarjeta-icono">⌂</span></div><p class="cifra cifra-texto">{{ $resumen['parcela'] }}</p><p class="tarjeta-detalle"><a class="enlace" href="{{ route('parcelas.index') }}">Ver detalles →</a></p></article>
</section>
<div class="dashboard-grid">
<section class="panel dashboard-chart">
<div class="grafico-cabecera"><div><h2>Consumo de agua</h2><p>Litros registrados por día · últimos 14 días</p></div><a class="enlace" href="{{ route('estadisticas.index') }}">Ver estadísticas →</a></div>
<div class="grafico-contenedor"><canvas id="grafico-inicio" data-etiquetas="{{ json_encode($dias) }}" data-valores="{{ json_encode($litrosPorDia) }}" role="img" aria-label="Litros utilizados por día durante los últimos 14 días"></canvas></div>
</section>
<aside class="panel dashboard-quick">
<div class="panel-heading"><h2>Accesos rápidos</h2><p class="panel-copy">Tus módulos principales</p></div>
<a class="quick-link" href="{{ route('riegos.index') }}"><span class="quick-icon">≡</span><span><strong>Historial de riegos</strong><small>Revisa y edita tus registros</small></span><b>→</b></a>
<a class="quick-link" href="{{ route('estadisticas.index') }}"><span class="quick-icon">⌁</span><span><strong>Estadísticas</strong><small>Analiza el consumo de agua</small></span><b>→</b></a>
<a class="quick-link" href="{{ route('parcelas.index') }}"><span class="quick-icon">⌂</span><span><strong>Parcela principal</strong><small>{{ $resumen['parcela'] }}</small></span><b>→</b></a>
<button class="quick-link chatbot-quick-link" type="button" data-chatbot-open><span class="quick-icon">✦</span><span><strong>Consulta a la IA</strong><small>Resuelve dudas sobre la plataforma</small></span><b>→</b></button>
@if (auth()->user()->esAdmin())
<a class="quick-link" href="{{ route('admin.dashboard') }}"><span class="quick-icon">▦</span><span><strong>Administración</strong><small>Resumen general del sistema</small></span><b>→</b></a>
@endif
</aside>
</div>
<section class="panel dashboard-recent">
<div class="grafico-cabecera"><div><h2>Actividad reciente</h2><p>Últimos registros de riego</p></div><a class="enlace" href="{{ route('riegos.index') }}">Ver historial →</a></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha</th><th>Parcela</th><th>Agua utilizada</th><th>Duración</th></tr></thead><tbody>
@forelse ($riegosRecientes as $riego)
<tr><td><strong>{{ $riego->fecha->format('d/m/Y') }}</strong></td><td>{{ $riego->parcela->nombre }}</td><td>{{ number_format($riego->cantidad_litros, 2) }} L</td><td>{{ $riego->duracion_min }} min</td></tr>
@empty
<tr><td class="vacio" colspan="4"><strong>Aún no hay registros</strong>Al guardar tu primer riego, aparecerá aquí.</td></tr>
@endforelse
</tbody></table></div>
</section>
@else
<section class="portada">
<div class="portada-contenido">
<p class="eyebrow">Gestión hídrica · Cusco</p>
<h1>Sabe cuánta agua usas al regar</h1>
<p>Registra cada riego, entiende tu consumo y toma decisiones que ayudan a cuidar el agua.</p>
<div class="hero-acciones">
@auth
<a class="boton" href="{{ route('riegos.create') }}">Registrar un riego <span aria-hidden="true">→</span></a>
<a class="hero-link" href="{{ route('estadisticas.index') }}">Ver estadísticas <span aria-hidden="true">↗</span></a>
@else
<a class="boton" href="{{ route('registro') }}">Crear una cuenta <span aria-hidden="true">→</span></a>
<a class="hero-link" href="{{ route('login') }}">Ya tengo cuenta</a>
@endauth
</div>
</div>
<div class="hero-watermark" aria-hidden="true">
<div class="hero-watermark-orbit"><span class="hero-watermark-drop"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.4"/><path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.4"/></svg></span></div>
<span class="hero-watermark-caption">Un riego<br>más inteligente</span>
</div>
</section>
<section class="tarjetas tarjetas-guia" aria-label="Qué puedes hacer">
<article class="tarjeta"><div class="tarjeta-top"><h2>Registra</h2><span class="tarjeta-icono" aria-hidden="true">01</span></div><p>Anota la fecha, duración y litros de cada riego.</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Comprende</h2><span class="tarjeta-icono" aria-hidden="true">02</span></div><p>Consulta estadísticas claras sobre el consumo de agua.</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Cuida</h2><span class="tarjeta-icono" aria-hidden="true">03</span></div><p>Recibe alertas y mejora tus hábitos de riego.</p></article>
</section>
<div class="info-callout chatbot-callout"><div><strong>¿Tienes dudas sobre la plataforma?</strong><p>Consulta al asistente de IA y encuentra orientación para empezar.</p></div><button class="boton boton-secundario" type="button" data-chatbot-open>Consultar a la IA</button></div>
@endif
@endsection
@if ($resumen)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/graficos.js') }}"></script>
@endpush
@endif
