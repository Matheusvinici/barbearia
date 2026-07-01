@extends('layouts.app')
@section('title', 'Meus Agendamentos')
@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<a href="{{ route('barbeiro.dashboard') }}" style="color:inherit;text-decoration:none;">Dashboard</a>
<span class="sep">/</span>
<span class="current">Agendamentos</span>
@endsection

@section('content')
<div class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-calendar"/></svg></div>
            <div>
                <h2 class="panel-title">Meus Agendamentos</h2>
                <div class="panel-subtitle">Gerencie seus atendimentos</div>
            </div>
        </div>
    </div>
    <div class="panel-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Serviços</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agendamentos as $ag)
                <tr>
                    <td>{{ $ag->data->format('d/m/Y') }}</td>
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
                    <td>R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
                    <td>
                        @if($ag->status == 'pendente')
                        <form action="{{ route('barbeiro.agendamentos.confirmar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="action-btn success" title="Confirmar">
                                <svg class="icon icon-sm"><use href="#i-check"/></svg>
                                Confirmar
                            </button>
                        </form>
                        @endif
                        @if(in_array($ag->status, ['pendente', 'confirmado']))
                        <form action="{{ route('barbeiro.agendamentos.realizar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="action-btn info" title="Realizar">
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
                    <td colspan="7" class="text-center" style="padding: 40px; color: var(--text-muted);">
                        Nenhum agendamento encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($agendamentos->hasPages())
    <div class="panel-footer">{{ $agendamentos->links() }}</div>
    @endif
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