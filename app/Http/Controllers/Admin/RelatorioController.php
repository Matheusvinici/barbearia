<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Despesa;
use App\Models\Caixa;
use App\Models\Barbeiro;
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

        $agendamentos = Agendamento::where('status', 'realizado')
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->with('barbeiro')
            ->get();

        $totalFaturamento = $agendamentos->sum('total');
        $porBarbeiro = $agendamentos->groupBy('barbeiro.nome')->map(function ($items) {
            return [
                'quantidade' => $items->count(),
                'total' => $items->sum('total'),
            ];
        });

        $despesas = Despesa::where('pago', true)
            ->whereBetween('data_pagamento', [$dataInicio, $dataFim])
            ->sum('valor');

        $lucroLiquido = $totalFaturamento - $despesas;

        $caixas = Caixa::whereBetween('data', [$dataInicio, $dataFim])->get();

        return view('admin.relatorios.faturamento', compact(
            'dataInicio', 'dataFim', 'totalFaturamento', 'porBarbeiro', 'despesas', 'lucroLiquido', 'caixas'
        ));
    }

    public function servicos(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $servicos = Servico::withCount(['agendamentos' => function ($q) use ($dataInicio, $dataFim) {
            $q->where('status', 'realizado')->whereBetween('data', [$dataInicio, $dataFim]);
        }])->get();

        return view('admin.relatorios.servicos', compact('dataInicio', 'dataFim', 'servicos'));
    }

    public function pdfFaturamento(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $agendamentos = Agendamento::where('status', 'realizado')
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->with('barbeiro', 'cliente', 'servicos')
            ->get();

        $totalFaturamento = $agendamentos->sum('total');

        $pdf = Pdf::loadView('admin.relatorios.pdf-faturamento', compact(
            'agendamentos', 'totalFaturamento', 'dataInicio', 'dataFim'
        ));

        return $pdf->download("relatorio-faturamento-{$dataInicio}-{$dataFim}.pdf");
    }
}
