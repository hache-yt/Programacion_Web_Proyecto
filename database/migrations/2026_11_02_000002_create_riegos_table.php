<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riegos', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('parcela_id')->constrained('parcelas')->cascadeOnDelete();
            $tabla->date('fecha');
            $tabla->unsignedInteger('duracion_min');
            $tabla->decimal('cantidad_litros', 10, 2);
            $tabla->string('observaciones', 255)->nullable();
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riegos');
    }
};
