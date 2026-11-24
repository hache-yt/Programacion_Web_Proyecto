<?php

use App\Http\Controllers\AdministracionController;
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\RiegoController;
use App\Http\Controllers\SistemaRiegoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])
    ->middleware(['active', 'password.change.required'])
    ->name('inicio');

Route::view('privacidad', 'informacion.privacidad')->name('privacidad');
Route::view('terminos', 'informacion.terminos')->name('terminos');
Route::view('ayuda', 'informacion.ayuda')->name('ayuda');
Route::view('acerca-de', 'informacion.acerca')->name('acerca');
Route::view('contacto', 'informacion.contacto')->name('contacto');

Route::get('asistente/index.html', function () {
    return response()->file(base_path('riego-chatbot-infinityfree/public/index.html'));
})->name('chatbot.index');

Route::post('asistente/chat.php', function () {
    require base_path('riego-chatbot-infinityfree/public/chat.php');
})->withoutMiddleware(ValidateCsrfToken::class)
    ->middleware('throttle:10,1')
    ->name('chatbot.message');

Route::middleware('guest')->group(function () {
    Route::get('ingresar', [AuthController::class, 'formLogin'])->name('login');
    Route::post('ingresar', [AuthController::class, 'login']);
    Route::get('registro', [AuthController::class, 'formRegistro'])->name('registro');
    Route::post('registro', [AuthController::class, 'registro']);
    Route::get('recuperar-contrasena', [AuthController::class, 'formRecuperarPassword'])->name('password.request');
    Route::post('recuperar-contrasena', [AuthController::class, 'enviarEnlaceRecuperacion'])->name('password.email');
    Route::get('restablecer-contrasena/{token}', [AuthController::class, 'formRestablecerPassword'])->name('password.reset');
    Route::post('restablecer-contrasena', [AuthController::class, 'restablecerPassword'])->name('password.reset.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('salir', [AuthController::class, 'logout'])->name('logout');
    Route::get('cuenta/cambiar-contrasena', [AuthController::class, 'formCambiarPassword'])->middleware(['active', 'password.change.required'])->name('password.change');
    Route::put('cuenta/cambiar-contrasena', [AuthController::class, 'cambiarPassword'])->middleware(['active', 'password.change.required'])->name('password.update');

    Route::middleware(['active', 'password.change.required'])->group(function () {
        Route::get('sistemas', [SistemaRiegoController::class, 'index'])->name('sistemas.index');
        Route::post('sistemas', [SistemaRiegoController::class, 'store'])->name('sistemas.store');
        Route::get('sistemas/{sistema}', [SistemaRiegoController::class, 'show'])->name('sistemas.show');
        Route::put('sistemas/{sistema}', [SistemaRiegoController::class, 'update'])->name('sistemas.update');
        Route::post('sistemas/{sistema}/mediciones', [SistemaRiegoController::class, 'storeMedicion'])->name('sistemas.mediciones.store');
        Route::put('sistemas/{sistema}/configuracion', [SistemaRiegoController::class, 'updateConfiguracion'])->name('sistemas.configuracion.update');
        Route::post('sistemas/{sistema}/iniciar', [SistemaRiegoController::class, 'iniciar'])->name('sistemas.iniciar');
        Route::post('sistemas/{sistema}/detener', [SistemaRiegoController::class, 'detener'])->name('sistemas.detener');
        Route::get('parcelas', [ParcelaController::class, 'index'])->name('parcelas.index');
        Route::resource('riegos', RiegoController::class)->except('show');
        Route::get('estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');
        Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::patch('alertas/{alerta}', [AlertaController::class, 'update'])->name('alertas.update');
        Route::delete('alertas/{alerta}', [AlertaController::class, 'destroy'])->name('alertas.destroy');
    });

    Route::middleware(['active', 'password.change.required', 'admin'])->group(function () {
        Route::get('administracion', [AdministracionController::class, 'index'])->name('admin.dashboard');
        Route::resource('usuarios', UsuarioController::class)->except('show');
        Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'estado'])->name('usuarios.estado');
    });
});
