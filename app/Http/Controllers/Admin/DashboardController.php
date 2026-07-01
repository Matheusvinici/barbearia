<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Agendamento;
use App\Models\Caixa;
use App\Models\Despesa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $hoje = Carbon::today();

        $queryAgendamentos = Agendamento::whereDate('data', $hoje)
            ->with(['barbeiro', 'cliente', 'cliente.planos', 'servicos']);

        $queryAgendamentos = $this->applyTenantScope($queryAgendamentos);

        $agendamentosHoje = $queryAgendamentos->orderBy('hora_inicio')->get();

        $pendentes = $agendamentosHoje->where('status', 'pendente')->count();
        $confirmados = $agendamentosHoje->where('status', 'confirmado')->count();
        $realizados = $agendamentosHoje->where('status', 'realizado')->count();
        $totalFaturamentoHoje = $agendamentosHoje->where('status', 'realizado')->sum('total');

        $querySemana = Agendamento::whereDate('data', '>=', Carbon::today())
            ->whereDate('data', '<=', Carbon::today()->addDays(7))
            ->whereNotIn('status', ['cancelado', 'ausente']);

        $querySemana = $this->applyTenantScope($querySemana);
        $agendamentosSemana = $querySemana->count();

        $queryCaixa = Caixa::whereDate('data', $hoje);
        $queryCaixa = $this->applyTenantScope($queryCaixa);
        $caixaHoje = $queryCaixa->first();

        $queryDespesasPendentes = Despesa::where('pago', false)->where('data_vencimento', '>=', $hoje);
        $queryDespesasPendentes = $this->applyTenantScope($queryDespesasPendentes);
        $despesasPendentes = $queryDespesasPendentes->sum('valor');

        $queryDespesasVencidas = Despesa::where('pago', false)->where('data_vencimento', '<', $hoje);
        $queryDespesasVencidas = $this->applyTenantScope($queryDespesasVencidas);
        $despesasVencidas = $queryDespesasVencidas->sum('valor');

        return view('admin.dashboard', compact(
            'agendamentosHoje', 'pendentes', 'confirmados', 'realizados',
            'totalFaturamentoHoje', 'agendamentosSemana', 'caixaHoje',
            'despesasPendentes', 'despesasVencidas'
        ));
    }
}
