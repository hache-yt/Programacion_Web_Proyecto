<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Riego;
use App\Models\User;
use Illuminate\View\View;

class AdministracionController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'totalUsuarios' => User::count(),
            'usuariosActivos' => User::where('activo', true)->count(),
            'usuariosInactivos' => User::where('activo', false)->count(),
            'totalRiegos' => Riego::count(),
            'alertasPendientes' => Alerta::where('estado', 'pendiente')->count(),
            'riegosRecientes' => Riego::with('parcela.usuario')
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->limit(8)
                ->get(),
        ]);
    }
}
