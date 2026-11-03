<?php

use App\Http\Controllers\AlertaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\RiegoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [InicioController::class, 'index'])->name('inicio');

Route::middleware('guest')->group(function () {
    Route::get('ingresar', [AuthController::class, 'formLogin'])->name('login');
    Route::post('ingresar', [AuthController::class, 'login']);
    Route::get('registro', [AuthController::class, 'formRegistro'])->name('registro');
    Route::post('registro', [AuthController::class, 'registro']);
});

Route::middleware('auth')->group(function () {
    Route::post('salir', [AuthController::class, 'logout'])->name('logout');
    Route::resource('parcelas', ParcelaController::class)->except('show');
    Route::resource('riegos', RiegoController::class)->except('show');
    Route::get('estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');
    Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::patch('alertas/{alerta}', [AlertaController::class, 'update'])->name('alertas.update');
    Route::delete('alertas/{alerta}', [AlertaController::class, 'destroy'])->name('alertas.destroy');
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});
