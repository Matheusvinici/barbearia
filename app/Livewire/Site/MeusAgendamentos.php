<?php

namespace App\Livewire\Site;

use App\Models\Agendamento;
use App\Models\Cliente;
use Livewire\Component;

class MeusAgendamentos extends Component
{
    public $cliente;
    public $agendamentos;
    public $slug;

    public function mount()
    {
        $clienteId = session('cliente_id');

        $this->cliente = $clienteId ? Cliente::find($clienteId) : null;

        if (!$this->cliente) {
            $this->redirect(route('site.login'));
        }

        $route = request()->route();
        $barbearia = $route->parameter('barbearia');
        if ($barbearia) {
            $this->slug = $barbearia instanceof \App\Models\Barbearia ? $barbearia->slug : $barbearia;
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
