<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Cliente;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Cliente::with(['agendamentos', 'planos'])->withCount('agendamentos');
        $query = $this->applyTenantScope($query);
        $clientes = $query->orderBy('nome')->paginate(15);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $telefoneRule = $this->isTenantContext()
            ? Rule::unique('clientes', 'telefone')->where(fn ($q) => $q->where('barbearia_id', $this->tenantId()))
            : 'unique:clientes,telefone';

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => ['required', 'string', 'max:20', $telefoneRule],
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

    private function resolveCliente(Request $request): Cliente
    {
        $cliente = $request->route('cliente');
        $cliente = $cliente instanceof Cliente ? $cliente : Cliente::findOrFail($cliente);

        // Isolamento estrito: em contexto tenant, bloqueia acesso a clientes de outras barbearias.
        if ($this->isTenantContext() && !in_array($cliente->barbearia_id, $this->tenantIds(), true)) {
            abort(404);
        }

        return $cliente;
    }

    public function show(Request $request)
    {
        $cliente = $this->resolveCliente($request);
        $cliente->load('agendamentos.barbeiro', 'agendamentos.servicos', 'planos.plano', 'planos.usos');

        $vendas = Venda::with('produtos')
            ->where('cliente_id', $cliente->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $comandaAberta = Venda::with('produtos')
            ->where('cliente_id', $cliente->id)
            ->where('status', 'pendente')
            ->sum('total');

        return view('admin.clientes.show', compact('cliente', 'vendas', 'comandaAberta'));
    }

    public function edit(Request $request)
    {
        $cliente = $this->resolveCliente($request);
        return view('admin.clientes.form', ['edit' => true, 'cliente' => $cliente]);
    }

    public function update(Request $request, $clienteId = null)
    {
        $cliente = $this->resolveCliente($request);
        $telefoneRule = $this->isTenantContext()
            ? Rule::unique('clientes', 'telefone')->where(fn ($q) => $q->where('barbearia_id', $this->tenantId()))->ignore($cliente->id)
            : 'unique:clientes,telefone,' . $cliente->id;

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => ['required', 'string', 'max:20', $telefoneRule],
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $cliente->update($data);

        $route = $this->isTenantContext()
            ? route('tenant.admin.clientes.index', $this->getTenant()->slug)
            : route('admin.clientes.index');

        return redirect()->to($route)->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Request $request)
    {
        $cliente = $this->resolveCliente($request);
        $cliente->delete();
        return response()->json(['success' => true, 'message' => 'Cliente excluído com sucesso']);
    }

    public function search(Request $request)
    {
        $term = $request->get('q');
        $query = Cliente::where(function ($q) use ($term) {
            $q->where('nome', 'LIKE', "%{$term}%")
                ->orWhere('telefone', 'LIKE', "%{$term}%");
        });
        $query = $this->applyTenantScope($query);
        $clientes = $query->limit(10)->get(['id', 'nome', 'telefone']);
        return response()->json($clientes);
    }
}
