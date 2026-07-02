<?php

namespace App\Livewire\Admin;

use App\Models\Avaliacao;
use Livewire\Component;

class AvaliacoesList extends Component
{
    public $avaliacoes = [];
    public $barbearia;
    public $resposta = '';
    public $editandoId = null;
    public $editandoTexto = '';

    public function mount($barbearia = null)
    {
        $this->barbearia = $barbearia;
        $this->carregar();
    }

    public function carregar()
    {
        $user = auth()->user();

        if ($this->barbearia) {
            $barbeariaIds = [$this->barbearia->id];
            if ($this->barbearia->isMatriz()) {
                $barbeariaIds = array_merge($barbeariaIds, $this->barbearia->filiais->pluck('id')->toArray());
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

    public function responder($avaliacaoId)
    {
        $this->validate(['resposta' => 'required|string|max:1000']);

        $avaliacao = Avaliacao::findOrFail($avaliacaoId);
        $avaliacao->update([
            'resposta' => $this->resposta,
            'responded_at' => now(),
        ]);

        $this->resposta = '';
        $this->carregar();
    }

    public function editarResposta($avaliacaoId)
    {
        $avaliacao = Avaliacao::findOrFail($avaliacaoId);
        $this->editandoId = $avaliacaoId;
        $this->editandoTexto = $avaliacao->resposta;
    }

    public function salvarEdicao($avaliacaoId)
    {
        $this->validate(['editandoTexto' => 'required|string|max:1000']);

        Avaliacao::where('id', $avaliacaoId)->update([
            'resposta' => $this->editandoTexto,
            'responded_at' => now(),
        ]);

        $this->editandoId = null;
        $this->editandoTexto = '';
        $this->carregar();
    }

    public function cancelarEdicao()
    {
        $this->editandoId = null;
        $this->editandoTexto = '';
    }

    public function excluirResposta($avaliacaoId)
    {
        Avaliacao::where('id', $avaliacaoId)->update([
            'resposta' => null,
            'responded_at' => null,
        ]);

        $this->carregar();
    }

    public function render()
    {
        return view('livewire.admin.avaliacoes-list');
    }
}
