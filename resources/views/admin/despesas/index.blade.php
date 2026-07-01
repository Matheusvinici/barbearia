@extends('layouts.app')

@section('title', 'Financeiro')

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
.action-btn.edit { color: var(--accent); border-color: var(--accent); }
.action-btn.edit:hover { background: var(--accent-glow); }
.action-btn.danger { color: var(--danger); border-color: var(--danger); }
.action-btn.danger:hover { background: var(--danger-bg); }
.action-btn.success { color: var(--success); border-color: var(--success); }
.action-btn.success:hover { background: var(--success-bg); }
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<span class="current">Financeiro</span>
<span class="sep">/</span>
<span>Despesas</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ now()->format('F Y') }}</span>
<span class="pipe">·</span>
<span>{{ $despesas->total() ?? $despesas->count() }} despesas</span>
<span class="pipe">·</span>
<span>Total: R$ {{ number_format($totalMes ?? 0, 2, ',', '.') }}</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon"><use href="#i-menu"/></svg></button>
<div class="period-switch">
    <button class="period-btn active">Mês</button>
    <button class="period-btn">Trimestre</button>
    <button class="period-btn">Ano</button>
</div>
<div class="search-box">
    <svg class="icon icon-sm"><use href="#i-search"/></svg>
    <input type="text" placeholder="Buscar despesa..." id="searchInput" />
</div>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
<button class="icon-btn"><svg class="icon"><use href="#i-bell"/></svg><span class="dot-notif" id="notif-count"></span></button>
<a href="{{ route('admin.despesas.create') }}" class="btn-primary-c"><svg class="icon icon-sm"><use href="#i-plus"/></svg>Nova Despesa</a>
@endsection

@section('content')
<section class="stats-grid">
    <div class="stat-card red fade-in d1">
        <div class="stat-top">
            <div class="stat-icon red"><svg class="icon"><use href="#i-trend-down"/></svg></div>
        </div>
        <div class="stat-label">Despesas (Mês)</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($totalMes ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $qtdMes ?? 0 }} despesas no mês</div>
    </div>
    <div class="stat-card green fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-check"/></svg></div>
        </div>
        <div class="stat-label">Já Pagas</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($totalPago ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $qtdPago ?? 0 }} despesas pagas</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-clock"/></svg></div>
        </div>
        <div class="stat-label">A Pagar</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($totalPendente ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $qtdPendente ?? 0 }} pendentes</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon red"><svg class="icon"><use href="#i-alert"/></svg></div>
        </div>
        <div class="stat-label">Vencidas</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($totalVencido ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $qtdVencido ?? 0 }} vencidas</div>
    </div>
</section>

<div class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-chart"/></svg></div>
            <div>
                <h2 class="panel-title">Despesas por Mês</h2>
                <div class="panel-subtitle">Últimos 6 meses</div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        @php
            $chartData = $chartData ?? [];
            $maxVal = count($chartData) > 0 ? max(array_column($chartData, 'total')) : 1;
        @endphp
        <div class="chart-wrap">
            <svg class="finance-chart" viewBox="0 0 720 300" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="exp-gradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f87171" stop-opacity="0.8"/>
                        <stop offset="100%" stop-color="#ef4444" stop-opacity="0.4"/>
                    </linearGradient>
                </defs>
                <g class="chart-grid">
                    <line x1="40" y1="40" x2="700" y2="40"/>
                    <line x1="40" y1="110" x2="700" y2="110"/>
                    <line x1="40" y1="180" x2="700" y2="180"/>
                    <line x1="40" y1="250" x2="700" y2="250"/>
                </g>
                <g class="chart-axis">
                    <text x="30" y="44" text-anchor="end">R$ {{ number_format($maxVal, 0, ',', '.') }}</text>
                    <text x="30" y="114" text-anchor="end">R$ {{ number_format($maxVal * 0.75, 0, ',', '.') }}</text>
                    <text x="30" y="184" text-anchor="end">R$ {{ number_format($maxVal * 0.5, 0, ',', '.') }}</text>
                    <text x="30" y="254" text-anchor="end">R$ {{ number_format($maxVal * 0.25, 0, ',', '.') }}</text>
                    @foreach($chartData as $i => $d)
                    <text x="{{ 90 + $i * 112 }}" y="280" text-anchor="middle">{{ $d['label'] }}</text>
                    @endforeach
                </g>
                <g class="chart-bars">
                    @foreach($chartData as $i => $d)
                    @php
                        $barH = $maxVal > 0 ? max(2, ($d['total'] / $maxVal) * 210) : 2;
                        $barX = 40 + $i * (660 / max(count($chartData), 1)) + ((660 / max(count($chartData), 1)) - 40) / 2;
                    @endphp
                    <rect class="chart-bar-exp" x="{{ $barX }}" y="{{ 40 + 210 - $barH }}" width="40" height="{{ $barH }}" rx="5" fill="url(#exp-gradient)"/>
                    @endforeach
                </g>
            </svg>
        </div>
    </div>
