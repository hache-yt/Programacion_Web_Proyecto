<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('titulo', 'Riego Responsable') · Riego Responsable</title>
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
<header class="cabecera">
<a class="marca" href="{{ route('inicio') }}">Riego Responsable</a>
<nav aria-label="Navegación principal">
@auth
@foreach ([['inicio', 'Inicio', 'inicio'], ['riegos.create', 'Registrar riego', 'riegos.create'], ['riegos.index', 'Historial', 'riegos.index'], ['parcelas.index', 'Parcelas', 'parcelas.*'], ['estadisticas.index', 'Estadísticas', 'estadisticas.*'], ['alertas.index', 'Alertas', 'alertas.*']] as [$ruta, $texto, $patron])
<a href="{{ route($ruta) }}" @if (request()->routeIs($patron)) aria-current="page" @endif>{{ $texto }}</a>
@endforeach
@if (auth()->user()->esAdmin())
<a href="{{ route('usuarios.index') }}" @if (request()->routeIs('usuarios.*')) aria-current="page" @endif>Usuarios</a>
@endif
<form class="salir" method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit">Salir ({{ auth()->user()->name }})</button>
</form>
@else
<a href="{{ route('inicio') }}" @if (request()->routeIs('inicio')) aria-current="page" @endif>Inicio</a>
<a href="{{ route('login') }}" @if (request()->routeIs('login')) aria-current="page" @endif>Ingresar</a>
<a href="{{ route('registro') }}" @if (request()->routeIs('registro')) aria-current="page" @endif>Registrarse</a>
@endauth
</nav>
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
<footer class="pie">Programación Web – Universidad Continental, Cusco 2026</footer>
@stack('scripts')
</body>
</html>
