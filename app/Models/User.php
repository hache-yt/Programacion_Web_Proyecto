<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'rol'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class, 'usuario_id');
    }

    public function parcelaPrincipal(): Parcela
    {
        return $this->parcelas()->orderBy('id')->first()
            ?? $this->parcelas()->create(['nombre' => 'Parcela principal']);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'administrador';
    }
}
