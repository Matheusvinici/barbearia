@extends('layouts.app')
@section('title', 'Meu Dashboard')
@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<span class="current">Dashboard</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Bem-vindo, {{ $barbeiro->nome }}</span>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-calendar"/></svg></div>
        </div>
        <div class="stat-label">Agendamentos Hoje</div>
        <div class="stat-value">{{ $agendamentosHoje->count() }}</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-check"/></svg></div>
        </div>
        <div class="stat-label">Realizados Hoje</div>
        <div class="stat-value">{{ $totalHoje }}</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-clock"/></svg></div>
        </div>
        <div class="stat-label">Pendentes</div>
        <div class="stat-value">{{ $agendamentosHoje->where('status', 'pendente')->count() }}</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-trend-up"/></svg></div>
        </div>
        <div class="stat-label">Próximos Agendamentos</div>
        <div class="stat-value">{{ $proximosAgendamentos->count() }}</div>
    </div>
</div>

<div class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-calendar"/></svg></div>
            <div>
                <h2 class="panel-title">Agendamentos de Hoje</h2>
                <div class="panel-subtitle">{{ $agendamentosHoje->count() }} agendamento(s) para hoje</div>
            </div>
        </div>
    </div>
    <div class="panel-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Serviços</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agendamentosHoje as $ag)
                <tr>
                    <td>{{ $ag->hora_inicio->format('H:i') }}</td>
                    <td>
                        <div class="avatar-row">
                            <div class="avatar-circle">{{ mb_substr($ag->cliente->nome, 0, 1) }}</div>
                            <div>
                                <div class="avatar-name">{{ $ag->cliente->nome }}</div>
                                <div style="font-size:12px;color:var(--text-muted);">{{ $ag->cliente->telefone }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($ag->servicos as $s)
                        <span class="badge-c">{{ $s->nome }}</span>
                        @endforeach
                    </td>
                    <td>
                        @php
                            $statusClass = match($ag->status) {
                                'pendente' => 'warning',
                                'confirmado' => 'info',
                                'realizado' => 'success',
                                'cancelado' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge-c {{ $statusClass }}">{{ ucfirst($ag->status) }}</span>
                    </td>
                    <td>
                        @if($ag->status == 'pendente')
                        <form action="{{ route('barbeiro.agendamentos.confirmar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="action-btn success" title="Confirmar presença">
                                <svg class="icon icon-sm"><use href="#i-check"/></svg>
                                Confirmar
                            </button>
                        </form>
                        @endif
                        @if(in_array($ag->status, ['pendente', 'confirmado']))
                        <form action="{{ route('barbeiro.agendamentos.realizar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="action-btn info" title="Marcar como realizado">
                                <svg class="icon icon-sm"><use href="#i-scissor"/></svg>
                                Realizar
                            </button>
                        </form>
                        <form action="{{ route('barbeiro.agendamentos.cancelar', $ag) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar agendamento?')">
                            @csrf @method('PUT')
                            <button class="action-btn delete" title="Cancelar">
                                <svg class="icon icon-sm"><use href="#i-x"/></svg>
                                Cancelar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 40px; color: var(--text-muted);">
                        Nenhum agendamento para hoje
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <div class="panel fade-in d6">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-trend-up"/></svg></div>
                    <div>
                        <h2 class="panel-title">Próximos Agendamentos</h2>
                        <div class="panel-subtitle">Agendamentos futuros</div>
                    </div>
                </div>
            </div>
            <div class="panel-body p-0">
                <table class="data-table">
                    <thead>
                        <tr><th>Data</th><th>Hora</th><th>Cliente</th></tr>
                    </thead>
                    <tbody>
                        @forelse($proximosAgendamentos as $ag)
                        <tr>
                            <td>{{ $ag->data->format('d/m/Y') }}</td>
                            <td>{{ $ag->hora_inicio->format('H:i') }}</td>
                            <td>
                                <div class="avatar-row">
                                    <div class="avatar-circle sm">{{ mb_substr($ag->cliente->nome, 0, 1) }}</div>
                                    <div class="avatar-name">{{ $ag->cliente->nome }}</div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center" style="padding: 40px; color: var(--text-muted);">Nenhum próximo agendamento</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel fade-in d7">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-info"/></svg></div>
                    <div>
                        <h2 class="panel-title">Acesso Rápido</h2>
                        <div class="panel-subtitle">Suas permissões</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @can('agendamento.view')
                    <a href="{{ route('barbeiro.agendamentos.index') }}" class="action-btn" style="padding:8px 16px;height:auto;">
                        <svg class="icon icon-sm"><use href="#i-calendar"/></svg>
                        Ver Agendamentos
                    </a>
                    @endcan
                    @can('agendamento.confirmar')
                    <span class="action-btn" style="padding:8px 16px;height:auto;cursor:default;opacity:0.8;">
                        <svg class="icon icon-sm"><use href="#i-check"/></svg>
                        Confirmar
                    </span>
                    @endcan
                    @can('agendamento.realizar')
                    <span class="action-btn" style="padding:8px 16px;height:auto;cursor:default;opacity:0.8;">
                        <svg class="icon icon-sm"><use href="#i-scissor"/></svg>
                        Realizar
                    </span>
                    @endcan
                    @can('agendamento.cancelar')
                    <span class="action-btn" style="padding:8px 16px;height:auto;cursor:default;opacity:0.8;">
                        <svg class="icon icon-sm"><use href="#i-x"/></svg>
                        Cancelar
                    </span>
                    @endcan
                </div>
                <div style="margin-top:16px;font-size:13px;color:var(--text-muted);line-height:1.5;">
                    Utilize os botões na tabela de agendamentos para gerenciar cada atendimento.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.action-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 8px;
    border: 1.5px solid;
    background: transparent;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 150ms;
    font-size: 12.5px;
    font-weight: 600;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.action-btn.success { color: var(--success); border-color: var(--success); }
.action-btn.success:hover { background: var(--success-bg); }
.action-btn.info { color: var(--info); border-color: var(--info); }
.action-btn.info:hover { background: var(--info-bg); }
.action-btn.delete { color: var(--danger); border-color: var(--danger); }
.action-btn.delete:hover { background: var(--danger-bg); }
form.d-inline { display: inline; }
</style>
@endpush