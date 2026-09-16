<?php

namespace App\Http\Controllers\Barbeiro;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\ClientePlanoUso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index()
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        $query = Agendamento::with('cliente', 'servicos', 'barbearia');

        if ($barbeiro->hasRole('proprietario')) {
            $barbearias = Barbearia::whereHas('barbeiros', function ($q) use ($barbeiro) {
                $q->where('barbeiros.id', $barbeiro->id);
            })->orWhereIn('id', function ($q) use ($barbeiro) {
                $q->select('parent_id')->from('barbearias')
                  ->whereIn('id', function ($q2) use ($barbeiro) {
                      $q2->select('barbearia_id')->from('barbeiros')->where('id', $barbeiro->id);
                  });
            })->pluck('id');

            $query->whereIn('barbearia_id', $barbearias);
        } else {
            $query->where('barbeiro_id', $barbeiro->id);
        }

        $agendamentos = $query->whereDate('data', '>=', Carbon::today()->subDay())
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->paginate(20);

        return view('barbeiro.agendamentos.index', compact('agendamentos'));
    }

    public function confirmar(Request $request, Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if (!$barbeiro->hasRole('proprietario') && $agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        if ($agendamento->status !== 'pendente') {
            return redirect()->back()->with('error', 'Agendamento não está pendente.');
        }

        $agendamento->update(['status' => 'confirmado']);

        return redirect()->back()->with('success', 'Presença confirmada!');
    }

    public function realizar(Request $request, Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if (!$barbeiro->hasRole('proprietario') && $agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        if (!in_array($agendamento->status, ['confirmado', 'pendente'])) {
            return redirect()->back()->with('error', 'Agendamento não pode ser marcado como realizado.');
        }

        $data = $request->validate([
            'forma_pagamento' => 'required|string|max:50',
            'usar_plano' => 'nullable|boolean',
            'cliente_plano_id' => 'nullable|exists:cliente_plano,id',
        ]);
        $usarPlano = $request->boolean('usar_plano', $agendamento->usar_plano);
        if (($data['forma_pagamento'] ?? null) === 'Plano') $usarPlano = true;
        $clientePlanoId = $data['cliente_plano_id'] ?? $agendamento->cliente_plano_id;
        if ($usarPlano && !$clientePlanoId) $clientePlanoId = $agendamento->plano_info?->id;

        $agendamento->update([
            'status' => 'realizado',
            'forma_pagamento' => $data['forma_pagamento'],
            'usar_plano' => $usarPlano,
            'cliente_plano_id' => $usarPlano ? $clientePlanoId : $agendamento->cliente_plano_id,
        ]);

        $ag = $agendamento->fresh()->load(['servicos', 'cliente', 'clientePlano.plano', 'clientePlano.usos']);
        if ($usarPlano) {
            $resumo = $ag->resumo_plano;
            if ($resumo['tem_plano'] && !$resumo['expirado'] && $resumo['pago']) {
                if ($resumo['dentro']) {
                    // sem cobrança
                } else {
                    $valorExcedente = (float) $resumo['valor_excedente'];
                    if ($valorExcedente > 0) {
                        $this->registrarNoCaixa($ag, $valorExcedente, "Serviço realizado (excedente plano {$resumo['plano_nome']}) - {$ag->cliente->nome}");
                    }
                }
            } else {
                $this->registrarNoCaixa($ag);
            }
        } else {
            $this->registrarNoCaixa($ag);
        }
        if ($usarPlano) $this->registrarUsoPlano($ag);

        $msg = 'Serviço marcado como realizado!';
        if ($usarPlano && isset($resumo) && $resumo['tem_plano'] && !$resumo['expirado'] && $resumo['pago']) {
            if ($resumo['dentro']) $msg = 'Serviço realizado com plano (sem cobrança, dentro da cota).';
            elseif ($resumo['valor_excedente'] > 0) $msg = 'Serviço realizado! Excedente de R$ ' . number_format($resumo['valor_excedente'], 2, ',', '.') . ' lançado no caixa.';
        }
        return redirect()->back()->with('success', $msg);
    }

    public function cancelar(Request $request, Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if (!$barbeiro->hasRole('proprietario') && $agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Agendamento cancelado.');
    }

    private function registrarUsoPlano(Agendamento $ag)
    {
        $ag->load(['cliente.planos', 'servicos', 'clientePlano']);
        $cp = $ag->clientePlano ?? $ag->cliente?->planos?->where('ativo', true)->first();
        if (!$cp) return;
        if (ClientePlanoUso::where('agendamento_id', $ag->id)->exists()) return;
        foreach ($ag->servicos as $servico) {
            ClientePlanoUso::create([
                'cliente_plano_id' => $cp->id,
                'agendamento_id' => $ag->id,
                'servico_id' => $servico->id,
                'usado_em' => now(),
            ]);
        }
    }

    private function registrarNoCaixa(Agendamento $agendamento, ?float $valorOverride = null, ?string $descricaoOverride = null)
    {
        $valor = $valorOverride ?? (float) $agendamento->total;
        if ($valor <= 0) return;
        $descricao = $descricaoOverride ?? "Serviço realizado por {$agendamento->barbeiro->nome} - {$agendamento->cliente->nome}";
        $dataStr = Carbon::parse($agendamento->data)->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr);
        if ($agendamento->barbearia_id) {
            $caixa->where('barbearia_id', $agendamento->barbearia_id);
        }
        $caixa = $caixa->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'barbearia_id' => $agendamento->barbearia_id,
                'data' => $dataStr,
                'saldo_inicial' => 0,
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $valor);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();
        }

        CaixaMovimentacao::create([
            'barbearia_id' => $caixa->barbearia_id,
            'caixa_id' => $caixa->id,
            'tipo' => 'entrada',
            'valor' => $valor,
            'descricao' => $descricao,
            'origem_type' => Agendamento::class,
            'origem_id' => $agendamento->id,
        ]);
    }
}
