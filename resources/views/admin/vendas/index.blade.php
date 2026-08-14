@extends('layouts.app')

@section('title', 'Vendas')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-receipt"/></svg>
    <span class="sep">/</span>
    <span>Barber Control</span>
    <span class="sep">/</span>
    <span class="current">Vendas</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>{{ $vendas->total() }} vendas registradas</span>
    <span class="pipe">·</span>
    <span>produtos vendidos na comanda do cliente</span>
@endsection

@section('topbar-actions')
    <div class="search-box">
        <svg class="icon icon-sm"><use href="#i-search"/></svg>
        <input type="text" placeholder="Buscar por cliente…" name="q" form="filterForm" value="{{ request('q') }}">
        <span class="kbd">⌘K</span>
    </div>
    <a href="{{ (request()->route('barbearia') ? route('tenant.admin.vendas.create', request()->route('barbearia')->slug) : route('admin.vendas.create')) }}" class="btn-primary-c">
        <svg class="icon icon-sm"><use href="#i-plus"/></svg>
        Nova Venda
    </a>
@endsection

@section('content')
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-receipt" viewBox="0 0 24 24" fill="none"><path d="M4 5c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v15.5l-2.5-1.5L15 20.5 12.5 19 10 20.5 7.5 19 5 20.5 4 19.5V5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 8h8M8 11.5h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24" fill="none"><circle cx="11.5" cy="11.5" r="8.5" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24" fill="none"><path d="M6 12h12M12 6v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24" fill="none"><path d="M13.26 3.6l-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.98 1.87 1.8l3.22-.55c.45-.08 1.08-.41 1.39-.75L18.86 8.6c.75-.81.8-2.01-.02-2.79l-1.6-1.54c-.83-.79-2.16-.76-2.98.08z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.47 5.08l3.43 3.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-close" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-coins" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18M7 6h1v4M16.71 13.88l.7.71-2.82 2.82" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-cart" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="21" r="1.5" stroke="currentColor" stroke-width="1.6"/><circle cx="20" cy="21" r="1.5" stroke="currentColor" stroke-width="1.6"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    </defs>
</svg>

