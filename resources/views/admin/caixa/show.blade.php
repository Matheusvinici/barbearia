@extends('layouts.app')
@section('title', 'Caixa - '.$caixa->data->format('d/m/Y'))
@section('breadcrumb', 'Financeiro > Caixa')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Resumo do Caixa</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Data:</th><td>{{ $caixa->data->format('d/m/Y') }}</td></tr>
                    <tr><th>Saldo Inicial:</th><td>R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</td></tr>
                    <tr><th>Total Entradas:</th><td class="text-success">R$ {{ number_format($caixa->total_entradas, 2, ',', '.') }}</td></tr>
                    <tr><th>Total Saídas:</th><td class="text-danger">R$ {{ number_format($caixa->total_saidas, 2, ',', '.') }}</td></tr>
                    <tr><th>Saldo Final:</th><td><strong>R$ {{ number_format($caixa->saldo_final, 2, ',', '.') }}</strong></td></tr>
                    <tr><th>Status:</th><td>{!! $caixa->fechado ? '<span class="badge bg-secondary">Fechado</span>' : '<span class="badge bg-success">Aberto</span>' !!}</td></tr>
                    <tr><th>Aberto por:</th><td>{{ $caixa->usuarioAbertura?->name ?? '-' }}</td></tr>
                    @if($caixa->usuarioFechamento)
                    <tr><th>Fechado por:</th><td>{{ $caixa->usuarioFechamento->name }}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Movimentações</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Tipo</th><th>Descrição</th><th>Valor</th></tr></thead>
                    <tbody>
                        @forelse($caixa->movimentacoes as $m)
                        <tr>
                            <td><span class="badge bg-{{ $m->tipo == 'entrada' ? 'success' : 'danger' }}">{{ ucfirst($m->tipo) }}</span></td>
                            <td>{{ $m->descricao }}</td>
                            <td class="{{ $m->tipo == 'entrada' ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($m->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Nenhuma movimentação</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
