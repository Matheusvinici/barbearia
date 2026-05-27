<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloqueioAgenda;
use App\Models\Barbeiro;
use Illuminate\Http\Request;

class BloqueioController extends Controller
{
    public function index()
    {
        $barbeiros = Barbeiro::where('ativo', true)->get();
        $bloqueios = BloqueioAgenda::with('barbeiro')
            ->whereDate('data', '>=', now()->subDay())
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->paginate(20);

        return view('admin.bloqueios.index', compact('bloqueios', 'barbeiros'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required|after:hora_inicio',
            'motivo' => 'nullable|string|max:255',
            'recorrente' => 'boolean',
        ]);

        BloqueioAgenda::create($data);

        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio cadastrado com sucesso!');
    }

    public function destroy(BloqueioAgenda $bloqueio)
    {
        $bloqueio->delete();
        return response()->json(['success' => true, 'message' => 'Bloqueio removido com sucesso']);
    }
}
