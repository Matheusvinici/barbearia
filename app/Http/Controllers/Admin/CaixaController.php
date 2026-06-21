<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaixaController extends Controller
{
    public function index()
    {
        $caixas = Caixa::with(['usuarioAbertura', 'usuarioFechamento'])
            ->orderBy('data', 'desc')
            ->paginate(20);

        return view('admin.caixa.index', compact('caixas'));
    }

    public function show(Caixa $caixa)
    {
        $caixa->load(['movimentacoes', 'usuarioAbertura', 'usuarioFechamento']);
        return view('admin.caixa.show', compact('caixa'));
    }

    public function edit(Caixa $caixa)
    {
        $caixa->load(['movimentacoes', 'usuarioAbertura', 'usuarioFechamento']);
        return view('admin.caixa.edit', compact('caixa'));
    }

    public function update(Request $request, Caixa $caixa)
    {
        $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
            'total_entradas' => 'required|numeric|min:0',
            'total_saidas' => 'required|numeric|min:0',
            'saldo_final' => 'required|numeric',
            'observacoes' => 'nullable|string',
        ]);

        $caixa->update([
            'saldo_inicial' => $request->saldo_inicial,
            'total_entradas' => $request->total_entradas,
            'total_saidas' => $request->total_saidas,
            'saldo_final' => $request->saldo_final,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('admin.caixa.index')->with('success', 'Caixa atualizado com sucesso!');
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'saldo_inicial' => 'required|numeric|min:0',
        ]);

        $existing = Caixa::whereDate('data', $request->data)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Caixa já aberto para esta data. Caso queira ajustar valores, edite o caixa diretamente.');
        }

        Caixa::create([
            'data' => $request->data,
            'saldo_inicial' => $request->saldo_inicial,
            'saldo_final' => $request->saldo_inicial,
            'user_id_abertura' => Auth::guard('web')->id(),
        ]);

        return redirect()->route('admin.caixa.index')->with('success', 'Caixa aberto com sucesso!');
    }

    public function fechar(Request $request, Caixa $caixa)
    {
        $request->validate([
            'saldo_informado' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $caixa->update([
            'saldo_final' => $request->saldo_informado,
            'fechado' => true,
            'observacoes' => $request->observacoes,
            'user_id_fechamento' => Auth::guard('web')->id(),
        ]);

        return redirect()->route('admin.caixa.index')->with('success', 'Caixa fechado com sucesso!');
    }

    public function reabrir(Caixa $caixa)
    {
        if (!$caixa->fechado) {
            return redirect()->back()->with('error', 'Caixa já está aberto.');
        }

        $caixa->update([
            'fechado' => false,
            'user_id_fechamento' => null,
        ]);

        return redirect()->route('admin.caixa.index')->with('success', 'Caixa reaberto com sucesso!');
    }
}
