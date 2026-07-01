<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Cliente::withCount('agendamentos');
        $query = $this->applyTenantScope($query);
        $clientes = $query->paginate(15);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20|unique:clientes,telefone',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        if ($this->isTenantContext()) {
            $data['barbearia_id'] = $this->tenantId();
        }

        Cliente::create($data);

        $route = $this->isTenantContext()
            ? route('tenant.admin.clientes.index', $this->getTenant()->slug)
            : route('admin.clientes.index');

        return redirect()->to($route)->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('agendamentos.barbeiro', 'agendamentos.servicos', 'planos.plano', 'planos.usos');
        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.form', ['edit' => true, 'cliente' => $cliente]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20|unique:clientes,telefone,' . $cliente->id,
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $cliente->update($data);

        $route = $this->isTenantContext()
            ? route('tenant.admin.clientes.index', $this->getTenant()->slug)
            : route('admin.clientes.index');

        return redirect()->to($route)->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(['success' => true, 'message' => 'Cliente excluído com sucesso']);
    }

    public function search(Request $request)
    {
        $term = $request->get('q');
        $query = Cliente::where('nome', 'LIKE', "%{$term}%")
            ->orWhere('telefone', 'LIKE', "%{$term}%");
        $query = $this->applyTenantScope($query);
        $clientes = $query->limit(10)->get(['id', 'nome', 'telefone']);
        return response()->json($clientes);
    }
}
