<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'nome',
        'descricao',
        'foto',
        'preco',
        'duracao_minutos',
        'ativo',
    ];

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function agendamentos()
    {
        return $this->belongsToMany(Agendamento::class, 'agendamento_servico')
            ->withPivot('preco_praticado')
            ->withTimestamps();
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }
}
