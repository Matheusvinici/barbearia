@extends('layouts.app')
@section('title', 'Agendamento #'.$agendamento->id)
@section('breadcrumb', 'Agendamentos')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Agendamento #{{ $agendamento->id }}</h5>
        <div>
            <a href="{{ route('admin.agendamentos.edit', $agendamento) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-list"></i> Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Cliente:</th><td>{{ $agendamento->cliente->nome }}<br><small>{{ $agendamento->cliente->telefone }}</small></td></tr>
                    <tr><th>Barbeiro:</th><td>{{ $agendamento->barbeiro->nome }}</td></tr>
                    <tr><th>Data:</th><td>{{ $agendamento->data->format('d/m/Y') }}</td></tr>
                    <tr><th>Horário:</th><td>{{ $agendamento->hora_inicio->format('H:i') }} às {{ $agendamento->hora_fim->format('H:i') }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr><th>Status:</th><td><span class="badge-status status-{{ $agendamento->status }}">{{ ucfirst($agendamento->status) }}</span></td></tr>
                    <tr><th>Serviços:</th><td>
                        <ul class="mb-0">
                            @foreach($agendamento->servicos as $s)
                            <li>{{ $s->nome }} - R$ {{ number_format($s->pivot->preco_praticado, 2, ',', '.') }}</li>
                            @endforeach
                        </ul>
                    </td></tr>
                    <tr><th>Total:</th><td><strong>R$ {{ number_format($agendamento->total ?? 0, 2, ',', '.') }}</strong></td></tr>
                    <tr><th>Origem:</th><td><span class="badge bg-{{ $agendamento->origem == 'bot' ? 'success' : 'secondary' }}">{{ $agendamento->origem }}</span></td></tr>
                    @php $cpInfo = $agendamento->plano_info; @endphp
                    @if($cpInfo)
                    <tr>
                        <th>Plano:</th>
                        <td>
                            {{ $cpInfo->plano->nome }}
                            @if($agendamento->dentro_da_cota)
                                <span class="badge bg-success">Dentro da cota</span>
                            @else
                                <span class="badge bg-danger">Cota excedida</span>
                            @endif
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @if($agendamento->observacoes)
        <div class="mt-3 p-3 bg-light rounded">
            <strong>Observações:</strong><br>{{ $agendamento->observacoes }}
        </div>
        @endif
    </div>
</div>
@endsection
