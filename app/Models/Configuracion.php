<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuracion extends Model
{
    protected $table = 'configuracions';

    protected $fillable = [
        'sistema_riego_id',
        'modo',
        'humedad_suelo_minima',
        'humedad_suelo_maxima',
        'temperatura_relevante',
        'dias_permitidos',
        'hora_inicio',
        'hora_fin',
        'duracion_maxima_min',
        'pausa_minima_min',
    ];

    protected function casts(): array
    {
        return [
            'humedad_suelo_minima' => 'float',
            'humedad_suelo_maxima' => 'float',
            'temperatura_relevante' => 'float',
            'dias_permitidos' => 'array',
        ];
    }

    public function sistemaRiego(): BelongsTo
    {
        return $this->belongsTo(SistemaRiego::class, 'sistema_riego_id');
    }
}
