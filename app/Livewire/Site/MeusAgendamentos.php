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

        $this->cliente = $clienteId ? Cliente::find($clienteId) : null;

        if (!$this->cliente) {
            return redirect()->to(route('site.login'));
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
