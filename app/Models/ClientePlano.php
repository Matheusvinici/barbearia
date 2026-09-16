<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientePlano extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'cliente_id',
        'plano_id',
        'data_inicio',
        'data_fim',
        'vencimento',
        'valor_pago',
        'forma_pagamento',
        'pago',
        'pago_em',
        'ativo',
        'observacoes',
        'cpf',
    ];

    protected $table = 'cliente_plano';

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'vencimento' => 'date',
            'valor_pago' => 'decimal:2',
            'pago' => 'boolean',
            'pago_em' => 'datetime',
            'ativo' => 'boolean',
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

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function usos()
    {
        return $this->hasMany(ClientePlanoUso::class);
    }

    // Cotas restantes por serviço
    public function getSaldoAttribute()
    {
        if (!$this->relationLoaded('plano')) {
            $this->load('plano.quotas');
        }
        if (!$this->relationLoaded('usos')) {
            $this->load('usos');
        }
        return $this->plano->quotas->map(function ($q) {
            $usos = $this->usos->where('servico_id', $q->servico_id)->count();
            return [
                'servico_id' => $q->servico_id,
                'contratada' => $q->quantidade,
                'usada' => $usos,
                'restante' => max(0, $q->quantidade - $usos),
            ];
        });
    }

    public function getValidadeAttribute()
    {
        return $this->vencimento ?? $this->data_fim;
    }

    public function getExpiradoAttribute(): bool
    {
        $v = $this->validade;
        if (!$v) return false;
        return $v->isPast();
    }

    public function getDiasParaVencerAttribute(): ?int
    {
        $v = $this->validade;
        if (!$v) return null;
        return (int) now()->startOfDay()->diffInDays($v, false);
    }

    public function getTotalContratadaAttribute()
    {
        return $this->plano?->quotas->sum('quantidade') ?? 0;
    }

    public function getTotalUsadaAttribute()
    {
        return $this->usos->count();
    }

    public function getTotalRestanteAttribute()
    {
        return max(0, $this->total_contratada - $this->total_usada);
    }

    public function isServicoDentroDaCota(int $servicoId, ?int $ignoreAgendamentoId = null): bool
    {
        if ($this->expirado || !$this->pago || !$this->ativo) return false;
        $quota = $this->plano?->quotas->where('servico_id', $servicoId)->first();
        if (!$quota || $quota->quantidade <= 0) return false;
        $usosCount = $this->usos->where('servico_id', $servicoId)->count();
        // Se já existe uso para o agendamento sendo verificado, desconta 1 (não contar o próprio)
        if ($ignoreAgendamentoId) {
            $temUsoDoAgendamento = $this->usos->where('agendamento_id', $ignoreAgendamentoId)->where('servico_id', $servicoId)->isNotEmpty();
            if ($temUsoDoAgendamento) $usosCount--; // não contar o próprio agendamento
        }
        return $usosCount < $quota->quantidade;
    }

    public function getCotaRestanteParaServico(int $servicoId): int
    {
        $quota = $this->plano?->quotas->where('servico_id', $servicoId)->first();
        if (!$quota) return 0;
        $usada = $this->usos->where('servico_id', $servicoId)->count();
        return max(0, $quota->quantidade - $usada);
    }
}
