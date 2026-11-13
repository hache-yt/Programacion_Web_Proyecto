<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Riego extends Model
{
    protected $table = 'riegos';

    protected $fillable = ['parcela_id', 'fecha', 'duracion_min', 'cantidad_litros', 'observaciones'];

    protected $casts = [
        'fecha' => 'date',
        'cantidad_litros' => 'float',
    ];

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class);
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class);
    }

    public function scopeFiltrar(Builder $consulta, array $filtros): Builder
    {
        return $consulta
            ->when($filtros['fecha'] ?? null, fn ($q, $fecha) => $q->whereDate('fecha', $fecha))
            ->when($filtros['q'] ?? null, fn ($q, $busqueda) => $q->where('observaciones', 'like', "%{$busqueda}%"));
    }

    public function scopeVisiblesPara(Builder $consulta, User $usuario): Builder
    {
        return $usuario->esAdmin()
            ? $consulta
            : $consulta->whereHas('parcela', fn ($q) => $q->where('usuario_id', $usuario->id));
    }

    public function perteneceA(User $usuario): bool
    {
        return $this->parcela->perteneceA($usuario);
    }

    public function esElevado(): bool
    {
        return $this->cantidad_litros > config('riego.umbral_litros');
    }

    public function generarAlerta(): void
    {
        if (! $this->esElevado()) {
            return;
        }

        $this->alertas()->firstOrCreate(
            ['estado' => 'pendiente'],
            ['mensaje' => sprintf('Consumo elevado: %s L en %s', number_format($this->cantidad_litros, 2), $this->parcela->nombre)]
        );
    }
}
