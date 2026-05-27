@extends('layouts.app')
@section('title', 'Detalhes do Barbeiro')
@section('breadcrumb', 'Barbeiros')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>{{ $barbeiro->nome }}</h5>
        <div>
            <a href="{{ route('admin.barbeiros.edit', $barbeiro) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('admin.barbeiros.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Nome:</th><td>{{ $barbeiro->nome }}</td></tr>
                    <tr><th>Email:</th><td>{{ $barbeiro->email }}</td></tr>
                    <tr><th>Telefone:</th><td>{{ $barbeiro->telefone ?? '-' }}</td></tr>
                    <tr><th>Comissão:</th><td>{{ $barbeiro->comissao_percentual }}%</td></tr>
                    <tr><th>Ativo:</th><td>{!! $barbeiro->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
