@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
<section class="portada">
<h1>Sabe cuánta agua usas al regar</h1>
<p>Registra cada riego, revisa tu historial y detecta consumos elevados para ahorrar agua.</p>
@auth
<a class="boton" href="{{ route('riegos.create') }}">Registrar un riego</a>
@else
<a class="boton" href="{{ route('registro') }}">Crear una cuenta</a>
@endauth
</section>
@if ($resumen)
<section class="tarjetas" aria-label="Resumen">
<article class="tarjeta">
<h2>Parcelas</h2>
<p class="cifra">{{ $resumen['parcelas'] }}</p>
</article>
<article class="tarjeta">
<h2>Riegos registrados</h2>
<p class="cifra">{{ $resumen['riegos'] }}</p>
</article>
<article class="tarjeta">
<h2>Litros en 7 días</h2>
<p class="cifra">{{ number_format($resumen['litrosSemana'], 2) }}</p>
</article>
<article class="tarjeta">
<h2>Alertas pendientes</h2>
<p class="cifra">{{ $resumen['alertas'] }}</p>
</article>
</section>
@else
<section class="tarjetas" aria-label="Qué puedes hacer">
<article class="tarjeta">
<h2>Registra</h2>
<p>Anota fecha, duración y litros de cada riego.</p>
</article>
<article class="tarjeta">
<h2>Consulta</h2>
<p>Revisa tu historial por parcela y por fecha.</p>
</article>
<article class="tarjeta">
<h2>Decide</h2>
<p>Estadísticas y alertas de consumo elevado.</p>
</article>
</section>
@endif
@endsection
