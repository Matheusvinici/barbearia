@extends('layouts.app')
@section('title', $edit ? 'Editar Cliente' : 'Novo Cliente')
@section('breadcrumb', 'Clientes')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Cliente' : 'Novo Cliente' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.clientes.update', $cliente) : route('admin.clientes.store') }}" method="POST">
            @csrf @if($edit) @method('PUT') @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $cliente->nome : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $edit ? $cliente->telefone : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $edit ? $cliente->email : '') }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes', $edit ? $cliente->observacoes : '') }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
