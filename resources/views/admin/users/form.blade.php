@extends('layouts.app')
@section('title', $edit ? 'Editar Usuário' : 'Novo Usuário')
@section('breadcrumb', $edit ? 'Editar Usuário' : 'Novo Usuário')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Usuário' : 'Novo Usuário' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if($edit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $edit ? $user->name : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $edit ? $user->email : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Senha{{ $edit ? ' (deixe em branco para manter)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $edit ? '' : 'required' }}>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Papéis</label>
                    <div class="border rounded p-2">
                        @forelse($roles as $r)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}"
                                   id="role{{ $r->id }}"
                                   {{ $edit && $user->hasRole($r->name) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role{{ $r->id }}">{{ ucfirst($r->name) }}</label>
                        </div>
                        @empty
                        <small class="text-muted">Nenhum papel disponível. Execute: php artisan db:seed --class=PermissionSeeder</small>
                        @endforelse
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
