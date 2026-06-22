@extends('layouts.app')
@section('title', 'Detalhes do Usuário')
@section('breadcrumb', 'Detalhes do Usuário')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $user->name }}</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width:200px">Nome</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Papéis</th>
                <td>
                    @foreach($user->roles as $r)
                    <span class="badge bg-info">{{ ucfirst($r->name) }}</span>
                    @endforeach
                    @if($user->roles->isEmpty())
                    <small class="text-muted">Nenhum papel atribuído</small>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Criado em</th>
                <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Atualizado em</th>
                <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endSection
