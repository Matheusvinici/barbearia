@extends('layouts.app')

@section('title', 'Serviços')

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
.action-btn.edit {
    color: var(--accent);
    border-color: var(--accent);
}
.action-btn.edit:hover {
    background: var(--accent-glow);
}
.action-btn.danger {
    color: var(--danger);
    border-color: var(--danger);
}
.action-btn.danger:hover {
    background: var(--danger-bg);
}
.avatar-row-img {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<span class="current">Serviços</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $servicos->total() }} serviços cadastrados</span>
<span class="pipe">·</span>
<span>{{ $servicos->where('ativo', true)->count() }} ativos</span>
<span class="pipe">·</span>
<span>Preço médio: R$ {{ number_format($servicos->avg('preco'), 2, ',', '.') }}</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<div class="search-box">
    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11.5" cy="11.5" r="8.5"/><path d="M21 21l-4.35-4.35" stroke-linecap="round"/></svg>
    <input type="text" placeholder="Buscar serviço…" id="tableSearch">
    <span class="kbd">/</span>
</div>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-linecap="round"/></svg></button>
<button class="icon-btn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 6.44v6.72M9 16.5h6"/><path d="M8.5 2.5H15.5c.55 0 1 .45 1 1v.74c0 .3.13.59.36.78.83.69 1.27 1.91 1.27 3.45v5.95c0 1.84-1.27 3.43-3.07 3.83-.97.22-1.96.36-2.95.41-.51.03-1.02.04-1.53.04s-1.02-.01-1.53-.04c-.99-.05-1.98-.19-2.95-.41-1.8-.4-3.07-1.99-3.07-3.83V8.47c0-1.54.44-2.76 1.27-3.45.23-.19.36-.48.36-.78V3.5c0-.55.45-1 1-1z" stroke-linejoin="round"/></svg><span class="dot-notif"></span></button>
<a href="{{ route('admin.servicos.create') }}" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 12h12M12 6v12"/></svg>Novo Serviço</a>
@endsection

@section('content')
@php
    $totalServicos = $servicos->total();
    $ativos = $servicos->where('ativo', true)->count();
    $inativos = $servicos->where('ativo', false)->count();
    $precoMedio = $servicos->avg('preco');
    $duracaoMedia = $servicos->avg('duracao_minutos');
@endphp

{{-- Stats --}}
<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>{{ $ativos }} ativos</span>
        </div>
        <div class="stat-label">Serviços ativos</div>
        <div class="stat-value">{{ $ativos }}</div>
        <div class="stat-sub">{{ $inativos }} inativos</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z"/></svg></div>
        </div>
        <div class="stat-label">Preço médio</div>
        <div class="stat-value">R$ {{ number_format($precoMedio, 2, ',', '.') }}</div>
        <div class="stat-sub">por serviço</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 1.5"/></svg></div>
        </div>
        <div class="stat-label">Duração média</div>
        <div class="stat-value">{{ round($duracaoMedia) }} min</div>
        <div class="stat-sub">tempo estimado</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6M15 8l2 2 4-4"/></svg></div>
            <span class="stat-delta down"><svg class="icon icon-xs" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 12l-7 7-7-7"/></svg>{{ $inativos }} inativos</span>
        </div>
        <div class="stat-label">Serviços inativos</div>
        <div class="stat-value">{{ $inativos }}</div>
        <div class="stat-sub">de {{ $totalServicos }} total</div>
    </div>
</section>

{{-- Panel with table --}}
<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"/></svg></div>
            <div>
                <h2 class="panel-title">Catálogo de serviços</h2>
                <div class="panel-subtitle">Gerencie preços, durações e disponibilidade</div>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <button class="filter-chip active" data-filter="all">
            <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
            Todos
            <span class="count">{{ $totalServicos }}</span>
        </button>
        <button class="filter-chip" data-filter="corte">
            <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6"/></svg>
            Cortes
        </button>
        <button class="filter-chip" data-filter="barba">
            <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10l-1 4H8L7 4z"/><path d="M8 8l1.5 12h5L16 8"/><path d="M10 12h4"/></svg>
            Barba
        </button>
        <button class="filter-chip" data-filter="combo">
            <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h7l-1 8 9-12h-7l1-8z"/></svg>
            Combos
        </button>
        <button class="filter-chip" data-filter="tratamento">
            <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M12 3l1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z"/></svg>
            Tratamentos
        </button>
        <div class="toolbar-spacer"></div>
        <div class="result-count"><strong id="resultCount">{{ $servicos->count() }}</strong> de <strong>{{ $totalServicos }}</strong> serviços</div>
    </div>

    <div class="panel-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Duração</th>
                    <th>Preço</th>
                    <th>Comissão Barbeiro</th>
                    <th>Status</th>
                    <th style="width:100px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($servicos as $s)
                <tr>
                    <td>
                        <div class="avatar-row">
                            @if($s->foto)
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nome }}" class="avatar-row-img">
                            @else
                            <div class="avatar-row-placeholder" style="background:var(--accent-glow);color:var(--accent);">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6"/></svg>
                            </div>
                            @endif
                            <div>
                                <div class="avatar-row-name">{{ $s->nome }}</div>
                                @if($s->descricao)
                                <div class="avatar-row-sub">{{ Str::limit($s->descricao, 40) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="svc-meta-item"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 1.5"/></svg> {{ $s->duracao_minutos }}min</span></td>
                    <td><span class="svc-price" style="font-size:16px;"><span class="cur">R$</span>{{ number_format($s->preco, 2, ',', '.') }}</span></td>
                    <td><span class="badge-c badge-amber">{{ $s->comissao_percentual ?? 50 }}%</span></td>
                    <td>
                        @if($s->ativo)
                        <span class="badge-c badge-success">Ativo</span>
                        @else
                        <span class="badge-c badge-danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <div class="svc-actions" style="display:flex;gap:4px;">
                            <a href="{{ route('admin.servicos.edit', $s) }}" class="action-btn edit" title="Editar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
                            <button class="action-btn danger" title="Excluir" onclick="confirmarExclusao('{{ route('admin.servicos.destroy', $s) }}')"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13c0 1 .5 2 2 2h8c1.5 0 2-1 2-2l1-13M9 7V4c0-1 .5-1 1-1h4c.5 0 1 0 1 1v3"/></svg>Excluir</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <svg class="icon" style="width:40px;height:40px;margin-bottom:12px;opacity:0.3;" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6"/></svg>
                        <div style="font-size:15px;font-weight:600;margin-bottom:4px;">Nenhum serviço encontrado</div>
                        <div style="font-size:13px;">Clique em "Novo Serviço" para começar.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($servicos->hasPages())
    <div class="panel-footer" style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:center;">
        {{ $servicos->links() }}
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({ title: 'Confirmar exclusão?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sim, excluir!' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}

$(document).ready(function() {
    $('#tableSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.data-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        $('#resultCount').text($('.data-table tbody tr:visible').length);
    });
});
</script>
@endpush
