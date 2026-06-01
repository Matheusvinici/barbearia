<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Caixa;
use App\Models\Despesa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();

        $agendamentosHoje = Agendamento::whereDate('data', $hoje)
            ->with(['barbeiro', 'cliente', 'servicos'])
            ->orderBy('hora_inicio')
            ->get();

        $pendentes = $agendamentosHoje->where('status', 'pendente')->count();
        $confirmados = $agendamentosHoje->where('status', 'confirmado')->count();
        $realizados = $agendamentosHoje->where('status', 'realizado')->count();
        $totalFaturamentoHoje = $agendamentosHoje->where('status', 'realizado')->sum('total');

        $agendamentosSemana = Agendamento::whereDate('data', '>=', Carbon::today())
            ->whereDate('data', '<=', Carbon::today()->addDays(7))
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->count();

        $caixaHoje = Caixa::whereDate('data', $hoje)->first();
        $despesasPendentes = Despesa::where('pago', false)->where('data_vencimento', '>=', $hoje)->sum('valor');
        $despesasVencidas = Despesa::where('pago', false)->where('data_vencimento', '<', $hoje)->sum('valor');

        return view('admin.dashboard', compact(
            'agendamentosHoje', 'pendentes', 'confirmados', 'realizados',
            'totalFaturamentoHoje', 'agendamentosSemana', 'caixaHoje',
            'despesasPendentes', 'despesasVencidas'
        ));
    }
}
