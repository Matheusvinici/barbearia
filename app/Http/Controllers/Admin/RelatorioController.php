<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\CaixaMovimentacao;
use App\Models\Despesa;
use App\Models\Servico;
use App\Models\Venda;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    use TenantScoped;

    public function index(Request $request)
    {
        $mes = (int) $request->get('mes', Carbon::now()->month);
        $ano = (int) $request->get('ano', Carbon::now()->year);
        $tipo = $request->get('tipo', '');
        $barbeariaId = $request->get('barbearia_id');

        $dataInicio = Carbon::create($ano, $mes, 1)->startOfDay();
        $dataFim = Carbon::create($ano, $mes, 1)->endOfMonth()->endOfDay();

        $query = CaixaMovimentacao::with('origem')->whereBetween('created_at', [$dataInicio, $dataFim]);

        if ($this->isTenantContext()) {
            $query = $this->applyTenantScope($query);
        } elseif ($barbeariaId) {
            $query->where('barbearia_id', $barbeariaId);
        }

        if (in_array($tipo, ['entrada', 'saida'])) {
            $query->where('tipo', $tipo);
        }

        $movimentacoes = $query->orderByDesc('created_at')->get();

        $entradas = $movimentacoes->where('tipo', 'entrada');
        $saidas = $movimentacoes->where('tipo', 'saida');

        $totalEntradas = round($entradas->sum('valor'), 2);
        $totalSaidas = round($saidas->sum('valor'), 2);
        $saldo = round($totalEntradas - $totalSaidas, 2);

        $totalAtendimentos = $entradas->where('origem_type', Agendamento::class)->count();
        $totalVendas = $entradas->where('origem_type', Venda::class)->count();
        $ticketMedio = $totalAtendimentos > 0 ? round($totalEntradas / $totalAtendimentos, 2) : 0;

        $porBarbeiro = $entradas
            ->filter(fn ($m) => $m->origem_type === Agendamento::class && $m->origem?->barbeiro)
            ->groupBy(fn ($m) => $m->origem->barbeiro->nome)
            ->map(fn ($items) => [
                'quantidade' => $items->count(),
                'total' => round($items->sum('valor'), 2),
            ])
            ->sortByDesc('total');

        $porCategoriaSaida = $saidas
            ->filter(fn ($m) => $m->origem_type === Despesa::class && $m->origem?->categoria)
            ->groupBy(fn ($m) => $m->origem->categoria)
            ->map(fn ($items) => round($items->sum('valor'), 2))
            ->sortByDesc(fn ($v) => $v);

        $porDia = $movimentacoes
            ->groupBy(fn ($m) => (int) $m->created_at->format('d'))
            ->map(fn ($items) => [
                'entradas' => round($items->where('tipo', 'entrada')->sum('valor'), 2),
                'saidas' => round($items->where('tipo', 'saida')->sum('valor'), 2),
            ]);

        $barbearias = Barbearia::orderBy('nome')->get();
        $mesesList = range(1, 12);
        $anosList = range(Carbon::now()->year - 3, Carbon::now()->year + 1);

        return view('admin.relatorios.index', compact(
            'mes', 'ano', 'tipo', 'barbeariaId',
            'movimentacoes', 'entradas', 'saidas',
            'totalEntradas', 'totalSaidas', 'saldo',
            'totalAtendimentos', 'totalVendas', 'ticketMedio',
            'porBarbeiro', 'porCategoriaSaida', 'porDia',
            'barbearias', 'mesesList', 'anosList',
            'dataInicio', 'dataFim'
        ));
    }

    public function faturamento(Request $request)
    {
        return redirect()->route(
            $this->isTenantContext()
                ? 'tenant.admin.relatorios.index'
                : 'admin.relatorios.index',
            array_merge(
                $this->isTenantContext() ? [$this->getTenant()->slug] : [],
                $request->query()
            )
        );
    }

    public function servicos(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $servicos = Servico::withCount(['agendamentos' => function ($q) use ($dataInicio, $dataFim) {
            $q->where('status', 'realizado')
              ->whereDate('data', '>=', $dataInicio)
              ->whereDate('data', '<=', $dataFim);
        }]);

        if ($this->isTenantContext()) {
            $servicos = $this->applyTenantScope($servicos);
        }

        $servicos = $servicos->get();

        $servicosData = $servicos->map(function ($s) {
            $qtd = (int) $s->agendamentos_count;
            return [
                'nome' => $s->nome,
                'quantidade' => $qtd,
                'receita' => $qtd * $s->preco,
                'preco_medio' => $s->preco,
                'duracao_media' => $s->duracao_minutos,
            ];
        });

        $receitaTotal = $servicosData->sum('receita');

        return view('admin.relatorios.servicos', compact('dataInicio', 'dataFim', 'servicos', 'servicosData', 'receitaTotal'));
    }

    public function pdfFaturamento(Request $request)
    {
        $mes = (int) $request->get('mes', Carbon::now()->month);
        $ano = (int) $request->get('ano', Carbon::now()->year);
        $tipo = $request->get('tipo', '');
        $barbeariaId = $request->get('barbearia_id');

        $dataInicio = Carbon::create($ano, $mes, 1)->startOfDay();
        $dataFim = Carbon::create($ano, $mes, 1)->endOfMonth()->endOfDay();

        $query = CaixaMovimentacao::with('origem')->whereBetween('created_at', [$dataInicio, $dataFim]);

        if ($this->isTenantContext()) {
            $query = $this->applyTenantScope($query);
        } elseif ($barbeariaId) {
            $query->where('barbearia_id', $barbeariaId);
        }

        if (in_array($tipo, ['entrada', 'saida'])) {
            $query->where('tipo', $tipo);
        }

        $movimentacoes = $query->orderBy('created_at')->get();

        $totalEntradas = round($movimentacoes->where('tipo', 'entrada')->sum('valor'), 2);
        $totalSaidas = round($movimentacoes->where('tipo', 'saida')->sum('valor'), 2);
        $saldo = round($totalEntradas - $totalSaidas, 2);

        $nomeMes = Carbon::create($ano, $mes, 1)->translatedFormat('F \d\e Y');

        $pdf = Pdf::loadView('admin.relatorios.pdf-faturamento', compact(
            'movimentacoes', 'totalEntradas', 'totalSaidas', 'saldo',
            'mes', 'ano', 'nomeMes', 'tipo'
        ));

        return $pdf->download("relatorio-financeiro-{$ano}-{$mes}.pdf");
    }
}