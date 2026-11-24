@extends('layouts.app')
@section('titulo', $sistema->nombre)
@section('contenido')
<div class="page-heading">
<div><p class="eyebrow">{{ $sistema->parcela->usuario->name }} · {{ $sistema->parcela->nombre }}</p><h1>{{ $sistema->nombre }}</h1><p>{{ $sistema->descripcion ?: 'Panel de estado y configuración de este sistema.' }}</p></div>
<a class="boton boton-secundario" href="{{ route('sistemas.index') }}">Todos los sistemas</a>
</div>
<div class="system-status-grid">
<section class="panel system-status-card"><span>Estado del riego</span><strong class="irrigation-status">{{ $sistema->riego_activo ? 'ACTIVO' : 'CERRADO' }}</strong><small>El estado solo se actualizará al conectar y verificar un actuador.</small></section>
<section class="panel system-status-card"><span>Modo seleccionado</span><strong>{{ strtoupper($sistema->configuracion->modo ?? 'manual') }}</strong><small>Configuración independiente de este sistema.</small></section>
<section class="panel system-status-card"><span>Conexión del equipo</span><strong class="status-pill {{ $sistema->conexion_simulada ? 'status-test' : 'status-offline' }}">{{ $sistema->conexion_simulada ? 'ENCONTRADO (SIMULADO)' : 'NO CONECTADA' }}</strong>
@if ($sistema->conexion_simulada)
<small>{{ $sistema->identificador_dispositivo }} · {{ $sistema->tipo_conexion === 'wifi' ? 'Wi-Fi' : 'Cable de red' }} · {{ $sistema->direccion_ip }}</small>
@else
<small>Este proyecto aún no recibe datos de hardware.</small>
@endif
</section>
<section class="panel system-status-card"><span>Última lectura registrada</span><strong>{{ $ultimaLectura?->fecha_lectura?->format('d/m/Y H:i') ?? 'Sin lecturas' }}</strong>
@if ($ultimaLectura)
<small class="{{ $lecturaVencida ? 'stale-reading' : 'fresh-reading' }}">{{ $lecturaVencida ? 'Dato desactualizado (más de 30 minutos)' : 'Dato reciente; de prueba, no recibido de hardware' }}</small>
@else
<small>No se han registrado lecturas.</small>
@endif
</section>
</div>
@if ($errors->has('control'))
<p class="aviso aviso-error" role="alert">{{ $errors->first('control') }}</p>
@endif
<section class="panel system-controls">
<div class="grafico-cabecera"><div><h2>Control manual</h2><p>Las solicitudes se registran; no activan bombas ni válvulas sin integración.</p></div><span class="status-pill status-offline">Actuadores no conectados</span></div>
<div class="control-actions">
<form method="POST" action="{{ route('sistemas.iniciar', $sistema) }}" data-confirm="Se registrará una solicitud de inicio. No se activará una bomba: todavía no hay actuadores conectados." data-confirm-title="Confirmar solicitud">
@csrf
<button class="boton" type="submit" @disabled(($sistema->configuracion->modo ?? 'manual') !== 'manual')>INICIAR RIEGO</button>
</form>
<form method="POST" action="{{ route('sistemas.detener', $sistema) }}">
@csrf
<button class="boton boton-secundario" type="submit" @disabled(($sistema->configuracion->modo ?? 'manual') !== 'manual')>DETENER RIEGO</button>
</form>
</div>
</section>
<section class="panel">
<div class="panel-heading"><h2>Lecturas del sistema</h2><p class="panel-copy">Solo se guardan cuando se envía el formulario. Todas se identifican como datos de prueba.</p></div>
<div class="reading-card-grid">
@foreach (['humedad_suelo' => 'Humedad del suelo', 'temperatura' => 'Temperatura ambiente', 'humedad_ambiental' => 'Humedad ambiental'] as $tipo => $titulo)
@php($lectura = $medicionesPorTipo->get($tipo)?->first())
<article class="reading-card">
<h3>{{ $titulo }}</h3>
@if ($lectura)
<strong>{{ number_format($lectura->valor, 2) }} {{ $lectura->unidad }}</strong>
<span>{{ $lectura->fecha_lectura->format('d/m/Y H:i') }} · {{ $lectura->sensor_id ?: 'Sensor sin identificar' }}</span>
@if ($lectura->fecha_lectura->lt(now()->subMinutes(30)))<span class="stale-reading">Desactualizada</span>@endif
@if ($lectura->es_prueba)<span class="status-pill status-test">LECTURA DE PRUEBA</span>@endif
@else
<strong>Sin datos</strong><span>No se asumió un valor para esta lectura.</span>
@endif
</article>
@endforeach
</div>
</section>
<div class="panel-heading"><h2>Registrar lectura de prueba</h2><p class="panel-copy">No simula la conexión de sensores. Solo los tipos permitidos son humedad del suelo, temperatura ambiente y humedad ambiental. Las unidades y rangos se verifican en el servidor.</p></div>
<form class="formulario" method="POST" action="{{ route('sistemas.mediciones.store', $sistema) }}">
@csrf
<div class="form-grid">
<div class="campo"><label for="tipo">Tipo de lectura</label><select id="tipo" name="tipo" required><option value="">Selecciona un tipo</option><option value="humedad_suelo" @selected(old('tipo') === 'humedad_suelo')>Humedad del suelo</option><option value="temperatura" @selected(old('tipo') === 'temperatura')>Temperatura ambiente</option><option value="humedad_ambiental" @selected(old('tipo') === 'humedad_ambiental')>Humedad ambiental</option></select><small class="error" role="alert">{{ $errors->first('tipo') }}</small></div>
<div class="campo"><label for="valor">Valor</label><input id="valor" name="valor" type="number" step="any" value="{{ old('valor') }}" required><small class="error" role="alert">{{ $errors->first('valor') }}</small></div>
<div class="campo"><label for="unidad">Unidad</label><select id="unidad" name="unidad" required><option value="%" @selected(old('unidad') === '%')>Porcentaje (%)</option><option value="°C" @selected(old('unidad') === '°C')>Grados Celsius (°C)</option></select><small class="error" role="alert">{{ $errors->first('unidad') }}</small></div>
<div class="campo"><label for="sensor_id">Identificador de sensor <span>(opcional)</span></label><input id="sensor_id" name="sensor_id" maxlength="100" value="{{ old('sensor_id') }}"><small class="error" role="alert">{{ $errors->first('sensor_id') }}</small></div>
<div class="campo"><label for="fecha_lectura">Fecha y hora de lectura</label><input id="fecha_lectura" name="fecha_lectura" type="datetime-local" max="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('fecha_lectura', now()->format('Y-m-d\TH:i')) }}" required><small class="error" role="alert">{{ $errors->first('fecha_lectura') }}</small></div>
</div>
<div class="form-acciones"><button class="boton" type="submit">Guardar lectura de prueba</button></div>
</form>
</section>
<section class="panel">
<div class="panel-heading"><h2>Lecturas recientes</h2><p class="panel-copy">Fecha, sistema, sensor, unidad y origen de cada valor registrado.</p></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha y hora</th><th>Tipo</th><th>Valor</th><th>Sensor</th><th>Origen</th></tr></thead><tbody>
@forelse ($medicionesPorTipo->flatten()->sortByDesc('fecha_lectura')->take(20) as $lectura)
<tr><td>{{ $lectura->fecha_lectura->format('d/m/Y H:i') }}</td><td>{{ ['humedad_suelo' => 'Humedad del suelo', 'temperatura' => 'Temperatura ambiente', 'humedad_ambiental' => 'Humedad ambiental'][$lectura->tipo] ?? $lectura->tipo }}</td><td>{{ number_format($lectura->valor, 2) }} {{ $lectura->unidad }}</td><td>{{ $lectura->sensor_id ?: 'No identificado' }}</td><td>{{ $lectura->es_prueba ? 'Prueba manual' : 'Integración' }}</td></tr>
@empty
<tr><td class="vacio" colspan="5"><strong>Sin lecturas registradas</strong>Los valores aparecerán aquí al guardar una lectura.</td></tr>
@endforelse
</tbody></table></div>
</section>
<section class="panel">
<div class="panel-heading"><h2>Reglas por sistema</h2><p class="panel-copy">El modo automático solo almacena parámetros; no ejecuta riego hasta conectar un servicio.</p></div>
@php($configuracion = $sistema->configuracion)
<form class="formulario" method="POST" action="{{ route('sistemas.configuracion.update', $sistema) }}">
@csrf
@method('PUT')
<div class="form-grid">
<div class="campo"><label for="modo">Modo</label><select id="modo" name="modo"><option value="manual" @selected(old('modo', $configuracion->modo ?? 'manual') === 'manual')>Manual</option><option value="automatico" @selected(old('modo', $configuracion->modo ?? 'manual') === 'automatico')>Automático</option></select><small class="error" role="alert">{{ $errors->first('modo') }}</small></div>
<div class="campo"><label for="humedad_suelo_minima">Humedad mínima del suelo (%)</label><input id="humedad_suelo_minima" name="humedad_suelo_minima" type="number" min="0" max="100" step="0.01" value="{{ old('humedad_suelo_minima', $configuracion->humedad_suelo_minima ?? '') }}"><small class="error" role="alert">{{ $errors->first('humedad_suelo_minima') }}</small></div>
<div class="campo"><label for="humedad_suelo_maxima">Humedad máxima del suelo (%)</label><input id="humedad_suelo_maxima" name="humedad_suelo_maxima" type="number" min="0" max="100" step="0.01" value="{{ old('humedad_suelo_maxima', $configuracion->humedad_suelo_maxima ?? '') }}"><small class="error" role="alert">{{ $errors->first('humedad_suelo_maxima') }}</small></div>
<div class="campo"><label for="temperatura_relevante">Temperatura relevante (°C) <span>(opcional)</span></label><input id="temperatura_relevante" name="temperatura_relevante" type="number" min="-80" max="100" step="0.01" value="{{ old('temperatura_relevante', $configuracion->temperatura_relevante ?? '') }}"><small class="error" role="alert">{{ $errors->first('temperatura_relevante') }}</small></div>
<fieldset class="campo days-field"><legend>Días permitidos para riego</legend>
@php($diasSeleccionados = old('dias_permitidos', $configuracion->dias_permitidos ?? []))
@foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'] as $numeroDia => $dia)
<label class="day-option"><input type="checkbox" name="dias_permitidos[]" value="{{ $numeroDia }}" @checked(is_array($diasSeleccionados) && in_array($numeroDia, $diasSeleccionados, false))>{{ $dia }}</label>
@endforeach
<small class="error" role="alert">{{ $errors->first('dias_permitidos') }}</small>
</fieldset>
<div class="campo"><label for="hora_inicio">Horario permitido desde</label><input id="hora_inicio" name="hora_inicio" type="time" value="{{ old('hora_inicio', $configuracion->hora_inicio ?? '') }}"><small class="error" role="alert">{{ $errors->first('hora_inicio') }}</small></div>
<div class="campo"><label for="hora_fin">Horario permitido hasta</label><input id="hora_fin" name="hora_fin" type="time" value="{{ old('hora_fin', $configuracion->hora_fin ?? '') }}"><small class="error" role="alert">{{ $errors->first('hora_fin') }}</small></div>
<div class="campo"><label for="duracion_maxima_min">Duración máxima (minutos)</label><input id="duracion_maxima_min" name="duracion_maxima_min" type="number" min="1" max="1440" value="{{ old('duracion_maxima_min', $configuracion->duracion_maxima_min ?? '') }}"><small class="error" role="alert">{{ $errors->first('duracion_maxima_min') }}</small></div>
<div class="campo"><label for="pausa_minima_min">Pausa mínima entre ciclos (minutos)</label><input id="pausa_minima_min" name="pausa_minima_min" type="number" min="1" max="10080" value="{{ old('pausa_minima_min', $configuracion->pausa_minima_min ?? '') }}"><small class="error" role="alert">{{ $errors->first('pausa_minima_min') }}</small></div>
</div>
<div class="form-acciones"><button class="boton" type="submit">Guardar configuración</button></div>
</form>
</section>
<section class="panel">
<div class="panel-heading"><h2>Órdenes y cambios</h2><p class="panel-copy">Solicitudes y cambios registrados. Una orden no equivale a un riego ejecutado.</p></div>
<div class="tabla-scroll"><table><thead><tr><th>Fecha</th><th>Acción</th><th>Usuario</th><th>Resultado</th><th>Detalle</th></tr></thead><tbody>
@forelse ($comandos as $comando)
<tr><td>{{ $comando->solicitado_en->format('d/m/Y H:i') }}</td><td>{{ str_replace('_', ' ', ucfirst($comando->accion)) }}</td><td>{{ $comando->usuario?->name ?? 'Usuario no disponible' }}</td><td>{{ str_replace('_', ' ', $comando->resultado) }}</td><td>{{ $comando->detalle }}</td></tr>
@empty
<tr><td class="vacio" colspan="5"><strong>Sin órdenes ni cambios</strong>Las acciones futuras aparecerán aquí.</td></tr>
@endforelse
</tbody></table></div>
</section>
<section class="panel">
<div class="panel-heading"><h2>Editar identificación</h2></div>
<form class="system-create-form" method="POST" action="{{ route('sistemas.update', $sistema) }}">
@csrf
@method('PUT')
<div class="campo"><label for="editar-nombre">Nombre</label><input id="editar-nombre" name="nombre" maxlength="100" value="{{ old('nombre', $sistema->nombre) }}" required><small class="error" role="alert">{{ $errors->first('nombre') }}</small></div>
<div class="campo"><label for="editar-descripcion">Descripción</label><input id="editar-descripcion" name="descripcion" maxlength="1000" value="{{ old('descripcion', $sistema->descripcion) }}"><small class="error" role="alert">{{ $errors->first('descripcion') }}</small></div>
<button class="boton boton-secundario" type="submit">Guardar datos</button>
</form>
</section>
@endsection
