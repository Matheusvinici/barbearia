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
        $this->search = Cliente::find($id)?->nome . ' - ' . Cliente::find($id)?->telefone;
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

    public function create()
    {
        $this->validate([
            'nome' => 'required|min:3',
            'telefone' => 'required|min:10',
        ]);

        $telefone = preg_replace('/\D/', '', $this->telefone);
        $cliente = Cliente::create([
            'nome' => $this->nome,
            'telefone' => $telefone,
        ]);

        $this->select($cliente->id);
        $this->creating = false;
        $this->dispatch('cliente-criado', id: $cliente->id);
    }

    public function getClientes()
    {
        if (strlen($this->search) < 2) return collect();
        return Cliente::where('nome', 'like', '%' . $this->search . '%')
            ->orWhere('telefone', 'like', '%' . preg_replace('/\D/', '', $this->search) . '%')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.buscar-cliente', [
            'resultados' => $this->getClientes(),
        ]);
    }
}
