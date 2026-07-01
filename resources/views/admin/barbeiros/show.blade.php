@extends('layouts.app')
@section('title', 'Detalhes do Barbeiro')
@section('breadcrumb', 'Barbeiros > Detalhes')

@php
    function barbeiroRoute($name, $params = []) {
        $slug = request()->route('barbearia')?->slug;
        if (!$slug) return route('admin.' . $name, $params);
        $params = is_array($params) ? $params : [$params];
        return route('tenant.admin.' . $name, array_merge([$slug], $params));
    }
@endphp

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $barbeiro->nome }}</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Nome</th><td>{{ $barbeiro->nome }}</td></tr>
            <tr><th>Email</th><td>{{ $barbeiro->email }}</td></tr>
            <tr><th>Telefone</th><td>{{ $barbeiro->telefone ?? '-' }}</td></tr>
            <tr><th>Barbearia</th><td>{{ $barbeiro->barbearia?->nome ?? '-' }}</td></tr>
            <tr><th>Papéis</th><td>
                @foreach($barbeiro->roles as $r)
                <span class="badge bg-info">{{ ucfirst($r->name) }}</span>
                @endforeach
                @if($barbeiro->roles->isEmpty()) - @endif
            </td></tr>
            <tr><th>Comissão</th><td>{{ $barbeiro->comissao_percentual }}%</td></tr>
            <tr><th>Ativo</th><td>{!! $barbeiro->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td></tr>
        </table>

        @if($barbeiro->horarios->count())
        <h5 class="mt-4">Horários de Atendimento</h5>
        @php $dias = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado']; @endphp
        @php $periodos = ['manha'=>'Manhã','tarde'=>'Tarde','noite'=>'Noite']; @endphp
        <table class="table table-bordered">
            <thead><tr><th>Dia</th><th>Período</th><th>Início</th><th>Fim</th></tr></thead>
            <tbody>
                @foreach($barbeiro->horarios->where('ativo', true)->sortBy('dia_semana') as $h)
                <tr>
                    <td>{{ $dias[$h->dia_semana] ?? $h->dia_semana }}</td>
                    <td>{{ $periodos[$h->periodo] ?? $h->periodo ?? '-' }}</td>
                    <td>{{ $h->hora_inicio }}</td>
                    <td>{{ $h->hora_fim }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <a href="{{ barbeiroRoute('barbeiros.edit', $barbeiro) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ barbeiroRoute('barbeiros.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection
