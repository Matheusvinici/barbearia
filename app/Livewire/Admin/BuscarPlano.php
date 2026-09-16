<?php

namespace App\Livewire\Admin;

use App\Models\Plano;
use Livewire\Component;

class BuscarPlano extends Component
{
    public $search = '';
    public $plano_id;

    public function mount($plano_id = null)
    {
        if ($plano_id) {
            $this->plano_id = $plano_id;
            $p = Plano::find($plano_id);
            if ($p) {
                $this->search = $p->nome . ' - R$ ' . number_format($p->valor, 2, ',', '.');
            }
        } elseif (old('plano_id')) {
            $this->plano_id = old('plano_id');
            $p = Plano::find($this->plano_id);
            if ($p) {
                $this->search = $p->nome . ' - R$ ' . number_format($p->valor, 2, ',', '.');
            }
        }
    }

    public function updatedSearch()
    {
        $this->plano_id = null;
    }

    public function select($id)
    {
        $this->plano_id = $id;
        $p = Plano::find($id);
        if ($p) {
            $this->search = $p->nome . ' - R$ ' . number_format($p->valor, 2, ',', '.');
        }
        $this->dispatch('plano-selecionado', planoId: $id, valor: $p?->valor);
    }

    public function getPlanos()
    {
        if (strlen($this->search) < 2) return collect();
        $s = trim($this->search);
        return Plano::where('ativo', true)
            ->where(function ($q) use ($s) {
                $q->where('nome', 'like', '%' . $s . '%')
                  ->orWhere('descricao', 'like', '%' . $s . '%');
            })
            ->orderByRaw("CASE WHEN nome LIKE ? THEN 0 WHEN nome LIKE ? THEN 1 ELSE 2 END", [$s . '%', '%' . $s . '%'])
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.buscar-plano', [
            'resultados' => $this->getPlanos(),
        ]);
    }
}
