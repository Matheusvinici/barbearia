@extends('layouts.app')
@section('title', $edit ? 'Editar Barbearia' : 'Nova Barbearia')
@section('breadcrumb', $edit ? 'Editar Barbearia' : 'Nova Barbearia')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Barbearia' : 'Nova Barbearia' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.barbearias.update', $barbearia) : route('admin.barbearias.store') }}" method="POST">
            @csrf
            @if($edit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $barbearia->nome : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Barbearia Matriz (deixe vazio se for matriz)</label>
                    <select name="parent_id" class="form-control">
                        <option value="">É matriz</option>
                        @foreach($matrizes as $m)
                        <option value="{{ $m->id }}" {{ old('parent_id', $edit ? $barbearia->parent_id : '') == $m->id ? 'selected' : '' }}>{{ $m->nome }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Selecione a matriz se esta for uma filial</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Bairro</label>
                    <input type="text" name="bairro" class="form-control" value="{{ old('bairro', $edit ? $barbearia->bairro : '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Cidade</label>
                    <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $edit ? $barbearia->cidade : '') }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" rows="3">{{ old('descricao', $edit ? $barbearia->descricao : '') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.barbearias.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
