@extends('layouts.app')

@php
    function _caixaRoute($name, $params = []) {
        $slug = request()->route('barbearia')?->slug;
        if (!$slug) return route('admin.' . $name, $params);
        $params = is_array($params) ? $params : [$params];
        return route('tenant.admin.' . $name, array_merge([$slug], $params));
    }
@endphp

@section('title', 'Caixa - '.$caixa->data->format('d/m/Y'))

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ _caixaRoute('caixa.index') }}" style="color:inherit;text-decoration:none;">Caixa</a>
<span class="sep">/</span>
<span class="current">{{ $caixa->data->format('d/m/Y') }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Detalhes do caixa</span>
<span class="pipe">·</span>
<span>{{ $caixa->barbearia?->nome ?? 'Sem unidade' }}</span>
<span class="pipe">·</span>
<span>{{ $caixa->fechado ? 'Fechado' : 'Aberto' }}</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ _caixaRoute('caixa.edit', $caixa) }}" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
<a href="{{ _caixaRoute('caixa.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">

        {{-- Summary Stats --}}
        <section class="stats-grid">
            <div class="stat-card fade-in d1">
                <div class="stat-top">
                    <div class="stat-icon blue"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 4 4-6"/></svg></div>
                </div>
                <div class="stat-label">Saldo Inicial</div>
                <div class="stat-value">R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</div>
                <div class="stat-sub">{{ $caixa->usuarioAbertura?->name ?? '-' }}</div>
            </div>
            <div class="stat-card fade-in d2">
                <div class="stat-top">
                    <div class="stat-icon green"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
                </div>
                <div class="stat-label">Total Entradas</div>
                <div class="stat-value" style="color:var(--success);">R$ {{ number_format($caixa->total_entradas, 2, ',', '.') }}</div>
                <div class="stat-sub">receitas do dia</div>
            </div>
            <div class="stat-card fade-in d3">
                <div class="stat-top">
                    <div class="stat-icon red"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg></div>
                </div>
                <div class="stat-label">Total Saídas</div>
                <div class="stat-value" style="color:var(--danger);">R$ {{ number_format($caixa->total_saidas, 2, ',', '.') }}</div>
                <div class="stat-sub">despesas do dia</div>
            </div>
            <div class="stat-card fade-in d4">
                <div class="stat-top">
                    <div class="stat-icon {{ $caixa->saldo_final >= 0 ? 'green' : 'red' }}"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z"/></svg></div>
                    <span class="stat-delta {{ $caixa->saldo_final >= 0 ? 'up' : 'down' }}">
                        <svg class="icon icon-xs" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $caixa->saldo_final >= 0 ? 'M12 19V5M5 12l7-7 7 7' : 'M12 5v14M19 12l-7 7-7-7' }}"/></svg>
                        {{ $caixa->saldo_final >= 0 ? 'Positivo' : 'Negativo' }}
                    </span>
                </div>
                <div class="stat-label">Saldo Final</div>
                <div class="stat-value">R$ {{ number_format($caixa->saldo_final, 2, ',', '.') }}</div>
                <div class="stat-sub">{{ $caixa->fechado ? 'Fechado' : 'Em aberto' }}</div>
            </div>
        </section>

        {{-- Movements --}}
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 11v2h-8M18 9l4 4-4 4"/></svg></div>
                    <div>
                        <h2 class="panel-title">Movimentações</h2>
                        <div class="panel-subtitle">Lançamentos do dia</div>
                    </div>
                </div>
            </div>
            <div class="panel-body" style="padding:0;">
                <table class="data-table">
                    <thead>
                        <tr><th>Tipo</th><th>Descrição</th><th>Valor</th></tr>
                    </thead>
                    <tbody>
                        @forelse($caixa->movimentacoes as $m)
                        <tr>
                            <td>
                                @if($m->tipo == 'entrada')
                                <span class="badge-c badge-success">Entrada</span>
                                @else
                                <span class="badge-c badge-danger">Saída</span>
                                @endif
                            </td>
                            <td>{{ $m->descricao }}</td>
                            <td style="color:{{ $m->tipo == 'entrada' ? 'var(--success)' : 'var(--danger)' }};font-weight:600;">
                                R$ {{ number_format($m->valor, 2, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;padding:32px;color:var(--text-muted);">Nenhuma movimentação registrada</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
