<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbeiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BarbeiroController extends Controller
{
    public function index()
    {
        $barbeiros = Barbeiro::paginate(10);
        return view('admin.barbeiros.index', compact('barbeiros'));
    }

    public function create()
    {
        return view('admin.barbeiros.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbeiros,email',
            'password' => 'required|min:6',
            'telefone' => 'nullable|string|max:20',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'ativo' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);

        Barbeiro::create($data);

        return redirect()->route('admin.barbeiros.index')->with('success', 'Barbeiro cadastrado com sucesso!');
    }

    public function show(Barbeiro $barbeiro)
    {
        return view('admin.barbeiros.show', compact('barbeiro'));
    }

    public function edit(Barbeiro $barbeiro)
    {
        return view('admin.barbeiros.form', ['edit' => true, 'barbeiro' => $barbeiro]);
    }

    public function update(Request $request, Barbeiro $barbeiro)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbeiros,email,' . $barbeiro->id,
            'password' => 'nullable|min:6',
            'telefone' => 'nullable|string|max:20',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'ativo' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $barbeiro->update($data);

        return redirect()->route('admin.barbeiros.index')->with('success', 'Barbeiro atualizado com sucesso!');
    }

    public function destroy(Barbeiro $barbeiro)
    {
        $barbeiro->delete();
        return response()->json(['success' => true, 'message' => 'Barbeiro excluído com sucesso']);
    }
}
