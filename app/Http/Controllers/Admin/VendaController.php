<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Agendamento;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendaController extends Controller
{
    use TenantScoped;

    private function authUserId(): ?int
    {
        return Auth::guard('web')->id() ?? Auth::guard('barbeiro')->id();
    }

    public function index()
    {
        $query = Venda::with(['cliente', 'produtos', 'barbearia', 'agendamento']);

        if (request('q')) {
            $query->whereHas('cliente', function ($q) {
                $q->where('nome', 'like', '%' . request('q') . '%');
            });
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('data')) {
            $query->whereDate('created_at', request('data'));
        }

        $query = $this->applyTenantScope($query);
        $vendas = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $totalPendente = (clone $query)->where('status', 'pendente')->sum('total');
        $totalFinalizadas = (clone $query)->where('status', 'finalizada')->sum('total');
        $qtdVendas = (clone $query)->count();

        return view('admin.vendas.index', compact('vendas', 'totalPendente', 'totalFinalizadas', 'qtdVendas'));
    }

    public function create()
    {
        $clientes = Cliente::query();
        $clientes = $this->applyTenantScope($clientes);
        $clientes = $clientes->orderBy('nome')->get();

        $produtos = Produto::query();
        $produtos = $this->applyTenantScope($produtos);
        $produtos = $produtos->where('ativo', true)->orderBy('nome')->get();

        $agendamentoId = request('agendamento_id');
        $clienteId = request('cliente_id');

        $agendamento = null;
        if ($agendamentoId) {
            $agendamento = Agendamento::with('cliente')->find($agendamentoId);
            if ($agendamento && !$clienteId) {
                $clienteId = $agendamento->cliente_id;
            }
        }

        return view('admin.vendas.create', compact('clientes', 'produtos', 'agendamentoId', 'clienteId', 'agendamento'));
    }

    public function store(Request $request)
    {
        $data = $this->validateVenda($request);

        $produtos = $this->resolveProdutos($request);
        $total = $this->calcularTotal($produtos, $request->get('desconto', 0));

        $venda = Venda::create([
            'barbearia_id' => $data['barbearia_id'],
            'cliente_id' => $data['cliente_id'],
            'agendamento_id' => $data['agendamento_id'] ?? null,
            'user_id' => $this->authUserId(),
            'total' => $total,
            'desconto' => $data['desconto'],
            'forma_pagamento' => $data['forma_pagamento'],
            'status' => $data['status'],
            'observacoes' => $data['observacoes'],
            'finalizada_at' => $data['status'] === 'finalizada' ? now() : null,
        ]);

        $this->attachProdutos($venda, $request);

        if ($venda->status === 'finalizada') {
            $this->registrarNoCaixa($venda);
        }

        return $this->redirectToList($venda, 'Venda registrada com sucesso!');
    }

    public function edit(Request $request)
    {
        $venda = $this->getVendaFromRoute($request);
        $venda->load(['cliente', 'produtos', 'agendamento']);

        $clientes = Cliente::query();
        $clientes = $this->applyTenantScope($clientes);
        $clientes = $clientes->orderBy('nome')->get();

        $produtos = Produto::query();
        $produtos = $this->applyTenantScope($produtos);
        $produtos = $produtos->orderBy('nome')->get();

        return view('admin.vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

    public function update(Request $request)
    {
        $venda = $this->getVendaFromRoute($request);
        $data = $this->validateVenda($request);

        $produtos = $this->resolveProdutos($request);
        $total = $this->calcularTotal($produtos, $request->get('desconto', 0));

        $oldStatus = $venda->status;

        $venda->update([
            'cliente_id' => $data['cliente_id'],
            'agendamento_id' => $data['agendamento_id'] ?? null,
            'total' => $total,
            'desconto' => $data['desconto'],
            'forma_pagamento' => $data['forma_pagamento'],
            'status' => $data['status'],
            'observacoes' => $data['observacoes'],
            'finalizada_at' => $data['status'] === 'finalizada' ? now() : null,
        ]);

        $venda->produtos()->detach();
        $this->attachProdutos($venda, $request);

        if ($data['status'] === 'finalizada' && $oldStatus !== 'finalizada') {
            $this->registrarNoCaixa($venda);
        }

        return $this->redirectToList($venda, 'Venda atualizada com sucesso!');
    }

    public function destroy(Request $request)
    {
        $venda = $this->getVendaFromRoute($request);
        $venda->produtos()->detach();
        $venda->delete();
        return response()->json(['success' => true, 'message' => 'Venda excluída com sucesso']);
    }

    private function getVendaFromRoute(Request $request): Venda
    {
        $param = $request->route('venda');
        return $param instanceof Venda ? $param : Venda::findOrFail((int) $param);
    }

    private function validateVenda(Request $request): array
    {
        $data = $request->validate([
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'agendamento_id' => 'nullable|exists:agendamentos,id',
            'desconto' => 'nullable|numeric|min:0',
            'forma_pagamento' => 'nullable|string|max:50',
            'status' => 'required|in:pendente,finalizada,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $data['desconto'] = $data['desconto'] ?? 0;
        $data['cliente_id'] = $data['cliente_id'] ?? null;
        $data['agendamento_id'] = $data['agendamento_id'] ?? null;
        $data['forma_pagamento'] = $data['forma_pagamento'] ?? null;
        $data['observacoes'] = $data['observacoes'] ?? null;

        if ($this->isTenantContext() && empty($data['barbearia_id'])) {
            $data['barbearia_id'] = $this->tenantId();
        }

        if (empty($data['barbearia_id']) && !empty($data['cliente_id'])) {
            $data['barbearia_id'] = Cliente::find($data['cliente_id'])?->barbearia_id;
        }

        return $data;
    }

    private function resolveProdutos(Request $request)
    {
        $ids = $request->get('produto_ids', []);
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: [];
        }
        $ids = array_filter(array_map('intval', (array) $ids));

        if (empty($ids)) {
            return collect();
        }

        $produtos = Produto::whereIn('id', $ids)->get();

        if ($this->isTenantContext()) {
            $produtos = $produtos->filter(function ($p) {
                return in_array($p->barbearia_id, $this->tenantIds()) || is_null($p->barbearia_id);
            });
        }

        return $produtos->keyBy('id');
    }

    private function calcularTotal($produtos, $desconto)
    {
        $total = 0;
        $quantidades = request('quantidades', []);

        foreach ($produtos as $produto) {
            $qtd = (int) ($quantidades[$produto->id] ?? 1);
            $qtd = max($qtd, 1);
            $total += $produto->preco * $qtd;
        }

        return max(round($total - (float) $desconto, 2), 0);
    }

    private function attachProdutos(Venda $venda, Request $request)
    {
        $produtos = $this->resolveProdutos($request);
        $quantidades = $request->get('quantidades', []);

        foreach ($produtos as $produto) {
            $qtd = max((int) ($quantidades[$produto->id] ?? 1), 1);
            $venda->produtos()->attach($produto->id, [
                'quantidade' => $qtd,
                'preco_unitario' => $produto->preco,
                'subtotal' => round($produto->preco * $qtd, 2),
            ]);
        }
    }

    private function registrarNoCaixa(Venda $venda)
    {
        $dataStr = $venda->created_at ? $venda->created_at->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        $caixaQuery = Caixa::whereDate('data', $dataStr);

        if ($venda->barbearia_id) {
            $caixaQuery->where('barbearia_id', $venda->barbearia_id);
        } elseif ($this->isTenantContext()) {
            $caixaQuery->where('barbearia_id', $this->tenantId());
        }

        $caixa = $caixaQuery->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'barbearia_id' => $venda->barbearia_id ?? $this->tenantId(),
                'data' => $dataStr,
                'saldo_inicial' => 0,
                'user_id_abertura' => $this->authUserId(),
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $venda->total);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();
        }

        CaixaMovimentacao::create([
            'barbearia_id' => $caixa->barbearia_id,
            'caixa_id' => $caixa->id,
            'tipo' => 'entrada',
            'valor' => $venda->total,
            'descricao' => "Venda de produtos" . ($venda->cliente ? " - {$venda->cliente->nome}" : ''),
            'origem_type' => Venda::class,
            'origem_id' => $venda->id,
            'user_id' => $this->authUserId(),
        ]);
    }

    private function redirectToList(Venda $venda, string $message)
    {
        $route = $this->isTenantContext()
            ? route('tenant.admin.vendas.index', $this->getTenant()->slug)
            : route('admin.vendas.index');

        return redirect()->to($route)->with('success', $message);
    }
}