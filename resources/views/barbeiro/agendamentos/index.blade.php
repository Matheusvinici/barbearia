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
        <div class="table-wrap">
            <table class="appointments-table">
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
                    @php
                        $statusClass = match($ag->status) {
                            'pendente' => 'warning',
                            'confirmado' => 'info',
                            'realizado' => 'success',
                            'cancelado' => 'danger',
                            default => 'secondary'
                        };
                        $horaAgd = $ag->hora_inicio->format('H:i');
                        $pagamento = $ag->forma_pagamento ?? '—';
                    @endphp
                    <tr>
                        <td data-label="Data">{{ $ag->data->format('d/m/Y') }}</td>
                        <td data-label="Hora">{{ $horaAgd }}</td>
                        <td data-label="Cliente">
                            <div class="avatar-row">
                                <div class="avatar-circle">{{ mb_substr($ag->cliente->nome, 0, 1) }}</div>
                                <div>
                                    <div class="avatar-name">{{ $ag->cliente->nome }}</div>
                                    <div style="font-size:12px;color:var(--text-muted);">{{ $ag->cliente->telefone }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Serviços">
                            @foreach($ag->servicos as $s)
                            <span class="badge-c">{{ $s->nome }}</span>
                            @endforeach
                        </td>
                        <td data-label="Status">
                            <span class="badge-c {{ $statusClass }}">{{ ucfirst($ag->status) }}</span>
                        </td>
                        <td data-label="Valor" class="value-cell">
                            R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}
                            @if($pagamento !== '—')
                                <span class="badge-c outlined" style="font-size:10px;padding:1px 5px;margin-top:2px;display:inline-block;">{{ $pagamento }}</span>
                            @endif
                        </td>
                        <td data-label="Ações" class="actions-cell">
                            @if(in_array($ag->status, ['pendente', 'confirmado']))
                                @php
                                    $realizarRoute = route('barbeiro.agendamentos.realizar', $ag);
                                @endphp
                                <button class="action-btn warning" title="Realizar" data-action="{{ $realizarRoute }}"
                                    onclick="abrirModalRealizar(this, '{{ addslashes($ag->cliente->nome) }}', '{{ $horaAgd }}')">
                                    <svg class="icon icon-sm"><use href="#i-scissor"/></svg>
                                    <span class="action-label">Realizar</span>
                                </button>
                                <form action="{{ route('barbeiro.agendamentos.cancelar', $ag) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar agendamento?')">
                                    @csrf @method('PUT')
                                    <button class="action-btn delete" title="Cancelar">
                                        <svg class="icon icon-sm"><use href="#i-x"/></svg>
                                        <span class="action-label">Cancelar</span>
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
    </div>
    @if($agendamentos->hasPages())
    <div class="panel-footer">{{ $agendamentos->links() }}</div>
    @endif
</div>

<div class="modal fade" id="modalRealizar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="formRealizar">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Realizar Serviço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="realizarInfo" class="mb-4"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Forma de Pagamento</label>
                        <select name="forma_pagamento" class="form-control" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Pix">Pix</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Plano">Plano</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Realizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table-wrap { overflow-x: auto; }
.appointments-table { width: 100%; border-collapse: collapse; min-width: 920px; }
.appointments-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-faint);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
}
.appointments-table td {
    padding: 14px 16px;
    font-size: 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.appointments-table tbody tr:hover { background: var(--bg); }
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
.action-btn.warning { color: #d97706; border-color: #d97706; }
.action-btn.warning:hover { background: #fef3c7; }
.action-btn.info { color: var(--info); border-color: var(--info); }
.action-btn.info:hover { background: var(--info-bg); }
.action-btn.delete { color: var(--danger); border-color: var(--danger); }
.action-btn.delete:hover { background: var(--danger-bg); }
form.d-inline { display: inline; }
.value-cell { white-space: nowrap; }
@media (max-width: 768px) {
    .appointments-table thead { display: none; }
    .appointments-table,
    .appointments-table tbody,
    .appointments-table tr,
    .appointments-table td { display: block; width: 100%; }
    .appointments-table tr { padding: 14px 18px; border-bottom: 1px solid var(--border); }
    .appointments-table tbody td { padding: 6px 0; border: none; display: flex; justify-content: space-between; align-items: center; }
    .appointments-table tbody td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; margin-right: 12px; flex-shrink: 0; }
    .actions-cell { justify-content: flex-end; flex-wrap: wrap; gap: 6px; }
    .action-label { display: none; }
}
</style>
@endpush

@push('scripts')
<script>
function abrirModalRealizar(btn, nome, hora) {
    document.getElementById('realizarInfo').textContent = 'Realizar serviço de ' + nome + ' às ' + hora + '?';
    document.getElementById('formRealizar').action = btn.dataset.action;
    new bootstrap.Modal(document.getElementById('modalRealizar')).show();
}
</script>
@endpush
