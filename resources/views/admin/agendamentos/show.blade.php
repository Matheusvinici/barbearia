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
            <tr><th>Encaixe</th><td>{{ $agendamento->encaixe ? 'Sim — sinalizado só o intervalo do encaixe' : 'Não' }}</td></tr>
            <tr><th>Usar Plano</th><td>{{ $agendamento->usar_plano ? 'Sim' : 'Não' }}</td></tr>
            <tr><th>Observações</th><td>{{ $agendamento->observacoes ?? '-' }}</td></tr>
            <tr><th>Origem</th><td>{{ $agendamento->origem }}</td></tr>
            <tr><th>Criado por</th><td>{{ $agendamento->creator->name ?? 'Sistema' }}</td></tr>
            <tr><th>Criado em</th><td>{{ $agendamento->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>

        @php $pi = $agendamento->clientePlano ?? $agendamento->plano_info; @endphp
        @if($pi)
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Informações do Plano</h5>
                @if($pi->pago)<span class="badge bg-success">Pago R$ {{ number_format($pi->valor_pago,2,',','.') }} @if($pi->forma_pagamento)({{ $pi->forma_pagamento }})@endif</span>@else<span class="badge bg-warning text-dark">Pagamento pendente</span>@endif
            </div>
            <div class="card-body">
                <div class="row small mb-2">
                    <div class="col-md-4"><strong>Plano:</strong> {{ $pi->plano->nome }} (R$ {{ number_format($pi->plano->valor,2,',','.') }})</div>
                    <div class="col-md-4"><strong>Cliente:</strong> {{ $pi->cliente->nome }}</div>
                    <div class="col-md-4"><strong>Validade:</strong> {{ ($pi->vencimento ?? $pi->data_fim)? ($pi->vencimento ?? $pi->data_fim)->format('d/m/Y') : '-' }} @if($pi->expirado) <span class="badge bg-danger">VENCIDO</span> @else <span class="badge bg-success">{{ $pi->dias_para_vencer }} dias restantes</span> @endif</div>
                </div>
                <div class="mb-2">
                    <strong>Cotas:</strong> {{ $pi->total_restante }}/{{ $pi->total_contratada }} restantes (usado {{ $pi->total_usada }})
                    @if($agendamento->dentro_da_cota)
                        <span class="badge bg-success">Dentro da cota - sem cobrança</span>
                    @else
                        <span class="badge bg-danger">Cota excedida - será cobrado</span>
                    @endif
                    @if($agendamento->usar_plano) <span class="badge bg-info">Usando plano neste agendamento</span> @endif
                </div>
                <table class="table table-sm table-bordered mb-0" style="font-size:12px">
                    <thead><tr><th>Serviço</th><th>Qtd contratada</th><th>Usada</th><th>Restante</th></tr></thead>
                    <tbody>
                        @foreach($pi->plano->quotas as $q)
                            @php $usada = $pi->usos->where('servico_id',$q->servico_id)->count(); $rest = max(0,$q->quantidade-$usada); @endphp
                            <tr class="{{ $rest>0?'':'table-danger' }}"><td>{{ $q->servico->nome }}</td><td>{{ $q->quantidade }}</td><td>{{ $usada }}</td><td>{{ $rest }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <a href="{{ route('admin.agendamentos.edit', $agendamento) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Editar</a>
        <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection
