<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Historia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlunos = Aluno::count();
        $totalHistorias = Historia::count();
        $historiasHoje = Historia::whereDate('created_at', today())->count();
        $historiasConcluidas = Historia::where('status', 'concluido')->count();
        $ultimasHistorias = Historia::with('aluno')
            ->where('status', 'concluido')
            ->latest()
            ->take(5)
            ->get();

<<<<<<< HEAD
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

=======
>>>>>>> origin/main
        return view('admin.dashboard', compact(
            'totalAlunos',
            'totalHistorias',
            'historiasHoje',
            'historiasConcluidas',
            'ultimasHistorias'
        ));
    }
}
