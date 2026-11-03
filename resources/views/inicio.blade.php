@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
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
</section>
@if ($resumen)
<section class="tarjetas" aria-label="Resumen de actividad">
<article class="tarjeta">
<div class="tarjeta-top"><h2>Parcela principal</h2><span class="tarjeta-icono" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3.5 20.5h17M5 20.5V9.2l7-5.7 7 5.7v11.3"/><path d="M9 20.5v-6h6v6"/></svg></span></div>
<p class="cifra cifra-texto">{{ $resumen['parcela'] }}</p>
<p class="tarjeta-detalle">Tu espacio de cultivo</p>
</article>
<article class="tarjeta">
<div class="tarjeta-top"><h2>Riegos registrados</h2><span class="tarjeta-icono" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v18M4.5 7.5h15M6.5 16.5h11"/></svg></span></div>
<p class="cifra">{{ number_format($resumen['riegos']) }}</p>
<p class="tarjeta-detalle">Registros en el historial</p>
</article>
<article class="tarjeta">
<div class="tarjeta-top"><h2>Consumo en 7 días</h2><span class="tarjeta-icono" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z"/><path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8"/></svg></span></div>
<p class="cifra">{{ number_format($resumen['litrosSemana'], 2) }} <small>L</small></p>
<p class="tarjeta-detalle">Litros utilizados</p>
</article>
<article class="tarjeta">
<div class="tarjeta-top"><h2>Alertas pendientes</h2><span class="tarjeta-icono" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg></span></div>
<p class="cifra">{{ number_format($resumen['alertas']) }}</p>
<p class="tarjeta-detalle"><a class="enlace" href="{{ route('alertas.index') }}">Revisar alertas →</a></p>
</article>
</section>
@else
<section class="tarjetas tarjetas-guia" aria-label="Qué puedes hacer">
<article class="tarjeta"><div class="tarjeta-top"><h2>Registra</h2><span class="tarjeta-icono" aria-hidden="true">01</span></div><p>Anota la fecha, duración y litros de cada riego.</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Comprende</h2><span class="tarjeta-icono" aria-hidden="true">02</span></div><p>Consulta estadísticas claras sobre el consumo de agua.</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Cuida</h2><span class="tarjeta-icono" aria-hidden="true">03</span></div><p>Recibe alertas y mejora tus hábitos de riego.</p></article>
</section>
@endif
@endsection
