<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parcela extends Model
{
    protected $table = 'parcelas';

    protected $fillable = ['usuario_id', 'nombre', 'ubicacion', 'area_m2', 'tipo_cultivo'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function riegos(): HasMany
    {
        return $this->hasMany(Riego::class);
    }

    public function scopeVisiblesPara(Builder $consulta, User $usuario): Builder
    {
        return $usuario->esAdmin() ? $consulta : $consulta->where('usuario_id', $usuario->id);
    }

    public function perteneceA(User $usuario): bool
    {
        return $usuario->esAdmin() || $this->usuario_id === $usuario->id;
    }
}