</div>

<div class="panel fade-in d6">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-receipt"/></svg></div>
            <div>
                <h2 class="panel-title">Despesas</h2>
                <div class="panel-subtitle">Todas as despesas registradas</div>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <button class="filter-chip active" data-filter="all">
            <svg class="icon icon-sm"><use href="#i-receipt"/></svg>
            Todas
            <span class="count">{{ $despesas->total() ?? $despesas->count() }}</span>
        </button>
        <button class="filter-chip" data-filter="paid">
            <svg class="icon icon-sm"><use href="#i-check"/></svg>
            Pagas
            <span class="count">{{ $qtdPago ?? 0 }}</span>
        </button>
        <button class="filter-chip" data-filter="pending">
            <svg class="icon icon-sm"><use href="#i-clock"/></svg>
            Pendentes
            <span class="count">{{ $qtdPendente ?? 0 }}</span>
        </button>
        <div class="toolbar-spacer"></div>
        <div class="result-count"><strong id="resultCount">{{ $despesas->count() }}</strong> despesas</div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Forma Pagamento</th>
                    <th>Status</th>
                    <th class="right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($despesas as $d)
                <tr>
                    <td>
                        <div class="desc-cell">
                            <div class="tx-icon out"><svg class="icon icon-sm"><use href="#i-trend-down"/></svg></div>
                            <div>
                                <div class="tx-name">{{ $d->descricao }}</div>
                                @if($d->observacoes)
                                <div class="tx-meta">{{ Str::limit($d->observacoes, 40) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-c gray">{{ $d->categoria }}</span></td>
                    <td class="amount-cell out">R$ {{ number_format($d->valor, 2, ',', '.') }}</td>
                    <td>{{ $d->data_vencimento->format('d/m/Y') }}</td>
                    <td>{{ $d->forma_pagamento ?? '-' }}</td>
                    <td>
                        <span class="badge-c {{ $d->pago ? 'green' : 'amber' }}">
                            {{ $d->pago ? 'Pago' : 'Pendente' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <button onclick="togglePago({{ $d->id }})" class="action-btn {{ $d->pago ? 'danger' : 'success' }}" style="font-size:12px;">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $d->pago ? 'M5 12l5 5L20 7' : 'M5 12l5 5L20 7' }}"/></svg>
                                {{ $d->pago ? 'Reverter' : 'Pagar' }}
                            </button>
                            <a href="{{ route('admin.despesas.edit', $d) }}" class="action-btn edit" title="Editar">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar
                            </a>
                            <button onclick="confirmarExclusao('{{ route('admin.despesas.destroy', $d) }}')" class="action-btn danger" title="Excluir">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13c0 1 .5 2 2 2h8c1.5 0 2-1 2-2l1-13M9 7V4c0-1 .5-1 1-1h4c.5 0 1 0 1 1v3"/></svg>Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:40px; color:var(--text-muted);">
                        <svg class="icon" style="width:40px;height:40px; margin-bottom:12px; opacity:0.4;"><use href="#i-receipt"/></svg>
                        <div style="font-size:16px; font-weight:600;">Nenhuma despesa encontrada</div>
                        <div style="font-size:13px; margin-top:4px;">Cadastre sua primeira despesa para começar</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($despesas, 'hasPages') && $despesas->hasPages())
    <div class="panel-footer">
        <div class="pagination-info">Mostrando {{ $despesas->firstItem() }} – {{ $despesas->lastItem() }} de {{ $despesas->total() }} despesas</div>
        <div class="pagination-c">
            {{ $despesas->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function togglePago(id) {
    $.ajax({
        url: '{{ route("admin.despesas.toggle-pago", "") }}/' + id,
        method: 'PATCH',
        data: { _token: '{{ csrf_token() }}' },
        success: () => location.reload()
    });
}
function confirmarExclusao(url) {
    Swal.fire({ title: 'Excluir despesa?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Excluir' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
</script>
@endpush
