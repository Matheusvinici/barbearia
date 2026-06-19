<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plano;
use App\Models\Servico;
use Illuminate\Http\Request;

class PlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::withCount('clientes')->paginate(10);
        return view('admin.planos.index', compact('planos'));
    }

    public function create()
    {
        $servicos = Servico::where('ativo', true)->get();
        return view('admin.planos.form', ['edit' => false, 'servicos' => $servicos]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'ativo' => 'boolean',
            'quotas' => 'required|array',
            'quotas.*.servico_id' => 'required|exists:servicos,id',
            'quotas.*.quantidade' => 'required|integer|min:0',
        ]);

        $plano = Plano::create([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'],
            'valor' => $data['valor'],
            'ativo' => $request->boolean('ativo', true),
        ]);

        foreach ($data['quotas'] as $quota) {
            if ($quota['quantidade'] > 0) {
                $plano->quotas()->create([
                    'servico_id' => $quota['servico_id'],
                    'quantidade' => $quota['quantidade'],
                ]);
            }
        }

        return redirect()->route('admin.planos.index')->with('success', 'Plano cadastrado com sucesso!');
    }

    public function show(Plano $plano)
    {
        $plano->load('quotas.servico', 'clientes.cliente');
        return view('admin.planos.show', compact('plano'));
    }

    public function edit(Plano $plano)
    {
        $plano->load('quotas');
        $servicos = Servico::where('ativo', true)->get();
        return view('admin.planos.form', ['edit' => true, 'plano' => $plano, 'servicos' => $servicos]);
    }

    public function update(Request $request, Plano $plano)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric|min:0',
            'ativo' => 'boolean',
            'quotas' => 'required|array',
            'quotas.*.servico_id' => 'required|exists:servicos,id',
            'quotas.*.quantidade' => 'required|integer|min:0',
        ]);

        $plano->update([
            'nome' => $data['nome'],
            'descricao' => $data['descricao'],
            'valor' => $data['valor'],
            'ativo' => $request->boolean('ativo', true),
        ]);

        $plano->quotas()->delete();
        foreach ($data['quotas'] as $quota) {
            if ($quota['quantidade'] > 0) {
                $plano->quotas()->create([
                    'servico_id' => $quota['servico_id'],
                    'quantidade' => $quota['quantidade'],
                ]);
            }
        }

        return redirect()->route('admin.planos.index')->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plano $plano)
    {
        $plano->delete();
        return response()->json(['success' => true, 'message' => 'Plano excluído com sucesso']);
    }
}
