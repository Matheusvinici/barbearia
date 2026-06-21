<?php

namespace App\Livewire;

use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\ClientePlanoUso;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AgendamentosTable extends Component
{
    public $data;
    public $barbeiroId;
    public $barbeariaId;

    public function mount()
    {
        $this->data = request('data', Carbon::today()->format('Y-m-d'));
        $this->barbeiroId = request('barbeiro_id');
        $this->barbeariaId = request('barbearia_id');
    }

    public function atualizarStatus($id, $status)
    {
        $validStatus = ['pendente', 'confirmado', 'realizado', 'cancelado', 'ausente'];
        if (!in_array($status, $validStatus)) return;

        $ag = Agendamento::findOrFail($id);
        $oldStatus = $ag->status;
        $ag->update(['status' => $status]);

        if ($status === 'realizado' && $oldStatus !== 'realizado') {
            $this->registrarNoCaixa($ag);
            if ($ag->usar_plano) {
                $this->registrarUsoPlano($ag);
            }
        }

        $this->dispatch('status-updated', id: $id, status: $status);
    }

    public function atualizarPagamento($id, $forma)
    {
        $validFormas = Agendamento::FORMAS_PAGAMENTO;
        if (!in_array($forma, $validFormas) && $forma !== null) return;

        Agendamento::findOrFail($id)->update(['forma_pagamento' => $forma]);
        $this->dispatch('pagamento-updated');
    }

    public function render()
    {
        $query = Agendamento::with(['barbeiro', 'cliente', 'servicos', 'cliente.planos' => function ($q) {
            $q->where('ativo', true)->with('plano.quotas');
        }, 'planoUso'])
            ->whereDate('data', $this->data);

        if ($this->barbeariaId) {
            $query->where('barbearia_id', $this->barbeariaId);
        }

        if ($this->barbeiroId) {
            $query->where('barbeiro_id', $this->barbeiroId);
        }

        $agendamentos = $query->orderBy('hora_inicio')->get();
        $barbeiros = Barbeiro::where('ativo', true)->get();
        $barbearias = Barbearia::orderBy('nome')->get();

        return view('livewire.agendamentos-table', compact('agendamentos', 'barbeiros', 'barbearias'));
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
        $dataStr = $agendamento->data instanceof Carbon
            ? $agendamento->data->format('Y-m-d')
            : Carbon::parse($agendamento->data)->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr)->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'data' => $dataStr,
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
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
                'descricao' => "Serviço realizado - {$agendamento->cliente->nome}",
                'origem_type' => Agendamento::class,
                'origem_id' => $agendamento->id,
                'user_id' => Auth::guard('web')->id(),
            ]);
        }
    }
}
