<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use Illuminate\Http\Request;

class BarbeariaController extends Controller
{
    public function index()
    {
        $barbearias = Barbearia::with('parent')->withCount('children')->paginate(10);
        return view('admin.barbearias.index', compact('barbearias'));
    }

    public function create()
    {
        $matrizes = Barbearia::whereNull('parent_id')->orderBy('nome')->get();
        return view('admin.barbearias.form', ['edit' => false, 'matrizes' => $matrizes]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:barbearias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
        ]);

        Barbearia::create($data);

        return redirect()->route('admin.barbearias.index')->with('success', 'Barbearia cadastrada com sucesso!');
    }

    public function edit(Barbearia $barbearia)
    {
        $matrizes = Barbearia::whereNull('parent_id')->where('id', '!=', $barbearia->id)->orderBy('nome')->get();
        return view('admin.barbearias.form', ['edit' => true, 'barbearia' => $barbearia, 'matrizes' => $matrizes]);
    }

    public function update(Request $request, Barbearia $barbearia)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:barbearias,id|different:id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
        ]);

        $barbearia->update($data);

        return redirect()->route('admin.barbearias.index')->with('success', 'Barbearia atualizada com sucesso!');
    }

    public function destroy(Barbearia $barbearia)
    {
        $barbearia->delete();
        return response()->json(['success' => true, 'message' => 'Barbearia excluída com sucesso']);
    }
}
