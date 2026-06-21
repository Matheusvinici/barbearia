<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use App\Models\BloqueioAgenda;
use App\Models\Barbeiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloqueioController extends Controller
{
    public function index()
    {
        $barbearias = Barbearia::orderBy('nome')->get();
        $barbeariaId = request('barbearia_id');

        $barbeiros = Barbeiro::where('ativo', true);
        if ($barbeariaId) {
            $barbeiros->where('barbearia_id', $barbeariaId);
        }
        $barbeiros = $barbeiros->orderBy('nome')->get();

        $bloqueios = BloqueioAgenda::with('barbeiro', 'barbearia')
            ->when($barbeariaId, function ($q) use ($barbeariaId) {
                $q->where('barbearia_id', $barbeariaId);
            })
            ->whereDate('data', '>=', now()->subDay())
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->paginate(20);

        return view('admin.bloqueios.index', compact('bloqueios', 'barbeiros', 'barbearias', 'barbeariaId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barbearia_id' => 'nullable|exists:barbearias,id',
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
