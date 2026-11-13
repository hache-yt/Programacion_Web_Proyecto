@extends('layouts.app')
@section('titulo', 'Acerca del proyecto')
@section('contenido')
<article class="info-page">
<header class="page-heading"><div><p class="eyebrow">Tecnología para cuidar el agua</p><h1>Acerca de Riego Inteligente</h1><p>Una herramienta académica para registrar y comprender el uso del agua en el riego.</p></div></header>
<section class="about-hero">
<div class="about-emblem" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.5"/><path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"/></svg></div>
<div><p class="eyebrow">Proyecto académico · 2026-II</p><h2>Monitorea. Comprende. Cuida.</h2><p>La plataforma reúne registros de riego, alertas y resúmenes para ayudar a las personas usuarias a observar su consumo de agua.</p></div>
</section>
<section class="info-section"><h2>Qué puedes hacer</h2><div class="about-features"><div><strong>Registrar</strong><span>Duración y cantidad de agua utilizada.</span></div><div><strong>Consultar</strong><span>Historial, alertas y estadísticas basadas en los registros.</span></div><div><strong>Administrar</strong><span>Gestionar cuentas y permisos desde un acceso autorizado.</span></div></div></section>
<section class="info-section"><h2>Equipo desarrollador</h2><div class="team-grid"><article class="team-card"><span class="team-avatar">ST</span><div><strong>Said Torres Lozano</strong><span>Desarrollador del proyecto</span></div></article><article class="team-card"><span class="team-avatar">RK</span><div><strong>Rey Kino Choquichampi Mendoza</strong><span>Desarrollador del proyecto</span></div></article></div></section>
<div class="info-callout"><strong>Contexto institucional</strong><p>Proyecto académico asociado a Universidad Continental, Cusco, periodo 2026-II. La plataforma no es un servicio oficial ni representa una declaración o certificación institucional.</p></div>
<p class="info-meta">Desarrollado con Laravel · Información de consumo calculada a partir de registros ingresados en la plataforma.</p>
<a class="boton boton-secundario" href="{{ route('inicio') }}">Volver al inicio</a>
</article>
@endsection
