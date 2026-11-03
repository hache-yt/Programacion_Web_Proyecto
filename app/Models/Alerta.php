<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = ['riego_id', 'mensaje', 'estado'];

    public function riego(): BelongsTo
    {
        return $this->belongsTo(Riego::class);
    }

    public function scopeVisiblesPara(Builder $consulta, User $usuario): Builder
    {
        return $usuario->esAdmin()
            ? $consulta
            : $consulta->whereHas('riego.parcela', fn ($q) => $q->where('usuario_id', $usuario->id));
    }
}
