@extends('layouts.app')
@section('title', 'Faturamento')
@section('breadcrumb', 'Financeiro > Relatórios > Faturamento')

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <label>De:</label>
            <input type="date" name="data_inicio" class="form-control form-control-sm" value="{{ $dataInicio }}" style="width:auto">
            <label>Até:</label>
            <input type="date" name="data_fim" class="form-control form-control-sm" value="{{ $dataFim }}" style="width:auto">
            <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i> Filtrar</button>
            <a href="{{ route('admin.relatorios.faturamento-pdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
        </form>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Faturamento Bruto</span>
                        <span class="info-box-number">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Despesas Pagas</span>
                        <span class="info-box-number">R$ {{ number_format($despesas, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Lucro Líquido</span>
                        <span class="info-box-number">R$ {{ number_format($lucroLiquido, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo Caixa</span>
                        <span class="info-box-number">R$ {{ number_format($caixas->sum('saldo_final'), 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Faturamento por Barbeiro</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead><tr><th>Barbeiro</th><th>Qtd</th><th>Total</th></tr></thead>
                            <tbody>
                                @foreach($porBarbeiro as $nome => $dados)
                                <tr>
                                    <td>{{ $nome }}</td>
                                    <td>{{ $dados['quantidade'] }}</td>
                                    <td>R$ {{ number_format($dados['total'], 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Resumo do Período</h5></div>
                    <div class="card-body">
                        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
                        <p><strong>Dias:</strong> {{ \Carbon\Carbon::parse($dataInicio)->diffInDays(\Carbon\Carbon::parse($dataFim)) + 1 }} dias</p>
                        <p><strong>Média por dia:</strong> R$ {{ number_format($totalFaturamento / max(\Carbon\Carbon::parse($dataInicio)->diffInDays(\Carbon\Carbon::parse($dataFim)) + 1, 1), 2, ',', '.') }}</p>
                        <p><strong>Total de Caixas:</strong> {{ $caixas->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
