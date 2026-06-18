@extends('layouts.app')
@section('title', $servico->nome)
@section('breadcrumb', 'Serviços')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>{{ $servico->nome }}</h5>
        <div>
            <a href="{{ route('admin.servicos.edit', $servico) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('admin.servicos.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @if($servico->foto)
            <div class="col-md-4 text-center mb-3">
                <img src="{{ $servico->foto_url }}" alt="{{ $servico->nome }}" class="img-fluid rounded" style="max-height:250px;object-fit:cover">
            </div>
            @endif
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr><th>Nome:</th><td>{{ $servico->nome }}</td></tr>
                    <tr><th>Preço:</th><td>R$ {{ number_format($servico->preco, 2, ',', '.') }}</td></tr>
                    <tr><th>Duração:</th><td>{{ $servico->duracao_minutos }} minutos</td></tr>
                    <tr><th>Descrição:</th><td>{{ $servico->descricao ?? '-' }}</td></tr>
                    <tr><th>Ativo:</th><td>{!! $servico->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection