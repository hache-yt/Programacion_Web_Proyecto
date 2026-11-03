<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelas', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $tabla->string('nombre', 100);
            $tabla->string('ubicacion', 150)->nullable();
            $tabla->decimal('area_m2', 10, 2)->nullable();
            $tabla->string('tipo_cultivo', 80)->nullable();
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas');
    }
};
