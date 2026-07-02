<?php

namespace App\Livewire\Site;

use App\Models\Agendamento;
use App\Models\Avaliacao;
use App\Models\Cliente;
use Livewire\Component;

class MeusAgendamentos extends Component
{
    public $cliente;
    public $agendamentos;
    public $avaliados = [];
    public $slug;

    public $avaliacao_rating = 5;
    public $avaliacao_comentario = '';
    public $avaliacao_agendamento_id = null;

    public function mount()
    {
        $clienteId = session('cliente_id');
        $this->cliente = $clienteId ? Cliente::find($clienteId) : null;

        if (!$this->cliente) {
            $this->redirect(route('site.login'));
            return;
        }

        $route = request()->route();
        $barbearia = $route->parameter('barbearia');
        if ($barbearia) {
            $this->slug = $barbearia instanceof \App\Models\Barbearia ? $barbearia->slug : $barbearia;
        }

        $this->carregarAgendamentos();
    }

    private function carregarAgendamentos()
    {
        $clienteId = $this->cliente?->id;
        if (!$clienteId) return;

        $this->avaliados = Avaliacao::whereIn('agendamento_id', function ($q) use ($clienteId) {
            $q->select('id')->from('agendamentos')->where('cliente_id', $clienteId);
        })->pluck('agendamento_id')->toArray();

        $this->agendamentos = Agendamento::where('cliente_id', $clienteId)
            ->with('barbeiro', 'servicos')
            ->orderBy('data', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
    }

    public function setRating($rating)
    {
        $this->avaliacao_rating = $rating;
    }

    public function abrirAvaliacao($agendamentoId)
    {
        $this->avaliacao_agendamento_id = $agendamentoId;
        $this->avaliacao_rating = 5;
        $this->avaliacao_comentario = '';
    }

    public function fecharAvaliacao()
    {
        $this->avaliacao_agendamento_id = null;
        $this->avaliacao_comentario = '';
    }

    public function salvarAvaliacao()
    {
        $this->validate([
            'avaliacao_rating' => 'required|integer|min:1|max:5',
            'avaliacao_comentario' => 'nullable|string|max:500',
        ]);

        $agendamento = Agendamento::find($this->avaliacao_agendamento_id);
        if (!$agendamento || $agendamento->cliente_id !== $this->cliente?->id) {
            session()->flash('error', 'Agendamento inválido.');
            return;
        }

        $jaExiste = Avaliacao::where('agendamento_id', $agendamento->id)->exists();

        if ($jaExiste) {
            session()->flash('error', 'Você já avaliou este agendamento.');
            $this->fecharAvaliacao();
            return;
        }

        Avaliacao::create([
            'barbearia_id' => $agendamento->barbearia_id,
            'cliente_id' => $this->cliente->id,
            'agendamento_id' => $agendamento->id,
            'cliente_nome' => $this->cliente->nome,
            'rating' => $this->avaliacao_rating,
            'comentario' => $this->avaliacao_comentario,
        ]);

        $this->fecharAvaliacao();
        $this->carregarAgendamentos();
        session()->flash('success', 'Avaliação enviada com sucesso! Obrigado.');
    }

    public function render()
    {
        return view('livewire.site.meus-agendamentos')
            ->layout('layouts.site');
    }
}
