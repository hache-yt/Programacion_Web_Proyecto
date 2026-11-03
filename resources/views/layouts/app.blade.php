<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#173a32">
<title>@yield('titulo', 'Riego Responsable') · Riego Responsable</title>
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
<div class="app-shell">
<aside class="sidebar">
<a class="brand" href="{{ route('inicio') }}">
<span class="brand-mark" aria-hidden="true">
<svg viewBox="0 0 24 24" fill="none">
<path d="M12 2.8C9.4 6.4 5.8 10.1 5.8 14.2a6.2 6.2 0 0 0 12.4 0C18.2 10.1 14.6 6.4 12 2.8Z" stroke="currentColor" stroke-width="1.8"/>
<path d="M9.3 14.4a2.8 2.8 0 0 0 2.8 2.8" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
</svg>
</span>
<span><span class="brand-name">Riego Responsable</span><span class="brand-caption">Gestión hídrica</span></span>
</a>
<p class="nav-label">Espacio de trabajo</p>
<nav class="main-nav" aria-label="Navegación principal">
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
<p class="topbar-label">@yield('titulo', 'Riego Responsable')</p>
@auth
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
<footer class="pie">Programación Web · Universidad Continental, Cusco · 2026</footer>
</div>
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
<script src="{{ asset('js/interacciones.js') }}"></script>
@stack('scripts')
</body>
</html>
