<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('agendamentos')->paginate(15);
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

        Cliente::create($data);

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
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

        return redirect()->route('admin.clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(['success' => true, 'message' => 'Cliente excluído com sucesso']);
    }

    public function search(Request $request)
    {
        $term = $request->get('q');
        $clientes = Cliente::where('nome', 'LIKE', "%{$term}%")
            ->orWhere('telefone', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get(['id', 'nome', 'telefone']);
        return response()->json($clientes);
    }
}
