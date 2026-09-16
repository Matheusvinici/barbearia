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
        'cliente_plano_id',
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
        'barber_notified_at',
        'lembrete_1h_at',
        'lembrete_30min_at',
        'lembrete_15min_at',
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

    public function clientePlano()
    {
        return $this->belongsTo(ClientePlano::class, 'cliente_plano_id');
    }

    public function getPlanoInfoAttribute()
    {
        if ($this->clientePlano) {
            return $this->clientePlano;
        }
        if (!$this->cliente) {
            return null;
        }
        // Prioridade: relação carregada com filtro de validade
        if ($this->cliente->relationLoaded('planos')) {
            $cp = $this->cliente->planos->where('ativo', true)->first();
            if ($cp && $cp->validade && $cp->validade->isPast()) {
                // se expirado, tenta outro válido
                $cp = $this->cliente->planos->where('ativo', true)->filter(fn($p) => !$p->expirado)->first() ?? $cp;
            }
        } else {
            $cp = $this->cliente->planoAtivo;
            // if available, try expiration check
            if ($cp && $cp->expirado) {
                $cp = $this->cliente->planos()->where('ativo', true)->with('plano.quotas')->get()->filter(fn($p)=> !$p->expirado)->first() ?? $cp;
            }
        }
        return $cp;
    }

    public function getDentroDaCotaAttribute()
    {
        $cp = $this->plano_info;
        if (!$cp) {
            return false;
        }
        if ($cp->expirado || !$cp->pago) return false;
        $servicoIds = $this->servicos->pluck('id');
        if ($servicoIds->isEmpty()) return true;
        foreach ($servicoIds as $servicoId) {
            if (!$cp->isServicoDentroDaCota($servicoId, $this->planoUso?->id ? $this->id : null)) {
                return false;
            }
        }
        return true;
    }

    public function getServicosStatusAttribute(): array
    {
        $cp = $this->plano_info;
        $result = [];
        foreach ($this->servicos as $servico) {
            $dentro = false;
            $motivo = '';
            $restante = 0;
            $quotaQtd = 0;
            if (!$cp) {
                $motivo = 'sem plano';
            } elseif ($cp->expirado) {
                $motivo = 'plano vencido';
            } elseif (!$cp->pago) {
                $motivo = 'plano não pago';
            } else {
                $quota = $cp->plano->quotas->where('servico_id', $servico->id)->first();
                $quotaQtd = $quota?->quantidade ?? 0;
                $restante = $cp->getCotaRestanteParaServico($servico->id);
                if (!$quota || $quota->quantidade <= 0) {
                    $motivo = 'não incluso no plano';
                } elseif ($restante <= 0) {
                    $motivo = 'cota esgotada';
                } else {
                    $dentro = true;
                    $motivo = 'dentro da cota';
                }
            }
            $result[] = [
                'servico_id' => $servico->id,
                'servico_nome' => $servico->nome,
                'preco' => (float) ($servico->pivot->preco_praticado ?? $servico->preco),
                'dentro' => $dentro,
                'motivo' => $motivo,
                'quota_qtd' => $quotaQtd,
                'restante' => $restante,
            ];
        }
        return $result;
    }

    public function getServicosExcedentesAttribute()
    {
        return collect($this->servicos_status)->where('dentro', false);
    }

    public function getServicosDentroAttribute()
    {
        return collect($this->servicos_status)->where('dentro', true);
    }

    public function getValorExcedenteAttribute(): float
    {
        return (float) collect($this->servicos_status)->where('dentro', false)->sum('preco');
    }

    public function getValorDentroAttribute(): float
    {
        return (float) collect($this->servicos_status)->where('dentro', true)->sum('preco');
    }

    public function getResumoPlanoAttribute(): array
    {
        $cp = $this->plano_info;
        $total = (float) ($this->total ?? $this->servicos->sum(fn($s)=> $s->pivot->preco_praticado ?? $s->preco));
        $excedente = $this->valor_excedente;
        $coberto = $this->valor_dentro;
        $dentro = $this->dentro_da_cota;
        $temPlano = (bool) $cp;
        return [
            'tem_plano' => $temPlano,
            'plano_nome' => $cp?->plano->nome,
            'expirado' => $cp?->expirado ?? false,
            'pago' => $cp?->pago ?? false,
            'dentro' => $dentro,
            'servicos_status' => $this->servicos_status,
            'valor_total' => $total,
            'valor_excedente' => $excedente,
            'valor_coberto' => $coberto,
            'qtd_excedente' => $this->servicos_excedentes->count(),
            'qtd_dentro' => $this->servicos_dentro->count(),
        ];
    }
}
