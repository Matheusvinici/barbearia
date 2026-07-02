<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Barbearia;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function index(Request $request)
    {
        $barbearia = $request->route('barbearia');
        $user = auth()->user();

        if ($barbearia) {
            $barbeariaIds = [$barbearia->id];
            if ($barbearia->isMatriz()) {
                $barbeariaIds = array_merge($barbeariaIds, $barbearia->filiais->pluck('id')->toArray());
            }
            $avaliacoes = Avaliacao::whereIn('barbearia_id', $barbeariaIds)
                ->with('barbearia')
                ->latest()
                ->get();
        } elseif ($user && $user->isSuperAdmin()) {
            $avaliacoes = Avaliacao::with('barbearia')
                ->latest()
                ->get();
        } else {
            $barbeariaIds = $user?->ownedBarbearias->pluck('id')->toArray() ?? [];
            $avaliacoes = Avaliacao::whereIn('barbearia_id', $barbeariaIds)
                ->with('barbearia')
                ->latest()
                ->get();
        }

        $html = view('admin.configuracoes._avaliacoes', compact('avaliacoes', 'barbearia'))->render();

        return response()->json(['html' => $html]);
    }

    public function responder(Request $request, Avaliacao $avaliacao)
    {
        $request->validate(['resposta' => 'required|string|max:1000']);

        $avaliacao->update([
            'resposta' => $request->resposta,
            'responded_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Resposta salva com sucesso!');
    }
}
