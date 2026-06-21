<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'barbeiro_id',
        'cliente_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'status',
        'total',
        'forma_pagamento',
        'observacoes',
        'usar_plano',
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

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
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

    public function planoUso()
    {
        return $this->hasOne(ClientePlanoUso::class);
    }

    public function getPlanoInfoAttribute()
    {
        if (!$this->cliente) {
            return null;
        }
        if ($this->cliente->relationLoaded('planos')) {
            $cp = $this->cliente->planos->where('ativo', true)->first();
        } else {
            $cp = $this->cliente->planoAtivo;
        }
        return $cp;
    }

    public function getDentroDaCotaAttribute()
    {
        $cp = $this->plano_info;
        if (!$cp) {
            return false;
        }

        $servicoIds = $this->servicos->pluck('id');

        foreach ($servicoIds as $servicoId) {
            $quota = $cp->plano->quotas->where('servico_id', $servicoId)->first();
            if ($quota) {
                $usosCount = $cp->usos()
                    ->where('servico_id', $servicoId)
                    ->where('id', '!=', $this->planoUso?->id)
                    ->count();
                if ($usosCount >= $quota->quantidade) {
                    return false;
                }
            }
        }

        return true;
    }
}
