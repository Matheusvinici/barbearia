<?php

namespace App\Livewire\Admin;

use App\Models\Avaliacao;
use App\Models\Barbearia;
use Livewire\Component;

class Configuracoes extends Component
{
    public $barbeariaSlug;
    public $barbeariaAtual;
    public $avaliacoes;

    public function mount()
    {
        $route = request()->route();
        $this->barbeariaAtual = $route->parameter('barbearia');
        $this->barbeariaSlug = $this->barbeariaAtual?->slug;
    }

    public function carregarAvaliacoes()
    {
        $barbearia = $this->barbeariaAtual;
        $user = auth()->user();

        if ($barbearia) {
            $barbeariaIds = [$barbearia->id];
            if ($barbearia->isMatriz()) {
                $barbeariaIds = array_merge($barbeariaIds, $barbearia->filiais->pluck('id')->toArray());
            }
            $this->avaliacoes = Avaliacao::whereIn('barbearia_id', $barbeariaIds)
                ->with('barbearia')
                ->latest()
                ->get();
        } elseif ($user && $user->isSuperAdmin()) {
            $this->avaliacoes = Avaliacao::with('barbearia')->latest()->get();
        } else {
            $barbeariaIds = $user?->ownedBarbearias->pluck('id')->toArray() ?? [];
            $this->avaliacoes = Avaliacao::whereIn('barbearia_id', $barbeariaIds)
                ->with('barbearia')
                ->latest()
                ->get();
        }
    }

    public function responder($avaliacaoId, $resposta)
    {
        $this->validate(['resposta' => 'required|string|max:1000']);

        $avaliacao = Avaliacao::findOrFail($avaliacaoId);
        $avaliacao->update([
            'resposta' => $resposta,
            'responded_at' => now(),
        ]);

        $this->carregarAvaliacoes();
    }

    public function render()
    {
        return view('livewire.admin.configuracoes')
            ->layout('layouts.app');
    }
}
