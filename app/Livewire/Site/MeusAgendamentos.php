<?php

namespace App\Livewire\Site;

use App\Models\Agendamento;
use App\Models\Cliente;
use Livewire\Component;

class MeusAgendamentos extends Component
{
    public $cliente;
    public $agendamentos;

    public function mount()
    {
        $clienteId = session('cliente_id');
        if (!$clienteId) {
            $this->redirect(route('site.login'), navigate: true);
            return;
        }

        $this->cliente = Cliente::find($clienteId);
        if (!$this->cliente) {
            $this->redirect(route('site.login'), navigate: true);
            return;
        }

        $this->agendamentos = Agendamento::where('cliente_id', $clienteId)
            ->with('barbeiro', 'servicos')
            ->orderBy('data', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.site.meus-agendamentos')
            ->layout('layouts.site');
    }
}
