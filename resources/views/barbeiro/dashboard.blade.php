@extends('layouts.app')
@section('title', 'Meu Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $agendamentosHoje->count() }}</h3>
                <p>Agendamentos Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <a href="{{ route('barbeiro.agendamentos.index') }}" class="small-box-footer">Ver todos <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalHoje }}</h3>
                <p>Realizados Hoje</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $agendamentosHoje->where('status', 'pendente')->count() }}</h3>
                <p>Pendentes</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $proximosAgendamentos->count() }}</h3>
                <p>Próximos Agendamentos</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Agendamentos de Hoje</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Hora</th><th>Cliente</th><th>Serviços</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                        @forelse($agendamentosHoje as $ag)
                        <tr>
                            <td>{{ substr($ag->hora_inicio, 0, 5) }}</td>
                            <td>{{ $ag->cliente->nome }}<br><small>{{ $ag->cliente->telefone }}</small></td>
                            <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                            <td><span class="badge-status status-{{ $ag->status }}">{{ ucfirst($ag->status) }}</span></td>
                            <td>
                                @if($ag->status == 'pendente')
                                <form action="{{ route('barbeiro.agendamentos.confirmar', $ag) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-success" title="Confirmar presença"><i class="fas fa-check"></i></button>
                                </form>
                                @endif
                                @if(in_array($ag->status, ['pendente', 'confirmado']))
                                <form action="{{ route('barbeiro.agendamentos.realizar', $ag) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-primary" title="Marcar como realizado"><i class="fas fa-cut"></i></button>
                                </form>
                                <form action="{{ route('barbeiro.agendamentos.cancelar', $ag) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar agendamento?')">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-danger" title="Cancelar"><i class="fas fa-times"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Nenhum agendamento para hoje</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Próximos Agendamentos</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Data</th><th>Hora</th><th>Cliente</th></tr></thead>
                    <tbody>
                        @forelse($proximosAgendamentos as $ag)
                        <tr>
                            <td>{{ $ag->data->format('d/m') }}</td>
                            <td>{{ substr($ag->hora_inicio, 0, 5) }}</td>
                            <td>{{ $ag->cliente->nome }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Nenhum próximo agendamento</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5>Bem-vindo, {{ $barbeiro->nome }}!</h5></div>
            <div class="card-body">
                <p><strong>Estes são seus agendamentos de hoje.</strong></p>
                <p class="text-muted">Use os botões para confirmar a presença do cliente ou marcar o serviço como realizado.</p>
            </div>
        </div>
    </div>
</div>
@endsection
