<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activo')->default(true);
            $table->boolean('debe_cambiar_password')->default(false);
        });

        DB::table('users')
            ->where('email', 'admin@riego.test')
            ->where('rol', 'administrador')
            ->update(['debe_cambiar_password' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activo', 'debe_cambiar_password']);
        });
    }
};
