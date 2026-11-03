@extends('layouts.app')
@section('titulo', 'Parcela principal')
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">Tu espacio de cultivo</p><h1>Parcela principal</h1><p>Riegos nuevos y estadísticas se registran en esta parcela. Tus datos históricos se mantienen intactos.</p></div>
</div>
@if ($esAdmin)
<div class="panel tabla-panel">
<div class="tabla-scroll">
<table>
<thead><tr><th>Cuenta</th><th>Parcela principal</th><th>Ubicación</th><th>Área</th><th>Cultivo</th></tr></thead>
<tbody>
@forelse ($parcelas as $parcela)
<tr>
<td><strong>{{ $parcela->usuario->name }}</strong></td>
<td>{{ $parcela->nombre }}</td>
<td>{{ $parcela->ubicacion ?: '—' }}</td>
<td>{{ $parcela->area_m2 ? number_format($parcela->area_m2, 2).' m²' : '—' }}</td>
<td>{{ $parcela->tipo_cultivo ?: '—' }}</td>
</tr>
@empty
<tr><td class="vacio" colspan="5"><strong>No hay parcelas registradas</strong>Las parcelas aparecerán cuando las cuentas registren actividad.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
@else
@forelse ($parcelas as $parcela)
<section class="panel">
<div class="tarjeta-top panel-heading">
<div><h2>{{ $parcela->nombre }}</h2><p class="panel-copy">Parcela única asociada a tu cuenta</p></div>
<span class="etiqueta etiqueta-ok">Principal</span>
</div>
<div class="detalle-parcela">
<div class="detalle-item"><span>Ubicación</span><strong>{{ $parcela->ubicacion ?: 'Sin especificar' }}</strong></div>
<div class="detalle-item"><span>Área</span><strong>{{ $parcela->area_m2 ? number_format($parcela->area_m2, 2).' m²' : 'Sin especificar' }}</strong></div>
<div class="detalle-item"><span>Tipo de cultivo</span><strong>{{ $parcela->tipo_cultivo ?: 'Sin especificar' }}</strong></div>
</div>
</section>
@empty
<div class="panel vacio"><strong>No hay una parcela principal</strong>Registra un riego para configurar automáticamente la parcela de tu cuenta.</div>
@endforelse
@endif
@endsection
