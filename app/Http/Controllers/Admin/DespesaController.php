<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Despesa;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DespesaController extends Controller
{
    public function index()
    {
        $despesas = Despesa::orderBy('data_vencimento', 'desc')->paginate(15);
        return view('admin.despesas.index', compact('despesas'));
    }

    public function create()
    {
        return view('admin.despesas.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'categoria' => 'required|string|max:50',
            'forma_pagamento' => 'nullable|string|max:50',
            'pago' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $data['user_id'] = Auth::guard('web')->id();

        $despesa = Despesa::create($data);

        if ($despesa->pago && $despesa->data_pagamento) {
            $this->registrarSaidaNoCaixa($despesa);
        }

        return redirect()->route('admin.despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    public function edit(Despesa $despesa)
    {
        return view('admin.despesas.form', ['edit' => true, 'despesa' => $despesa]);
    }

    public function update(Request $request, Despesa $despesa)
    {
        $data = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'categoria' => 'required|string|max:50',
            'forma_pagamento' => 'nullable|string|max:50',
            'pago' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $wasUnpaid = !$despesa->pago;
        $despesa->update($data);

        if ($despesa->pago && $wasUnpaid && $despesa->data_pagamento) {
            $this->registrarSaidaNoCaixa($despesa);
        }

        return redirect()->route('admin.despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function destroy(Despesa $despesa)
    {
        $despesa->delete();
        return response()->json(['success' => true, 'message' => 'Despesa excluída com sucesso']);
    }

    public function togglePago(Despesa $despesa)
    {
        $despesa->update([
            'pago' => !$despesa->pago,
            'data_pagamento' => !$despesa->pago ? now() : null,
        ]);

        if ($despesa->pago) {
            $this->registrarSaidaNoCaixa($despesa);
        }

        return response()->json(['success' => true]);
    }

    private function registrarSaidaNoCaixa(Despesa $despesa)
    {
        $data = $despesa->data_pagamento ? \Carbon\Carbon::parse($despesa->data_pagamento) : now();

        $caixa = Caixa::firstOrCreate(
            ['data' => $data->format('Y-m-d')],
            [
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]
        );

        if (!$caixa->fechado) {
            $caixa->increment('total_saidas', $despesa->valor);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();

            CaixaMovimentacao::create([
                'caixa_id' => $caixa->id,
                'tipo' => 'saida',
                'valor' => $despesa->valor,
                'descricao' => "Despesa: {$despesa->descricao}",
                'origem_type' => Despesa::class,
                'origem_id' => $despesa->id,
                'user_id' => Auth::guard('web')->id(),
            ]);
        }
    }
}
