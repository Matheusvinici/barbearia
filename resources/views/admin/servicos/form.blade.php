@extends('layouts.app')
@section('title', $edit ? 'Editar Serviço' : 'Novo Serviço')
@section('breadcrumb', 'Serviços')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Serviço' : 'Novo Serviço' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.servicos.update', $servico) : route('admin.servicos.store') }}" method="POST">
            @csrf @if($edit) @method('PUT') @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $servico->nome : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Preço (R$)</label>
                    <input type="number" step="0.01" name="preco" class="form-control" value="{{ old('preco', $edit ? $servico->preco : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Duração (minutos)</label>
                    <input type="number" name="duracao_minutos" class="form-control" value="{{ old('duracao_minutos', $edit ? $servico->duracao_minutos : 30) }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" rows="2">{{ old('descricao', $edit ? $servico->descricao : '') }}</textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $edit && $servico->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $edit && !$servico->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.servicos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
