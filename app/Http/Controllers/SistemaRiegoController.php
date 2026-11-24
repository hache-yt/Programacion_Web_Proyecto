<?php

namespace App\Http\Controllers;

use App\Models\SistemaRiego;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SistemaRiegoController extends Controller
{
    public function index(Request $request): View
    {
        $sistemas = SistemaRiego::visiblesPara($request->user())
            ->with(['parcela.usuario', 'configuracion', 'ultimaMedicion'])
            ->orderBy('nombre')
            ->get();

        return view('sistemas.index', compact('sistemas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'registro_fisico' => ['nullable', 'in:0,1'],
            'tipo_conexion' => ['nullable', 'required_if:registro_fisico,1', 'in:wifi,ethernet'],
            'direccion_ip' => ['nullable', 'required_if:registro_fisico,1', 'ip', 'max:45'],
            'identificador_dispositivo' => ['nullable', 'required_if:registro_fisico,1', 'string', 'max:100'],
            'dispositivo_encontrado' => ['nullable', 'in:0,1'],
        ], [
            'nombre.required' => 'Ingresa el nombre del sistema.',
            'nombre.max' => 'El nombre admite hasta 100 caracteres.',
            'tipo_conexion.required_if' => 'Selecciona el tipo de conexión del equipo.',
            'direccion_ip.required_if' => 'Ingresa la dirección IP del equipo.',
            'direccion_ip.ip' => 'Ingresa una dirección IP válida.',
            'identificador_dispositivo.required_if' => 'Busca o ingresa el identificador del equipo.',
        ]);

        $registroFisico = ($datos['registro_fisico'] ?? '0') === '1';

        $datosSistema = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'tipo_conexion' => $registroFisico ? $datos['tipo_conexion'] : null,
            'direccion_ip' => $registroFisico ? $datos['direccion_ip'] : null,
            'identificador_dispositivo' => $registroFisico ? $datos['identificador_dispositivo'] : null,
            'conexion_simulada' => $registroFisico,
        ];

        $sistema = DB::transaction(function () use ($request, $datosSistema): SistemaRiego {
            $sistema = $request->user()->parcelaPrincipal()->sistemasRiego()->create($datosSistema);
            $sistema->configuracion()->create(['modo' => 'manual']);

            return $sistema;
        });

        $mensaje = $registroFisico
            ? 'Sistema físico registrado con una simulación de búsqueda. No se realizó una conexión real con el equipo.'
            : 'Sistema creado. La integración con sensores y actuadores aún no está conectada.';

        return redirect()->route('sistemas.show', $sistema)->with('ok', $mensaje);
    }

    public function show(Request $request, SistemaRiego $sistema): View
    {
        $this->autorizar($request, $sistema);
        $sistema->load(['parcela.usuario', 'configuracion']);

        $mediciones = $sistema->mediciones()
            ->orderByDesc('fecha_lectura')
            ->limit(100)
            ->get();
        $ultimaLectura = $mediciones->first();
        $medicionesPorTipo = $mediciones->groupBy('tipo');

        return view('sistemas.show', [
            'sistema' => $sistema,
            'medicionesPorTipo' => $medicionesPorTipo,
            'ultimaLectura' => $ultimaLectura,
            'comandos' => $sistema->comandos()->with('usuario')->latest('solicitado_en')->limit(20)->get(),
            'tiposLectura' => $this->tiposLectura(),
            'lecturaVencida' => $ultimaLectura && $ultimaLectura->fecha_lectura->lt(now()->subMinutes(30)),
        ]);
    }

    public function update(Request $request, SistemaRiego $sistema): RedirectResponse
    {
        $this->autorizar($request, $sistema);

        $sistema->update($request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]));

        return back()->with('ok', 'Datos del sistema actualizados.');
    }

    public function storeMedicion(Request $request, SistemaRiego $sistema): RedirectResponse
    {
        $this->autorizar($request, $sistema);

        $datos = $request->validate([
            'tipo' => ['required', 'in:humedad_suelo,temperatura,humedad_ambiental'],
            'valor' => ['required', 'numeric', 'between:-80,100000000'],
            'unidad' => ['required', 'string', 'max:24'],
            'sensor_id' => ['nullable', 'string', 'max:100'],
            'fecha_lectura' => ['required', 'date', 'before_or_equal:now'],
        ], [
            'tipo.in' => 'Selecciona un tipo de lectura válido.',
            'fecha_lectura.before_or_equal' => 'La fecha de lectura no puede ser futura.',
        ]);

        $this->validarUnidadYRango($datos, $sistema);

        $sistema->mediciones()->create($datos + ['es_prueba' => true]);

        return back()->with('ok', 'Lectura de prueba guardada. No proviene de un sensor conectado.');
    }

    public function updateConfiguracion(Request $request, SistemaRiego $sistema): RedirectResponse
    {
        $this->autorizar($request, $sistema);

        $datos = $request->validate([
            'modo' => ['required', 'in:manual,automatico'],
            'humedad_suelo_minima' => ['nullable', 'numeric', 'between:0,100', 'required_if:modo,automatico'],
            'humedad_suelo_maxima' => ['nullable', 'numeric', 'between:0,100', 'required_if:modo,automatico'],
            'temperatura_relevante' => ['nullable', 'numeric', 'between:-80,100'],
            'dias_permitidos' => ['required_if:modo,automatico', 'array', 'min:1'],
            'dias_permitidos.*' => ['integer', 'between:1,7'],
            'hora_inicio' => ['nullable', 'date_format:H:i', 'required_if:modo,automatico'],
            'hora_fin' => ['nullable', 'date_format:H:i', 'required_if:modo,automatico', 'after:hora_inicio'],
            'duracion_maxima_min' => ['nullable', 'integer', 'between:1,1440', 'required_if:modo,automatico'],
            'pausa_minima_min' => ['nullable', 'integer', 'between:1,10080', 'required_if:modo,automatico'],
        ], [
            'modo.in' => 'El modo debe ser manual o automático.',
            'humedad_suelo_maxima.gt' => 'El umbral máximo debe ser mayor que el mínimo.',
            'hora_fin.after' => 'La hora final debe ser posterior a la hora de inicio.',
        ]);

        if (
            isset($datos['humedad_suelo_minima'], $datos['humedad_suelo_maxima'])
            && $datos['humedad_suelo_maxima'] <= $datos['humedad_suelo_minima']
        ) {
            throw ValidationException::withMessages([
                'humedad_suelo_maxima' => 'El umbral máximo debe ser mayor que el mínimo.',
            ]);
        }

        DB::transaction(function () use ($request, $sistema, $datos): void {
            $sistemaBloqueado = SistemaRiego::query()->lockForUpdate()->findOrFail($sistema->id);
            $configuracion = $sistemaBloqueado->configuracion()->firstOrCreate(
                [],
                ['modo' => 'manual']
            );
            $modoAnterior = $configuracion->modo;
            $configuracion->update($datos);

            if ($modoAnterior !== $datos['modo']) {
                $sistemaBloqueado->comandos()->create([
                    'user_id' => $request->user()->id,
                    'accion' => 'cambio_modo',
                    'resultado' => 'registrado',
                    'detalle' => "Modo cambiado de {$modoAnterior} a {$datos['modo']}. La automatización no está conectada.",
                ]);
            }
        });

        return back()->with('ok', 'Configuración guardada. Las reglas automáticas quedan registradas, pero no se ejecutan sin integración.');
    }

    public function iniciar(Request $request, SistemaRiego $sistema): RedirectResponse
    {
        return $this->registrarOrden($request, $sistema, 'iniciar');
    }

    public function detener(Request $request, SistemaRiego $sistema): RedirectResponse
    {
        return $this->registrarOrden($request, $sistema, 'detener');
    }

    private function registrarOrden(Request $request, SistemaRiego $sistema, string $accion): RedirectResponse
    {
        $this->autorizar($request, $sistema);
        $resultado = DB::transaction(function () use ($request, $sistema, $accion): string {
            $sistemaBloqueado = SistemaRiego::query()->lockForUpdate()->findOrFail($sistema->id);
            $configuracion = $sistemaBloqueado->configuracion;

            if ($configuracion && $configuracion->modo !== 'manual') {
                return 'modo_automatico';
            }

            $duplicada = $sistemaBloqueado->comandos()
                ->where('accion', $accion)
                ->where('resultado', 'no_conectado')
                ->where('solicitado_en', '>=', now()->subMinutes(5))
                ->exists();

            if ($duplicada) {
                return 'duplicada';
            }

            $sistemaBloqueado->comandos()->create([
                'user_id' => $request->user()->id,
                'accion' => $accion,
                'resultado' => 'no_conectado',
                'detalle' => 'Orden registrada; no fue enviada ni ejecutada porque no hay una integración de actuadores conectada.',
            ]);

            return 'no_conectado';
        });

        if ($resultado === 'modo_automatico') {
            return back()->withErrors(['control' => 'Cambia a modo manual antes de solicitar una orden manual.']);
        }

        if ($resultado === 'duplicada') {
            return back()->with('ok', 'Ya existe una orden reciente sin conexión; no se duplicó.');
        }

        return back()->with('ok', 'Orden registrada como no conectada. El estado permanece CERRADO y no se activó ningún actuador.');
    }

    private function autorizar(Request $request, SistemaRiego $sistema): void
    {
        abort_unless($sistema->perteneceA($request->user()), 403);
    }

    private function validarUnidadYRango(array $datos, SistemaRiego $sistema): void
    {
        $unidadEsperada = match ($datos['tipo']) {
            'humedad_suelo', 'humedad_ambiental' => ['%'],
            'temperatura' => ['°C'],
        };

        if (! in_array($datos['unidad'], $unidadEsperada, true)) {
            throw ValidationException::withMessages(['unidad' => 'La unidad no corresponde al tipo de lectura.']);
        }

        $valor = (float) $datos['valor'];
        $maximo = match ([$datos['tipo'], $datos['unidad']]) {
            ['humedad_suelo', '%'], ['humedad_ambiental', '%'] => 100,
            ['temperatura', '°C'] => 100,
            default => null,
        };
        $minimo = $datos['tipo'] === 'temperatura' ? -80 : 0;

        if ($maximo !== null && ($valor < $minimo || $valor > $maximo)) {
            throw ValidationException::withMessages(['valor' => 'El valor está fuera del rango admitido para esta lectura.']);
        }
    }

    private function tiposLectura(): array
    {
        return [
            'humedad_suelo' => ['nombre' => 'Humedad del suelo', 'unidad' => '%'],
            'temperatura' => ['nombre' => 'Temperatura ambiente', 'unidad' => '°C'],
            'humedad_ambiental' => ['nombre' => 'Humedad ambiental', 'unidad' => '%'],
        ];
    }
}