@php
$slug = request()->route('barbearia')?->slug;
$statusMap = ['pendente' => 'amber', 'finalizada' => 'green', 'cancelada' => 'red'];
$statusLabel = ['pendente' => 'Pendente', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'];
@endphp

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top"><div class="stat-icon amber"><svg class="icon"><use href="#i-cart"/></svg></div></div>
        <div class="stat-label">Vendas registradas</div>
        <div class="stat-value">{{ $qtdVendas }}</div>
        <div class="stat-sub">no período filtrado</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top"><div class="stat-icon green"><svg class="icon"><use href="#i-check"/></svg></div></div>
        <div class="stat-label">Total finalizado</div>
        <div class="stat-value"><small style="font-size:16px;font-weight:600;color:var(--text-muted)">R$</small>{{ number_format($totalFinalizadas, 2, ',', '.') }}</div>
        <div class="stat-sub">vendas concluídas</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top"><div class="stat-icon blue"><svg class="icon"><use href="#i-clock"/></svg></div></div>
        <div class="stat-label">Pendentes (abertas)</div>
        <div class="stat-value"><small style="font-size:16px;font-weight:600;color:var(--text-muted)">R$</small>{{ number_format($totalPendente, 2, ',', '.') }}</div>
        <div class="stat-sub">somam na comanda do cliente</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top"><div class="stat-icon purple"><svg class="icon"><use href="#i-coins"/></svg></div></div>
        <div class="stat-label">Faturamento total</div>
        <div class="stat-value"><small style="font-size:16px;font-weight:600;color:var(--text-muted)">R$</small>{{ number_format($totalPendente + $totalFinalizadas, 2, ',', '.') }}</div>
        <div class="stat-sub">produtos vendidos</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-receipt"/></svg></div>
            <div>
                <h2 class="panel-title">Histórico de vendas</h2>
                <div class="panel-subtitle">Acompanhe e atualize as vendas de produtos</div>
            </div>
        </div>
    </div>

    <form id="filterForm" method="GET" class="toolbar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por cliente…" style="height:36px;padding:0 12px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;min-width:180px;">
        <select name="status" onchange="this.form.submit()" style="height:36px;padding:0 10px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
            <option value="">Todos os status</option>
            @foreach($statusLabel as $key => $label)
            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <input type="date" name="data" value="{{ request('data') }}" onchange="this.form.submit()" style="height:36px;padding:0 10px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
        <div class="toolbar-spacer"></div>
        <div class="result-count"><strong>{{ $vendas->total() }}</strong> venda(s)</div>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Itens</th>
                    <th>Data</th>
                    <th>Pagamento</th>
                    <th style="text-align:right">Total</th>
                    <th>Status</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendas as $venda)
                <tr>
                    <td><span class="badge-c outlined">#{{ $venda->id }}</span></td>
                    <td>
                        <div class="avatar-row">
                            <div class="av {{ $venda->status === 'cancelada' ? 'pink' : 'amber' }}">{{ mb_substr($venda->cliente?->nome ?? 'AV', 0, 2) }}</div>
                            <div class="info">
                                <strong>{{ $venda->cliente?->nome ?? 'Cliente avulso' }}</strong>
                                <span>{{ $venda->agendamento ? 'Venda em atendimento' : 'Venda avulsa' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="avatar-row" style="flex-wrap:wrap;gap:6px;">
                            @foreach($venda->produtos->take(2) as $produto)
                            <span class="badge-c gray">{{ $produto->nome }} <small>×{{ $produto->pivot->quantidade }}</small></span>
                            @endforeach
                            @if($venda->produtos->count() > 2)
                            <span class="badge-c outlined">+{{ $venda->produtos->count() - 2 }}</span>
                            @endif
                        </div>
                    </td>
                    <td><span class="cell-muted">{{ $venda->created_at->format('d/m/Y H:i') }}</span></td>
                    <td><span class="cell-muted">{{ $venda->forma_pagamento ?: '—' }}</span></td>
                    <td style="text-align:right;font-weight:700;">R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                    <td><span class="badge-c {{ $statusMap[$venda->status] }}">{{ $statusLabel[$venda->status] }}</span></td>
                    <td style="text-align:right">
                        <div class="actions-cell">
                            <a href="{{ $slug ? route('tenant.admin.vendas.edit', [$slug, $venda]) : route('admin.vendas.edit', $venda) }}" class="action-btn">
                                <svg class="icon"><use href="#i-edit"/></svg>
                                <span class="action-label">Atualizar</span>
                            </a>
                            <button class="action-btn danger" onclick="confirmarExclusao('{{ $slug ? route('tenant.admin.vendas.destroy', [$slug, $venda]) : route('admin.vendas.destroy', $venda) }}')">
                                <svg class="icon"><use href="#i-close"/></svg>
                                <span class="action-label">Excluir</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="icon-wrap"><svg class="icon"><use href="#i-cart"/></svg></div>
                            <h4>Nenhuma venda encontrada</h4>
                            <p>Registre a primeira venda de produtos da barbearia.</p>
                            <a href="{{ $slug ? route('tenant.admin.vendas.create', $slug) : route('admin.vendas.create') }}" class="btn-primary-c">
                                <svg class="icon icon-sm"><use href="#i-plus"/></svg> Nova Venda
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vendas->hasPages())
    <div class="panel-footer" style="justify-content:center;border-top:1px solid var(--border);">
        {{ $vendas->links() }}
    </div>
    @endif
</section>
@endsection

@push('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
.stat-card { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 22px; position: relative; overflow: hidden; transition: all 220ms; }
.stat-card:hover { border-color: var(--border-strong); transform: translateY(-2px); }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; }
.stat-icon.amber { background: var(--accent-glow); color: var(--accent); }
.stat-icon.green { background: var(--success-bg); color: var(--success); }
.stat-icon.blue { background: var(--info-bg); color: var(--info); }
.stat-icon.purple { background: var(--purple-bg); color: var(--purple); }
.stat-label { font-size: 12.5px; color: var(--text-muted); font-weight: 500; margin-bottom: 6px; }
.stat-value { font-size: 28px; font-weight: 800; letter-spacing: -0.025em; line-height: 1; }
.stat-sub { font-size: 11.5px; color: var(--text-faint); margin-top: 10px; font-weight: 500; }
.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 17px; font-weight: 700; margin: 0; letter-spacing: -0.015em; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }
.toolbar { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: rgba(0,0,0,0.02); }
.toolbar-spacer { flex: 1; }
.result-count { font-size: 13px; color: var(--text-muted); }
.result-count strong { color: var(--text); font-weight: 700; }
.actions-cell { display: flex; gap: 4px; justify-content: flex-end; flex-wrap: wrap; }
.action-btn { height: 34px; padding: 0 10px; border-radius: 9px; border: 1px solid var(--border); background: transparent; color: var(--text-muted); display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 150ms; font-size: 12px; font-weight: 600; font-family: inherit; white-space: nowrap; text-decoration: none; }
.action-btn:hover { color: var(--accent); border-color: var(--accent); background: var(--accent-glow); }
.action-btn.danger:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-bg); }
.action-btn .icon { width: 16px; height: 16px; }
.action-label { font-size: 12px; }
.fade-in { animation: fadeInUp 400ms ease both; }
.d1 { animation-delay: 50ms; } .d2 { animation-delay: 100ms; } .d3 { animation-delay: 150ms; } .d4 { animation-delay: 200ms; } .d5 { animation-delay: 250ms; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: 'Esta venda será removida do histórico.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((r) => {
        if (r.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}

document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-box input').focus();
    }
});
</script>
@endpush