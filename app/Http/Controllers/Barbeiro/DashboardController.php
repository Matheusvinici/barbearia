<?php

namespace App\Http\Controllers\Barbeiro;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        $agendamentosHoje = Agendamento::where('barbeiro_id', $barbeiro->id)
            ->whereDate('data', Carbon::today())
            ->with('cliente', 'servicos')
            ->orderBy('hora_inicio')
            ->get();

        $proximosAgendamentos = Agendamento::where('barbeiro_id', $barbeiro->id)
            ->whereDate('data', '>', Carbon::today())
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->with('cliente', 'servicos')
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        $totalHoje = $agendamentosHoje->where('status', 'realizado')->count();

        return view('barbeiro.dashboard', compact(
            'barbeiro', 'agendamentosHoje', 'proximosAgendamentos', 'totalHoje'
        ));
    }
}
