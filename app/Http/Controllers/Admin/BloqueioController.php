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
        $isBarbeiro = Auth::guard('barbeiro')->check();

        if ($isBarbeiro) {
            $barbeiro = Auth::guard('barbeiro')->user();
            $bloqueios = BloqueioAgenda::with('barbeiro', 'barbearia')
                ->where('barbeiro_id', $barbeiro->id)
                ->whereDate('data', '>=', now()->subDay())
                ->orderBy('data')
                ->orderBy('hora_inicio')
                ->paginate(20);

            return view('admin.bloqueios.index', compact('bloqueios', 'barbeiro', 'isBarbeiro'));
        }

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

        return view('admin.bloqueios.index', compact('bloqueios', 'barbeiros', 'barbearias', 'barbeariaId', 'isBarbeiro'));
    }

    public function store(Request $request)
    {
        $isBarbeiro = Auth::guard('barbeiro')->check();

        $rules = [
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required|after:hora_inicio',
            'motivo' => 'nullable|string|max:255',
            'recorrente' => 'boolean',
        ];

        if (!$isBarbeiro) {
            $rules['barbearia_id'] = 'nullable|exists:barbearias,id';
            $rules['barbeiro_id'] = 'required|exists:barbeiros,id';
        }

        $data = $request->validate($rules);

        if ($isBarbeiro) {
            $barbeiro = Auth::guard('barbeiro')->user();
            $data['barbeiro_id'] = $barbeiro->id;
            $data['barbearia_id'] = $barbeiro->barbearia_id;
        }

        BloqueioAgenda::create($data);

        return redirect()->route('admin.bloqueios.index')->with('success', 'Bloqueio cadastrado com sucesso!');
    }

    public function destroy(BloqueioAgenda $bloqueio)
    {
        $isBarbeiro = Auth::guard('barbeiro')->check();

        if ($isBarbeiro) {
            $barbeiro = Auth::guard('barbeiro')->user();
            if ($bloqueio->barbeiro_id !== $barbeiro->id) {
                return response()->json(['success' => false, 'message' => 'Você só pode remover seus próprios bloqueios'], 403);
            }
        }

        $bloqueio->delete();
        return response()->json(['success' => true, 'message' => 'Bloqueio removido com sucesso']);
    }
}
