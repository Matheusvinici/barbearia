<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientePlano extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'plano_id',
        'data_inicio',
        'data_fim',
        'ativo',
        'observacoes',
    ];

    protected $table = 'cliente_plano';

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'ativo' => 'boolean',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function usos()
    {
        return $this->hasMany(ClientePlanoUso::class);
    }
}
