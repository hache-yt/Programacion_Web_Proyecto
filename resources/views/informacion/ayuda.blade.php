@extends('layouts.app')
@section('titulo', 'Centro de ayuda')
@section('contenido')
<article class="info-page">
<header class="page-heading"><div><p class="eyebrow">Estamos para orientarte</p><h1>Centro de ayuda</h1><p>Guías rápidas para usar Riego Inteligente con seguridad.</p></div></header>
<section class="help-grid">
<article class="help-card"><span class="help-number">01</span><h2>Cuenta y acceso</h2><p>Regístrate con tu nombre y correo. Si olvidaste tu contraseña, solicita un enlace de recuperación desde la pantalla de ingreso.</p><a class="enlace" href="{{ route('login') }}">Ir a iniciar sesión →</a></article>
<article class="help-card"><span class="help-number">02</span><h2>Registrar riegos</h2><p>Guarda la fecha, la duración y los litros utilizados. El historial y las estadísticas dependen de los datos que registres.</p>@auth<a class="enlace" href="{{ route('riegos.create') }}">Registrar un riego →</a>@else<a class="enlace" href="{{ route('registro') }}">Crear una cuenta →</a>@endauth</article>
<article class="help-card"><span class="help-number">03</span><h2>Alertas y estadísticas</h2><p>Las alertas señalan registros que superan el umbral configurado. No son órdenes automáticas ni sustituyen la revisión del sistema de riego.</p></article>
<article class="help-card"><span class="help-number">04</span><h2>Privacidad y ubicación</h2><p>La ubicación es opcional. Si deseas consultar las coordenadas aproximadas de este dispositivo, usa el botón de abajo. El navegador pedirá permiso; la ubicación se muestra aquí y no se guarda ni se envía al sistema.</p><button class="boton boton-secundario" type="button" data-location-request>Consultar mi ubicación</button><p class="location-result" id="location-result" role="status" aria-live="polite"></p></article>
<article class="help-card"><span class="help-number">05</span><h2>¿Necesitas asistencia?</h2><p>Contacta al administrador de la instalación o al equipo académico responsable. Esta versión no cuenta con un correo de soporte publicado.</p><a class="enlace" href="{{ route('privacidad') }}">Revisar privacidad →</a></article>
<article class="help-card"><span class="help-number">06</span><h2>Consulta al asistente IA</h2><p>¿Tienes dudas sobre cómo usar Riego Inteligente? Pregunta al asistente; no tiene acceso a tu cuenta ni a tus registros.</p><button class="boton boton-secundario" type="button" data-chatbot-open>Preguntar a la IA</button></article>
</section>
<div class="info-callout"><strong>¿No quieres compartir tu ubicación?</strong><p>No hay problema. La ubicación no es necesaria para registrarte, iniciar sesión ni usar los módulos de riego.</p></div>
</article>
@endsection
