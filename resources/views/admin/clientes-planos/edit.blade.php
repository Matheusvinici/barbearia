@extends('layouts.app')
@section('title', 'Editar Vínculo')
@section('breadcrumb', 'Clientes Planos')

@section('content')
<div class="card">
    <div class="card-header"><h5>Editar Vínculo - {{ $vinculo->cliente->nome }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.clientes-planos.update', $vinculo) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Cliente</label>
                    <input type="text" class="form-control" value="{{ $vinculo->cliente->nome }} - {{ $vinculo->cliente->telefone }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Plano</label>
                    <select name="plano_id" class="form-control" required>
                        @foreach($planos as $p)
                        <option value="{{ $p->id }}" {{ $vinculo->plano_id == $p->id ? 'selected' : '' }}>{{ $p->nome }} - R$ {{ number_format($p->valor, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ $vinculo->data_inicio->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="{{ $vinculo->data_fim ? $vinculo->data_fim->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $vinculo->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ !$vinculo->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control" value="{{ $vinculo->cpf }}" placeholder="000.000.000-00">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ $vinculo->observacoes }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.clientes-planos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
