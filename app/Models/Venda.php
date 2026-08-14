<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'cliente_id',
        'agendamento_id',
        'user_id',
        'total',
        'desconto',
        'forma_pagamento',
        'status',
        'finalizada_at',
        'observacoes',
    ];

    const STATUS = ['pendente', 'finalizada', 'cancelada'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'desconto' => 'decimal:2',
        ];
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'venda_produto')
            ->withPivot('quantidade', 'preco_unitario', 'subtotal')
            ->withTimestamps();
    }

    public function getItensCountAttribute()
    {
        return $this->produtos->sum(fn ($p) => $p->pivot->quantidade);
    }
}