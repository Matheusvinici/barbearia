@extends('layouts.app')
@section('title', 'Relatório de Serviços')
@section('breadcrumb', 'Financeiro > Relatórios > Serviços')

@section('content')
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <label>De:</label>
            <input type="date" name="data_inicio" class="form-control form-control-sm" value="{{ $dataInicio }}" style="width:auto">
            <label>Até:</label>
            <input type="date" name="data_fim" class="form-control form-control-sm" value="{{ $dataFim }}" style="width:auto">
            <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i> Filtrar</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Serviço</th><th>Quantidade Realizada</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($servicos as $s)
                <tr>
                    <td>{{ $s->nome }}</td>
                    <td><span class="badge bg-primary">{{ $s->agendamentos_count }} vezes</span></td>
                    <td>
                        <a href="{{ route('admin.relatorios.faturamento', ['servico_id' => $s->id]) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-search"></i> Ver detalhes
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
