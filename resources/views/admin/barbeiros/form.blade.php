@extends('layouts.app')
@section('title', $edit ? 'Editar Barbeiro' : 'Novo Barbeiro')
@section('breadcrumb', $edit ? 'Editar Barbeiro' : 'Novo Barbeiro')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Barbeiro' : 'Novo Barbeiro' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.barbeiros.update', $barbeiro) : route('admin.barbeiros.store') }}" method="POST">
            @csrf
            @if($edit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $barbeiro->nome : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $edit ? $barbeiro->email : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Senha{{ $edit ? ' (deixe em branco para manter)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $edit ? '' : 'required' }}>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $edit ? $barbeiro->telefone : '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label>Comissão (%)</label>
                    <input type="number" step="0.01" name="comissao_percentual" class="form-control" value="{{ old('comissao_percentual', $edit ? $barbeiro->comissao_percentual : 50) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $edit && $barbeiro->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $edit && !$barbeiro->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.barbeiros.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
