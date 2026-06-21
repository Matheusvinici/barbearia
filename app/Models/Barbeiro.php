<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Barbeiro extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'telefone',
        'foto',
        'comissao_percentual',
        'barbearia_id',
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

    protected $guard_name = 'barbeiro';

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function bloqueios()
    {
        return $this->hasMany(BloqueioAgenda::class);
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }

    public function horarios()
    {
        return $this->hasMany(BarbeiroHorario::class);
    }
}
