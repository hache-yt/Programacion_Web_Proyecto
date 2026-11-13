<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $this->call(AdminDevelopmentSeeder::class);

        $demoPassword = config('app.demo_user_password');
        if (! is_string($demoPassword) || $demoPassword === '') {
            return;
        }

        $demo = User::create([
            'name' => 'Usuario Demo',
            'email' => 'usuario@riego.test',
            'password' => $demoPassword,
            'rol' => 'usuario',
        ]);

        $parcela = $demo->parcelas()->create([
            'nombre' => 'Parcela Saylla 1',
            'ubicacion' => 'Saylla, Cusco',
            'area_m2' => 1200,
            'tipo_cultivo' => 'Maíz',
        ]);

        $registros = [
            [6, 40, 700, 'Riego por goteo'],
            [5, 20, 280, 'Riego manual'],
            [4, 45, 900, 'Riego por aspersión'],
            [3, 25, 310, 'Riego manual'],
            [2, 30, 650, 'Riego por goteo'],
            [1, 20, 300, 'Riego manual'],
            [0, 50, 1000, 'Riego por aspersión'],
        ];

        foreach ($registros as [$dias, $minutos, $litros, $nota]) {
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
