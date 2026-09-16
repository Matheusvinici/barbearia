<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\Cliente;
use App\Models\ClientePlano;
use App\Models\ClientePlanoUso;
use App\Models\Plano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientePlanoController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = ClientePlano::with(['cliente', 'plano.quotas.servico', 'usos']);
        if ($this->isTenantContext()) {
            $query->whereIn('barbearia_id', $this->tenantIds());
        }
        $vinculos = $query->latest()->paginate(15);
        $planos = Plano::where('ativo', true)->get();
        $clientes = Cliente::orderBy('nome')->get();
        // Para seleção filtrada por tenant
        if ($this->isTenantContext()) {
            $clientes = Cliente::whereIn('barbearia_id', $this->tenantIds())->orderBy('nome')->get();
        }
        return view('admin.clientes-planos.index', compact('vinculos', 'planos', 'clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'plano_id' => 'required|exists:planos,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'vencimento' => 'nullable|date|after_or_equal:data_inicio',
            'cpf' => 'nullable|string|max:14',
            'valor_pago' => 'nullable|numeric|min:0',
            'forma_pagamento' => 'nullable|string|max:50',
            'pago' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $data['ativo'] = true;
        $barbeariaId = $this->isTenantContext() ? $this->tenantId() : (Cliente::find($data['cliente_id'])?->barbearia_id);
        // Fallback para admin global: usa barbearia do usuário ou primeira cadastrada para não ficar null e sumir do caixa tenant
        if (!$barbeariaId) {
            $user = Auth::guard('web')->user();
            $barbeariaId = $user?->ownedBarbearias()->first()?->id ?? \App\Models\Barbearia::first()?->id;
        }
        $data['barbearia_id'] = $barbeariaId;
        $data['pago'] = $request->boolean('pago', false);
        if ($data['pago']) {
            $data['pago_em'] = now();
            if (empty($data['valor_pago'])) {
                $plano = Plano::find($data['plano_id']);
                $data['valor_pago'] = $plano?->valor ?? 0;
            }
        }
        if (!empty($data['vencimento']) && empty($data['data_fim'])) {
            $data['data_fim'] = $data['vencimento'];
        } elseif (!empty($data['data_fim']) && empty($data['vencimento'])) {
            $data['vencimento'] = $data['data_fim'];
        }

        $vinculo = ClientePlano::create($data);

        if ($vinculo->pago && $vinculo->valor_pago > 0) {
            $this->registrarPagamentoNoCaixa($vinculo);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.clientes-planos.index', $this->getTenant()->slug)
            : route('admin.clientes-planos.index');

        return redirect()->to($route)->with('success', 'Cliente vinculado ao plano com sucesso!');
    }

    public function edit(ClientePlano $clientesPlano)
    {
        $vinculo = $clientesPlano->load(['cliente', 'plano.quotas.servico', 'usos']);
        $planos = Plano::where('ativo', true)->get();
        return view('admin.clientes-planos.edit', compact('vinculo', 'planos'));
    }

    public function update(Request $request, ClientePlano $clientesPlano)
    {
        $wasPago = (bool) $clientesPlano->pago;

        $data = $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'vencimento' => 'nullable|date|after_or_equal:data_inicio',
            'cpf' => 'nullable|string|max:14',
            'valor_pago' => 'nullable|numeric|min:0',
            'forma_pagamento' => 'nullable|string|max:50',
            'pago' => 'boolean',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);
        $data['pago'] = $request->boolean('pago', false);
        if ($data['pago'] && !$wasPago) {
            $data['pago_em'] = now();
            if (empty($data['valor_pago'])) {
                $plano = Plano::find($data['plano_id']);
                $data['valor_pago'] = $clientesPlano->valor_pago ?? $plano?->valor ?? 0;
            }
        } elseif (!$data['pago']) {
            $data['pago_em'] = null;
        }
        if (!empty($data['vencimento']) && empty($data['data_fim'])) {
            $data['data_fim'] = $data['vencimento'];
        } elseif (!empty($data['data_fim']) && empty($data['vencimento'])) {
            $data['vencimento'] = $data['data_fim'];
        }

        $clientesPlano->update($data);

        if ($clientesPlano->pago && !$wasPago && $clientesPlano->valor_pago > 0) {
            $this->registrarPagamentoNoCaixa($clientesPlano);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.clientes-planos.index', $this->getTenant()->slug)
            : route('admin.clientes-planos.index');

        return redirect()->to($route)->with('success', 'Vínculo atualizado com sucesso!');
    }

    public function destroy(ClientePlano $clientesPlano)
    {
        $clientesPlano->delete();
        return response()->json(['success' => true, 'message' => 'Vínculo excluído com sucesso']);
    }

    public function dashboard()
    {
        $query = ClientePlano::with([
            'cliente',
            'plano.quotas.servico',
            'usos.servico',
        ])->where('ativo', true);
        if ($this->isTenantContext()) {
            $query->whereIn('barbearia_id', $this->tenantIds());
        }
        $vinculos = $query->get();

        $dados = $vinculos->map(function ($cp) {
            $quotas = $cp->plano->quotas->map(function ($q) use ($cp) {
                $usos = $cp->usos->where('servico_id', $q->servico_id)->count();
                $dentro = $usos < $q->quantidade;
                return [
                    'servico' => $q->servico->nome,
                    'contratada' => $q->quantidade,
                    'utilizada' => $usos,
                    'restante' => max(0, $q->quantidade - $usos),
                    'dentro_da_cota' => $dentro,
                ];
            });

            $totalUsos = $cp->usos->count();
            $totalQuotas = $cp->plano->quotas->sum('quantidade');
            $todasDentro = $quotas->every('dentro_da_cota');

            return [
                'id' => $cp->id,
                'cliente' => $cp->cliente,
                'plano' => $cp->plano,
                'data_inicio' => $cp->data_inicio,
                'data_fim' => $cp->data_fim,
                'vencimento' => $cp->vencimento ?? $cp->data_fim,
                'dias_para_vencer' => $cp->dias_para_vencer,
                'expirado' => $cp->expirado,
                'pago' => $cp->pago,
                'valor_pago' => $cp->valor_pago,
                'forma_pagamento' => $cp->forma_pagamento,
                'cpf' => $cp->cpf,
                'quotas' => $quotas,
                'total_utilizada' => $totalUsos,
                'total_contratada' => $totalQuotas,
                'todas_dentro' => $todasDentro,
            ];
        });

        return view('admin.clientes-planos.dashboard', compact('dados'));
    }

    public function clientePlanoInfo(Request $request, Cliente $cliente)
    {
        $cliente->load(['planos.plano.quotas.servico', 'planos.usos']);
        $ativos = $cliente->planos->where('ativo', true)->map(function ($cp) {
            $validade = $cp->vencimento ?? $cp->data_fim;
            return [
                'id' => $cp->id,
                'plano_id' => $cp->plano_id,
                'plano_nome' => $cp->plano->nome ?? '-',
                'plano_valor' => $cp->plano->valor ?? 0,
                'data_inicio' => $cp->data_inicio?->format('Y-m-d'),
                'data_fim' => $cp->data_fim?->format('Y-m-d'),
                'vencimento' => $validade?->format('Y-m-d'),
                'vencimento_br' => $validade?->format('d/m/Y'),
                'dias_para_vencer' => $cp->dias_para_vencer,
                'expirado' => $cp->expirado,
                'pago' => (bool) $cp->pago,
                'valor_pago' => $cp->valor_pago,
                'forma_pagamento' => $cp->forma_pagamento,
                'quotas' => $cp->plano->quotas->map(fn($q) => [
                    'servico_id' => $q->servico_id,
                    'servico_nome' => $q->servico->nome,
                    'quantidade' => $q->quantidade,
                    'usada' => $cp->usos->where('servico_id', $q->servico_id)->count(),
                    'restante' => max(0, $q->quantidade - $cp->usos->where('servico_id', $q->servico_id)->count()),
                ])->values(),
                'total_contratada' => $cp->total_contratada,
                'total_usada' => $cp->total_usada,
                'total_restante' => $cp->total_restante,
            ];
        })->values();

        return response()->json(['cliente_id' => $cliente->id, 'planos' => $ativos]);
    }

    private function registrarPagamentoNoCaixa(ClientePlano $cp): void
    {
        $cp->load(['cliente', 'plano']);
        $barbeariaId = $cp->barbearia_id ?? $cp->cliente?->barbearia_id ?? $this->tenantId();
        $dataStr = $cp->pago_em ? Carbon::parse($cp->pago_em)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr);
        if ($barbeariaId) {
            $caixa->where('barbearia_id', $barbeariaId);
        } elseif ($this->isTenantContext()) {
            $caixa->where('barbearia_id', $this->tenantId());
        }
        $caixa = $caixa->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'barbearia_id' => $barbeariaId,
                'data' => $dataStr,
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $cp->valor_pago);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();
        }

        CaixaMovimentacao::create([
            'barbearia_id' => $caixa->barbearia_id,
            'caixa_id' => $caixa->id,
            'tipo' => 'entrada',
            'valor' => $cp->valor_pago,
            'descricao' => "Plano {$cp->plano->nome} - {$cp->cliente->nome}" . ($cp->forma_pagamento ? " ({$cp->forma_pagamento})" : ''),
            'origem_type' => ClientePlano::class,
            'origem_id' => $cp->id,
            'user_id' => Auth::guard('web')->id(),
        ]);
    }
}
