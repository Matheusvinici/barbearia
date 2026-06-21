<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Despesa;
use App\Models\Caixa;
use App\Models\Servico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    public function index()
    {
        return view('admin.relatorios.index');
    }

    public function faturamento(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));
        $barbeariaId = $request->get('barbearia_id');
        $barbeiroId = $request->get('barbeiro_id');

        $query = Agendamento::where('status', 'realizado')
            ->whereDate('data', '>=', $dataInicio)
            ->whereDate('data', '<=', $dataFim)
            ->with('barbeiro');

        if ($barbeariaId) {
            $query->where('barbearia_id', $barbeariaId);
        }

        if ($barbeiroId) {
            $query->where('barbeiro_id', $barbeiroId);
        }

        $agendamentos = $query->get();

        $totalFaturamento = $agendamentos->sum('total');
        $porBarbeiro = $agendamentos->groupBy('barbeiro.nome')->map(function ($items) {
            return [
                'quantidade' => $items->count(),
                'total' => $items->sum('total'),
            ];
        });

        $despesas = Despesa::where('pago', true)
            ->whereDate('data_pagamento', '>=', $dataInicio)
            ->whereDate('data_pagamento', '<=', $dataFim)
            ->sum('valor');

        $lucroLiquido = $totalFaturamento - $despesas;

        $caixas = Caixa::whereDate('data', '>=', $dataInicio)
            ->whereDate('data', '<=', $dataFim)
            ->get();

        $barbearias = Barbearia::orderBy('nome')->get();
        $barbeiros = Barbeiro::where('ativo', true)->get();

        return view('admin.relatorios.faturamento', compact(
            'dataInicio', 'dataFim', 'totalFaturamento', 'porBarbeiro',
            'despesas', 'lucroLiquido', 'caixas', 'barbearias', 'barbeiros',
            'barbeariaId', 'barbeiroId'
        ));
    }

    public function servicos(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $servicos = Servico::withCount(['agendamentos' => function ($q) use ($dataInicio, $dataFim) {
            $q->where('status', 'realizado')
              ->whereDate('data', '>=', $dataInicio)
              ->whereDate('data', '<=', $dataFim);
        }])->get();

        return view('admin.relatorios.servicos', compact('dataInicio', 'dataFim', 'servicos'));
    }

    public function pdfFaturamento(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $agendamentos = Agendamento::where('status', 'realizado')
            ->whereDate('data', '>=', $dataInicio)
            ->whereDate('data', '<=', $dataFim)
            ->with('barbeiro', 'cliente', 'servicos')
            ->get();

        $totalFaturamento = $agendamentos->sum('total');

        $pdf = Pdf::loadView('admin.relatorios.pdf-faturamento', compact(
            'agendamentos', 'totalFaturamento', 'dataInicio', 'dataFim'
        ));

        return $pdf->download("relatorio-faturamento-{$dataInicio}-{$dataFim}.pdf");
    }
}
