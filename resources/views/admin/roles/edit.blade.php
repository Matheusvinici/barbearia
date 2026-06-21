@extends('layouts.app')
@section('title', 'Editar Papel')
@section('breadcrumb', 'Papéis > Editar')

@section('content')
<div class="card">
    <div class="card-header"><h5>Editar Papel: {{ $role->name }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Nome do Papel</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label>Permissões</label>
                    <div class="border rounded p-2" style="max-height:300px;overflow-y:auto">
                        @foreach($permissions as $p)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->id }}"
                                   id="perm{{ $p->id }}" {{ $role->hasPermissionTo($p->name) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="perm{{ $p->id }}">{{ $p->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
