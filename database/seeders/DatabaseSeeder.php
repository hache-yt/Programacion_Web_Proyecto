<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@riego.test',
            'password' => 'admin1234',
            'rol' => 'administrador',
        ]);

        $demo = User::create([
            'name' => 'Usuario Demo',
            'email' => 'usuario@riego.test',
            'password' => 'usuario1234',
            'rol' => 'usuario',
        ]);

        $saylla = $demo->parcelas()->create([
            'nombre' => 'Parcela Saylla 1',
            'ubicacion' => 'Saylla, Cusco',
            'area_m2' => 1200,
            'tipo_cultivo' => 'Maíz',
        ]);

        $plaza = $demo->parcelas()->create([
            'nombre' => 'Área verde Plaza',
            'ubicacion' => 'Cusco',
            'area_m2' => 400,
            'tipo_cultivo' => 'Césped',
        ]);

        $registros = [
            [$saylla, 6, 40, 700, 'Riego por goteo'],
            [$plaza, 5, 20, 280, 'Riego manual'],
            [$saylla, 4, 45, 900, 'Riego por aspersión'],
            [$plaza, 3, 25, 310, 'Riego manual'],
            [$saylla, 2, 30, 650, 'Riego por goteo'],
            [$plaza, 1, 20, 300, 'Riego manual'],
            [$saylla, 0, 50, 1000, 'Riego por aspersión'],
        ];

        foreach ($registros as [$parcela, $dias, $minutos, $litros, $nota]) {
            $riego = $parcela->riegos()->create([
                'fecha' => now()->subDays($dias)->toDateString(),
                'duracion_min' => $minutos,
                'cantidad_litros' => $litros,
                'observaciones' => $nota,
            ]);
            $riego->generarAlerta();
        }
    }
}
