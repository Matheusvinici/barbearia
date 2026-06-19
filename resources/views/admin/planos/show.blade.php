@extends('layouts.app')
@section('title', $plano->nome)
@section('breadcrumb', 'Planos')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>{{ $plano->nome }}</h5>
        <div>
            <a href="{{ route('admin.planos.edit', $plano) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('admin.planos.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Nome:</th><td>{{ $plano->nome }}</td></tr>
                    <tr><th>Valor:</th><td>R$ {{ number_format($plano->valor, 2, ',', '.') }}</td></tr>
                    <tr><th>Ativo:</th><td>{!! $plano->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td></tr>
                    @if($plano->descricao)
                    <tr><th>Descrição:</th><td>{{ $plano->descricao }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <hr>
        <h6>Cotas Inclusas</h6>
        <table class="table table-sm">
            <thead><tr><th>Serviço</th><th>Quantidade</th></tr></thead>
            <tbody>
                @forelse($plano->quotas as $q)
                <tr>
                    <td>{{ $q->servico->nome }}</td>
                    <td>{{ $q->quantidade }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-muted">Nenhum serviço vinculado</td></tr>
                @endforelse
            </tbody>
        </table>

        <hr>
        <h6>Clientes Vinculados</h6>
        <table class="table table-sm">
            <thead><tr><th>Cliente</th><th>Telefone</th><th>Início</th><th>Fim</th><th>Ativo</th></tr></thead>
            <tbody>
                @forelse($plano->clientes as $cp)
                <tr>
                    <td>{{ $cp->cliente->nome }}</td>
                    <td>{{ $cp->cliente->telefone }}</td>
                    <td>{{ $cp->data_inicio->format('d/m/Y') }}</td>
                    <td>{{ $cp->data_fim ? $cp->data_fim->format('d/m/Y') : '-' }}</td>
                    <td>{!! $cp->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted">Nenhum cliente vinculado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
