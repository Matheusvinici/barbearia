<?php

namespace App\Livewire\Admin;

use App\Models\Cliente;
use Livewire\Component;

class BuscarCliente extends Component
{
    public $search = '';
    public $cliente_id;
    public $nome = '';
    public $telefone = '';
    public $creating = false;
    public $barbearia_id = null;
    public $tenantIds = [];

    public function mount($cliente_id = null, $barbearia_id = null, $tenantIds = null)
    {
        // Preserve tenant context for subsequent Livewire updates (route('barbearia') not available on /livewire/update)
        if ($barbearia_id) {
            $this->barbearia_id = $barbearia_id;
        } else {
            $this->barbearia_id = request()->route('barbearia')?->id;
        }
        if ($tenantIds) {
            $this->tenantIds = $tenantIds;
        } elseif (request()->route('barbearia')) {
            $this->tenantIds = request()->route('barbearia')->tenantTreeIds();
        }

        if ($cliente_id) {
            $this->cliente_id = $cliente_id;
            $c = Cliente::find($cliente_id);
            if ($c) $this->search = $c->nome . ' - ' . $c->telefone;
        } elseif (old('cliente_id')) {
            $this->cliente_id = old('cliente_id');
            $c = Cliente::find($this->cliente_id);
            if ($c) $this->search = $c->nome . ' - ' . $c->telefone;
        }
    }

    public function updatedSearch()
    {
        $this->cliente_id = null;
        $this->creating = false;
        $this->nome = '';
        $this->telefone = '';
    }

    public function select($id)
    {
        $this->cliente_id = $id;
        $c = Cliente::find($id);
        $this->search = $c?->nome . ' - ' . $c?->telefone;
        $this->dispatch('cliente-selecionado', clienteId: $id);
    }

    public function startCreate()
    {
        $this->creating = true;
        $this->cliente_id = null;
    }

    public function cancelCreate()
    {
        $this->creating = false;
        $this->nome = '';
        $this->telefone = '';
    }

    public function getClientes()
    {
        if (strlen($this->search) < 2) return collect();
        $s = trim($this->search);
        $digits = preg_replace('/\D/', '', $s);
        $query = Cliente::query()->where(function ($q) use ($s, $digits) {
            $q->where('nome', 'like', '%' . $s . '%');
            if ($digits !== '') {
                $q->orWhere('telefone', 'like', '%' . $digits . '%');
            }
        });

        // Tenant filter: usa tenantIds armazenado (persistido entre requisições Livewire)
        if (!empty($this->tenantIds)) {
            $query->whereIn('barbearia_id', $this->tenantIds);
        } elseif ($this->barbearia_id) {
            $query->where('barbearia_id', $this->barbearia_id);
        }

        return $query
            ->orderByRaw("CASE WHEN nome LIKE ? THEN 0 WHEN nome LIKE ? THEN 1 ELSE 2 END", [$s . '%', '%' . $s . '%'])
            ->limit(10)
            ->get();
    }

    public function create()
    {
        $this->validate([
            'nome' => 'required|min:3',
            'telefone' => 'required|min:10',
        ]);

        $telefone = preg_replace('/\D/', '', $this->telefone);
        $barbeariaId = $this->barbearia_id ?? request()->route('barbearia')?->id ?? null;
        $cliente = Cliente::create([
            'nome' => trim($this->nome),
            'telefone' => $telefone,
            'barbearia_id' => $barbeariaId,
        ]);

        $this->select($cliente->id);
        $this->creating = false;
        $this->dispatch('cliente-criado', id: $cliente->id);
        $this->dispatch('cliente-selecionado', clienteId: $cliente->id);
    }

    public function render()
    {
        return view('livewire.admin.buscar-cliente', [
            'resultados' => $this->getClientes(),
        ]);
    }
}
