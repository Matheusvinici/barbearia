<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbeiro_id',
        'cliente_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'total',
        'forma_pagamento',
        'observacoes',
        'created_by',
        'origem',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'hora_inicio' => 'datetime:H:i',
            'hora_fim' => 'datetime:H:i',
            'total' => 'decimal:2',
        ];
    }

    const FORMAS_PAGAMENTO = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Pix', 'Outro'];

    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'agendamento_servico')
            ->withPivot('preco_praticado')
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
