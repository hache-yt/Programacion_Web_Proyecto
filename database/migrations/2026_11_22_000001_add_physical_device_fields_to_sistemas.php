<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sistemas_riego', function (Blueprint $table) {
            $table->string('tipo_conexion', 20)->nullable()->after('descripcion');
            $table->string('direccion_ip', 45)->nullable()->after('tipo_conexion');
            $table->string('identificador_dispositivo', 100)->nullable()->after('direccion_ip');
            $table->boolean('conexion_simulada')->default(false)->after('identificador_dispositivo');
        });
    }

    public function down(): void
    {
        Schema::table('sistemas_riego', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_conexion',
                'direccion_ip',
                'identificador_dispositivo',
                'conexion_simulada',
            ]);
        });
    }
};
