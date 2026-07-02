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

        $data = $request->validate(['forma_pagamento' => 'required|string|max:50']);

        $agendamento->update(['status' => 'realizado', 'forma_pagamento' => $data['forma_pagamento']]);

        $this->registrarNoCaixa($agendamento);
        $this->registrarUsoPlano($agendamento);

        return redirect()->back()->with('success', 'Serviço marcado como realizado!');
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
        $ag->load('cliente.planos', 'servicos');
        $cp = $ag->cliente?->planos?->where('ativo', true)->first();
        if (!$cp) return;

        foreach ($ag->servicos as $servico) {
            ClientePlanoUso::create([
                'cliente_plano_id' => $cp->id,
                'agendamento_id' => $ag->id,
                'servico_id' => $servico->id,
                'usado_em' => now(),
            ]);
        }
    }

    private function registrarNoCaixa(Agendamento $agendamento)
    {
        $dataStr = Carbon::parse($agendamento->data)->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr)->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'data' => $dataStr,
                'saldo_inicial' => 0,
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $agendamento->total);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();

            CaixaMovimentacao::create([
                'caixa_id' => $caixa->id,
                'tipo' => 'entrada',
                'valor' => $agendamento->total,
                'descricao' => "Serviço realizado por {$agendamento->barbeiro->nome} - {$agendamento->cliente->nome}",
                'origem_type' => Agendamento::class,
                'origem_id' => $agendamento->id,
            ]);
        }
    }
}
