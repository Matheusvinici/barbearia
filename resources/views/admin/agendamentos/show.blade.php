@extends('layouts.app')
@section('title', 'Detalhes do Agendamento')
@section('breadcrumb', 'Agendamentos > Detalhes')

@section('content')
<div class="card">
    <div class="card-header"><h5>Agendamento #{{ $agendamento->id }}</h5></div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th>Cliente</th><td>{{ $agendamento->cliente->nome }}<br><small>{{ $agendamento->cliente->telefone }}</small></td></tr>
            <tr><th>Barbearia</th><td>{{ $agendamento->barbearia?->nome ?? '-' }}</td></tr>
            <tr><th>Barbeiro</th><td>{{ $agendamento->barbeiro->nome }}</td></tr>
            <tr><th>Data</th><td>{{ $agendamento->data->format('d/m/Y') }}</td></tr>
            <tr><th>Horário</th><td>{{ $agendamento->hora_inicio->format('H:i') }} - {{ $agendamento->hora_fim->format('H:i') }}</td></tr>
            <tr><th>Serviços</th><td>
                @foreach($agendamento->servicos as $s)
                <span class="badge bg-info">{{ $s->nome }} (R$ {{ number_format($s->pivot->preco_praticado, 2, ',', '.') }})</span>
                @endforeach
            </td></tr>
            <tr><th>Valor Total</th><td>R$ {{ number_format($agendamento->total ?? 0, 2, ',', '.') }}</td></tr>
            <tr><th>Status</th><td><span class="badge-status status-{{ $agendamento->status }}">{{ ucfirst($agendamento->status) }}</span></td></tr>
            <tr><th>Forma Pagamento</th><td>{{ $agendamento->forma_pagamento ?? '-' }}</td></tr>
            <tr><th>Usar Plano</th><td>{{ $agendamento->usar_plano ? 'Sim' : 'Não' }}</td></tr>
            <tr><th>Observações</th><td>{{ $agendamento->observacoes ?? '-' }}</td></tr>
            <tr><th>Origem</th><td>{{ $agendamento->origem }}</td></tr>
            <tr><th>Criado por</th><td>{{ $agendamento->creator->name ?? 'Sistema' }}</td></tr>
            <tr><th>Criado em</th><td>{{ $agendamento->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>

        @if($agendamento->plano_info && $agendamento->usar_plano)
        <div class="card mt-3">
            <div class="card-header"><h5>Informações do Plano</h5></div>
            <div class="card-body">
                <p><strong>Plano:</strong> {{ $agendamento->plano_info->plano->nome }}</p>
                <p><strong>Cliente:</strong> {{ $agendamento->plano_info->cliente->nome }}</p>
                @if($agendamento->dentro_da_cota)
                    <span class="badge bg-success">Dentro da cota</span>
                @else
                    <span class="badge bg-danger">Cota excedida</span>
                @endif
            </div>
        </div>
        @endif

        <a href="{{ route('admin.agendamentos.edit', $agendamento) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection
