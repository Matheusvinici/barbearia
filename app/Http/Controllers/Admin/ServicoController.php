<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::paginate(10);
        return view('admin.servicos.index', compact('servicos'));
    }

    public function create()
    {
        return view('admin.servicos.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:5',
            'ativo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('servicos', 'public');
        }

        Servico::create($data);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function show(Servico $servico)
    {
        return view('admin.servicos.show', compact('servico'));
    }

    public function edit(Servico $servico)
    {
        return view('admin.servicos.form', ['edit' => true, 'servico' => $servico]);
    }

    public function update(Request $request, Servico $servico)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:5',
            'ativo' => 'boolean',
            'remover_foto' => 'boolean',
        ]);

        if ($request->boolean('remover_foto') && $servico->foto) {
            \Storage::disk('public')->delete($servico->foto);
            $data['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            if ($servico->foto) {
                \Storage::disk('public')->delete($servico->foto);
            }
            $data['foto'] = $request->file('foto')->store('servicos', 'public');
        }

        $servico->update($data);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        if ($servico->foto) {
            \Storage::disk('public')->delete($servico->foto);
        }
        $servico->delete();
        return response()->json(['success' => true, 'message' => 'Serviço excluído com sucesso']);
    }
}
