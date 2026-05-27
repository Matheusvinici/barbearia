<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
