@extends('layouts.app')
@section('titulo', 'Administración')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Centro de control</p><h1>Panel administrativo</h1><p>Estado general de las cuentas y la actividad registrada en la plataforma.</p></div>
<a class="boton" href="{{ route('usuarios.create') }}">+ Registrar usuario</a>
</div>
<section class="tarjetas dashboard-cards" aria-label="Indicadores administrativos">
<article class="tarjeta"><div class="tarjeta-top"><h2>Usuarios registrados</h2><span class="tarjeta-icono">♙</span></div><p class="cifra">{{ number_format($totalUsuarios) }}</p><p class="tarjeta-detalle">Todas las cuentas del sistema</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Cuentas activas</h2><span class="tarjeta-icono">✓</span></div><p class="cifra">{{ number_format($usuariosActivos) }}</p><p class="tarjeta-detalle">Con acceso habilitado</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Cuentas inactivas</h2><span class="tarjeta-icono">⊘</span></div><p class="cifra">{{ number_format($usuariosInactivos) }}</p><p class="tarjeta-detalle">Sin acceso al sistema</p></article>
<article class="tarjeta"><div class="tarjeta-top"><h2>Riegos registrados</h2><span class="tarjeta-icono">◷</span></div><p class="cifra">{{ number_format($totalRiegos) }}</p><p class="tarjeta-detalle">{{ number_format($alertasPendientes) }} alertas pendientes</p></article>
</section>
<section class="panel dashboard-recent">
<div class="grafico-cabecera"><div><h2>Registros recientes del sistema</h2><p>Actividad real registrada por las cuentas</p></div><a class="enlace" href="{{ route('riegos.index') }}">Consultar registros →</a></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha</th><th>Usuario</th><th>Parcela</th><th>Agua utilizada</th><th>Duración</th></tr></thead><tbody>
@forelse ($riegosRecientes as $riego)
<tr><td><strong>{{ $riego->fecha->format('d/m/Y') }}</strong></td><td>{{ $riego->parcela->usuario->name }}</td><td>{{ $riego->parcela->nombre }}</td><td>{{ number_format($riego->cantidad_litros, 2) }} L</td><td>{{ $riego->duracion_min }} min</td></tr>
@empty
<tr><td class="vacio" colspan="5"><strong>No hay actividad registrada</strong>Los registros aparecerán cuando los usuarios guarden riegos.</td></tr>
@endforelse
</tbody></table></div>
</section>
<div class="admin-shortcuts">
<a class="quick-link panel" href="{{ route('usuarios.index') }}"><span class="quick-icon">♙</span><span><strong>Gestionar usuarios</strong><small>Roles, acceso y cuentas</small></span><b>→</b></a>
<a class="quick-link panel" href="{{ route('alertas.index') }}"><span class="quick-icon">♧</span><span><strong>Consultar alertas</strong><small>Revisar incidencias pendientes</small></span><b>→</b></a>
<a class="quick-link panel" href="{{ route('estadisticas.index') }}"><span class="quick-icon">⌁</span><span><strong>Estadísticas</strong><small>Consumo agregado registrado</small></span><b>→</b></a>
</div>
@endsection
