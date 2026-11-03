<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $tabla) {
            $tabla->id();
            $tabla->foreignId('riego_id')->constrained('riegos')->cascadeOnDelete();
            $tabla->string('mensaje', 200);
            $tabla->enum('estado', ['pendiente', 'atendida'])->default('pendiente');
            $tabla->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
