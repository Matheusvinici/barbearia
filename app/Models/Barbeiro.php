<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Barbeiro extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'telefone',
        'foto',
        'comissao_percentual',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
            'comissao_percentual' => 'decimal:2',
        ];
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function bloqueios()
    {
        return $this->hasMany(BloqueioAgenda::class);
    }
}
