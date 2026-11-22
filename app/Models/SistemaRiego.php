<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SistemaRiego extends Model
{
    protected $table = 'sistemas_riego';

    protected $fillable = [
        'parcela_id',
        'nombre',
        'descripcion',
        'tipo_conexion',
        'direccion_ip',
        'identificador_dispositivo',
        'conexion_simulada',
    ];

    protected function casts(): array
    {
        return [
            'riego_activo' => 'boolean',
            'conexion_simulada' => 'boolean',
        ];
    }

    public function parcela(): BelongsTo
    {
        return $this->belongsTo(Parcela::class);
    }

    public function mediciones(): HasMany
    {
        return $this->hasMany(Medicion::class, 'sistema_riego_id');
    }

    public function ultimaMedicion(): HasOne
    {
        return $this->hasOne(Medicion::class, 'sistema_riego_id')->latestOfMany('fecha_lectura');
    }

    public function configuracion(): HasOne
    {
        return $this->hasOne(Configuracion::class, 'sistema_riego_id');
    }

    public function comandos(): HasMany
    {
        return $this->hasMany(Comando::class, 'sistema_riego_id');
    }

    public function riegos(): HasMany
    {
        return $this->hasMany(Riego::class, 'sistema_riego_id');
    }

    public function scopeVisiblesPara(Builder $consulta, User $usuario): Builder
    {
        return $usuario->esAdmin()
            ? $consulta
            : $consulta->whereHas('parcela', fn (Builder $q) => $q->where('usuario_id', $usuario->id));
    }

    public function perteneceA(User $usuario): bool
    {
        return $usuario->esAdmin() || $this->parcela()->where('usuario_id', $usuario->id)->exists();
    }
}
