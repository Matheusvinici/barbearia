<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cliente extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'barbearia_id',
        'nome',
        'telefone',
        'email',
        'whatsapp_id',
        'observacoes',
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function planos()
    {
        return $this->hasMany(ClientePlano::class);
    }

    public function planoAtivo()
    {
        return $this->hasOne(ClientePlano::class)->where('ativo', true);
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }
}
