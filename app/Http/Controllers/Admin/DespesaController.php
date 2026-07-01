<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbearia;
use App\Models\Despesa;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DespesaController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Despesa::with('barbearia')->orderBy('data_vencimento', 'desc');
        $query = $this->applyTenantScope($query);

        $despesas = $query->paginate(15);

        $allQuery = Despesa::query();
        $allQuery = $this->applyTenantScope($allQuery);

        $totalMes = (clone $allQuery)->whereMonth('data_vencimento', now()->month)
            ->whereYear('data_vencimento', now()->year)->sum('valor');
        $qtdMes = (clone $allQuery)->whereMonth('data_vencimento', now()->month)
            ->whereYear('data_vencimento', now()->year)->count();

        $totalPago = (clone $allQuery)->where('pago', true)->sum('valor');
        $qtdPago = (clone $allQuery)->where('pago', true)->count();

        $totalPendente = (clone $allQuery)->where('pago', false)->sum('valor');
        $qtdPendente = (clone $allQuery)->where('pago', false)->count();

        $totalVencido = (clone $allQuery)->where('pago', false)
            ->where('data_vencimento', '<', now())->sum('valor');
        $qtdVencido = (clone $allQuery)->where('pago', false)
            ->where('data_vencimento', '<', now())->count();

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $total = (clone $allQuery)->whereMonth('data_vencimento', $date->month)
                ->whereYear('data_vencimento', $date->year)->sum('valor');
            $chartData[] = [
                'label' => $date->format('M'),
                'total' => (float) $total,
            ];
        }

        return view('admin.despesas.index', compact(
            'despesas', 'totalMes', 'qtdMes', 'totalPago', 'qtdPago',
            'totalPendente', 'qtdPendente', 'totalVencido', 'qtdVencido', 'chartData'
        ));
    }

    public function create()
    {
        $barbearias = $this->getTenantBarbearias();
        return view('admin.despesas.form', ['edit' => false, 'barbearias' => $barbearias]);
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
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'pago' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        if ($this->isTenantContext() && empty($data['barbearia_id'])) {
            $data['barbearia_id'] = $this->tenantId();
        }

        $data['user_id'] = Auth::guard('web')->id();

        $despesa = Despesa::create($data);

        if ($despesa->pago && $despesa->data_pagamento) {
            $this->registrarSaidaNoCaixa($despesa);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.despesas.index', $this->getTenant()->slug)
            : route('admin.despesas.index');

        return redirect()->to($route)->with('success', 'Despesa cadastrada com sucesso!');
    }

    public function edit(Despesa $despesa)
    {
        $barbearias = $this->getTenantBarbearias();
        return view('admin.despesas.form', ['edit' => true, 'despesa' => $despesa, 'barbearias' => $barbearias]);
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
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'pago' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $wasUnpaid = !$despesa->pago;
        $despesa->update($data);

        if ($despesa->pago && $wasUnpaid && $despesa->data_pagamento) {
            $this->registrarSaidaNoCaixa($despesa);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.despesas.index', $this->getTenant()->slug)
            : route('admin.despesas.index');

        return redirect()->to($route)->with('success', 'Despesa atualizada com sucesso!');
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

        $caixaQuery = Caixa::whereDate('data', $data->format('Y-m-d'));
        if ($despesa->barbearia_id) {
            $caixaQuery->where('barbearia_id', $despesa->barbearia_id);
        } elseif ($this->isTenantContext()) {
            $caixaQuery->where('barbearia_id', $this->tenantId());
        }

        $caixa = $caixaQuery->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'barbearia_id' => $despesa->barbearia_id ?? $this->tenantId(),
                'data' => $data->format('Y-m-d'),
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_saidas', $despesa->valor);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();

            CaixaMovimentacao::create([
                'barbearia_id' => $caixa->barbearia_id,
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

    private function getTenantBarbearias()
    {
        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
            $ids = $tenant->tenantTreeIds();
            return Barbearia::whereIn('id', $ids)->orderBy('nome')->get();
        }
        return Barbearia::orderBy('nome')->get();
    }
}
