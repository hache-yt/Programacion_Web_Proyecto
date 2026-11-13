<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#173a32">
<title>@yield('titulo', 'Riego Inteligente') · Riego Inteligente</title>
<link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ filemtime(public_path('css/styles.css')) }}">
</head>
<body class="{{ request()->routeIs('login', 'registro', 'password.request', 'password.reset') ? 'auth-page' : '' }}">
@if (request()->routeIs('inicio'))
<div class="presentation-screen" id="presentation-screen" aria-hidden="true">
<div class="presentation-card">
<div class="presentation-particles" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
<div class="presentation-mark-wrap"><span class="presentation-mark">
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.8"/><path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg>
</span></div>
<p class="presentation-kicker">Universidad Continental <span>·</span> Cusco</p>
<h2>Plataforma de Riego</h2>
<p class="presentation-term">Semestre académico 2026-II</p>
<p class="presentation-tagline">Innovación que hace crecer cada gota.</p>
<div class="presentation-progress" role="presentation"><span></span></div>
<p class="presentation-loading" id="presentation-loading">Preparando una experiencia más sostenible</p>
<div class="presentation-footer"><span>AGRICULTURA INTELIGENTE</span><span>•</span><span>AGUA · CULTIVO · FUTURO</span></div>
</div>
</div>
@endif
@if (request()->routeIs('login', 'registro', 'password.request', 'password.reset'))
<div class="auth-screen {{ request()->routeIs('registro') ? 'auth-register' : 'auth-login' }}">
<aside class="auth-visual">
<a class="brand auth-brand" href="{{ route('inicio') }}">
<span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.8"/><path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/></svg></span>
<span><span class="brand-name">Riego Inteligente</span><span class="brand-caption">Tecnología que cuida el agua</span></span>
</a>
<div class="auth-visual-copy">
<p>Monitorea · Controla · Optimiza</p>
<span>Tu aliado en el uso eficiente del agua.</span>
</div>
<span class="auth-visual-foot">Cada gota cuenta para un futuro sostenible.</span>
</aside>
<main class="auth-main">
@if (session('ok'))
<p class="aviso" role="status">{{ session('ok') }}</p>
@endif
@if (session('status'))
<p class="aviso" role="status">{{ session('status') }}</p>
@endif
@yield('contenido')
<footer class="pie pie-formal">
<span>Proyecto académico · Universidad Continental · 2026-II</span>
<span>Desarrollado por Said Torres Lozano y Rey Kino Choquichampi Mendoza</span>
<nav class="footer-links" aria-label="Información del sistema">
<a href="{{ route('privacidad') }}">Privacidad</a><a href="{{ route('terminos') }}">Términos</a><a href="{{ route('ayuda') }}">Ayuda</a><a href="{{ route('acerca') }}">Acerca de</a>
</nav>
<button class="footer-cookie-settings" type="button" data-cookie-open>Preferencias de cookies</button>
</footer>
</main>
</div>
@else
<div class="app-shell">
<aside class="sidebar">
<a class="brand" href="{{ route('inicio') }}">
<span class="brand-mark" aria-hidden="true">
<svg viewBox="0 0 24 24" fill="none">
<path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.8"/>
<path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
</svg>
</span>
<span><span class="brand-name">Riego Inteligente</span><span class="brand-caption">Gestión hídrica</span></span>
</a>
<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav" aria-label="Abrir menú de navegación">
<span></span><span></span><span></span>
</button>
<p class="nav-label">Espacio de trabajo</p>
<nav class="main-nav" id="main-nav" aria-label="Navegación principal">
@auth
<a href="{{ route('inicio') }}" @if (request()->routeIs('inicio')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3.5 10 8.5-7 8.5 7"/><path d="M5.5 9v11h13V9M9.5 20v-6h5v6"/></svg></span>
Inicio
</a>
<a href="{{ route('riegos.create') }}" @if (request()->routeIs('riegos.create')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 3v18M4.5 7.5h15M6.5 16.5h11"/><path d="m9 4 3-2 3 2"/></svg></span>
Registrar riego
</a>
<a href="{{ route('riegos.index') }}" @if (request()->routeIs('riegos.index', 'riegos.edit')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 5.5h14M5 10h14M5 14.5h9M5 19h9"/><path d="M3 3h18v18H3z"/></svg></span>
Historial
</a>
<a href="{{ route('parcelas.index') }}" @if (request()->routeIs('parcelas.*')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3.5 20.5h17M5 20.5V9.2l7-5.7 7 5.7v11.3"/><path d="M9 20.5v-6h6v6M8 10h.01M16 10h.01"/></svg></span>
Parcela principal
</a>
<a href="{{ route('estadisticas.index') }}" @if (request()->routeIs('estadisticas.*')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 20V11M10 20V5M16 20v-8M22 20V3"/><path d="M2 20.5h21"/></svg></span>
Estadísticas
</a>
<a href="{{ route('alertas.index') }}" @if (request()->routeIs('alertas.*')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></span>
Alertas
</a>
@if (auth()->user()->esAdmin())
<a href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.*')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v5h-8zM13 10h8v11h-8zM3 13h8v8H3z"/></svg></span>
Administración
</a>
<a href="{{ route('usuarios.index') }}" @if (request()->routeIs('usuarios.*')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.1 2.5-5.5 6.5-5.5s6.5 2.4 6.5 5.5M16 5.2a3.5 3.5 0 0 1 0 6.7M18 14.8c2.3.8 3.5 2.6 3.5 5.2"/></svg></span>
Usuarios
</a>
@endif
@else
<a href="{{ route('inicio') }}" @if (request()->routeIs('inicio')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3.5 10 8.5-7 8.5 7"/><path d="M5.5 9v11h13V9M9.5 20v-6h5v6"/></svg></span>
Inicio
</a>
<a href="{{ route('login') }}" @if (request()->routeIs('login')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M10 5H5v14h5M14 8l4 4-4 4M8 12h10"/></svg></span>
Ingresar
</a>
<a href="{{ route('registro') }}" @if (request()->routeIs('registro')) aria-current="page" @endif>
<span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.1 2.5-5.5 6.5-5.5s6.5 2.4 6.5 5.5M19 8v7M15.5 11.5h7"/></svg></span>
Crear cuenta
</a>
@endauth
</nav>
<div class="sidebar-note">
<strong>Una parcela. Cada gota cuenta.</strong>
<p>Registra el agua, sigue tus hábitos y cuida cada recurso.</p>
</div>
</aside>
<div class="workspace">
<header class="topbar">
<p class="topbar-label">@yield('titulo', 'Riego Inteligente')</p>
@auth
<div class="topbar-meta"><span class="date-chip">{{ now()->format('d/m/Y') }} · {{ now()->format('H:i') }}</span><a class="notification-link" href="{{ route('alertas.index') }}" aria-label="Ver alertas"><span aria-hidden="true">♧</span>@php($alertasPendientes = \App\Models\Alerta::visiblesPara(auth()->user())->where('estado', 'pendiente')->count())@if ($alertasPendientes > 0)<span class="notification-count">{{ $alertasPendientes }}</span>@endif</a></div>
<div class="topbar-user">
<span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
<span class="user-name">{{ auth()->user()->name }}</span>
<form class="salir" method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit">Salir</button>
</form>
</div>
@else
<span class="topbar-label">Cultiva con conciencia</span>
@endauth
</header>
<main class="contenido">
@if (session('ok'))
<p class="aviso" role="status">{{ session('ok') }}</p>
@endif
@if ($errors->has('usuario'))
<p class="aviso aviso-error" role="alert">{{ $errors->first('usuario') }}</p>
@endif
@yield('contenido')
</main>
<footer class="pie pie-formal">
<span>Proyecto académico · Universidad Continental · 2026-II</span>
<span>Desarrollado por Said Torres Lozano y Rey Kino Choquichampi Mendoza</span>
<nav class="footer-links" aria-label="Información del sistema">
<a href="{{ route('privacidad') }}">Privacidad</a><a href="{{ route('terminos') }}">Términos</a><a href="{{ route('ayuda') }}">Ayuda</a><a href="{{ route('acerca') }}">Acerca de</a>
</nav>
<button class="footer-cookie-settings" type="button" data-cookie-open>Preferencias de cookies</button>
</footer>
</div>
</div>
@endif
<div class="chatbot-widget">
<section class="chatbot-panel" id="chatbot-panel" role="dialog" aria-label="Asistente de Riego Inteligente" hidden>
<header class="chatbot-panel-header">
<div><strong>Asistente IA</strong><span>Resuelve tus dudas sobre la plataforma</span></div>
<button class="chatbot-close" type="button" data-chatbot-close aria-label="Cerrar chat">×</button>
</header>
<iframe class="chatbot-frame" title="Chat de ayuda de Riego Inteligente" data-src="{{ route('chatbot.index') }}" loading="lazy"></iframe>
</section>
<button class="chatbot-launcher" type="button" aria-controls="chatbot-panel" aria-expanded="false" data-chatbot-open>
<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3.5a8.5 8.5 0 0 0-7.2 13l-.9 4 4.1-1A8.5 8.5 0 1 0 12 3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9.3 12 1.8 1.8 3.7-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
<span>¿Consultas? Habla con la IA</span>
</button>
</div>
<dialog class="dialogo-confirmacion" id="dialogo-confirmacion" aria-labelledby="dialogo-titulo">
<div class="dialogo-contenido">
<h2 id="dialogo-titulo">Confirmar acción</h2>
<p id="dialogo-mensaje"></p>
<div class="dialogo-acciones">
<button class="boton boton-secundario" type="button" data-confirm-cancel>Cancelar</button>
<button class="boton" type="button" data-confirm-accept>Confirmar</button>
</div>
</div>
</dialog>
<section class="cookie-banner" id="cookie-banner" aria-labelledby="cookie-title" aria-describedby="cookie-description" hidden>
<div class="cookie-copy">
<p class="eyebrow">Privacidad y preferencias</p>
<h2 id="cookie-title">Tu privacidad importa</h2>
<p id="cookie-description">Usamos cookies técnicas necesarias para mantener tu sesión y proteger formularios. No usamos cookies publicitarias ni de seguimiento y actualmente no hay cookies opcionales. Tu elección solo recuerda esta preferencia en este navegador.</p>
<a class="enlace" href="{{ route('privacidad') }}">Conoce nuestra política de privacidad</a>
</div>
<div class="cookie-actions">
<button class="boton" type="button" data-cookie-choice="accepted">Aceptar aviso</button>
<button class="boton boton-secundario" type="button" data-cookie-choice="essential">Continuar solo con necesarias</button>
</div>
</section>
<script src="{{ asset('js/interacciones.js') }}?v={{ filemtime(public_path('js/interacciones.js')) }}"></script>
@stack('scripts')
</body>
</html>
