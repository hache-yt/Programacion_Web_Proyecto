<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('app.admin_development_password');
        if (! app()->environment('local') || ! is_string($password) || $password === '') {
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@riego.test'],
            [
                'name' => 'Administrador',
                'password' => $password,
                'rol' => 'administrador',
                'activo' => true,
                'debe_cambiar_password' => true,
            ]
        );
    }
}
