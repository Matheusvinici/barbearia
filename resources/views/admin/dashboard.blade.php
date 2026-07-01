@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-home"/></svg>
    <span class="sep">/</span>
    <span class="current">Dashboard</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Sistema online</span>
    <span class="pipe">·</span>
    <span>{{ now()->format('l, d \d\e F \d\e Y') }}</span>
    <span class="pipe">·</span>
    <span>{{ $agendamentosHoje->count() }} agendamentos hoje</span>
@endsection

@section('topbar-actions')
    <div class="period-switch">
        <button class="period-btn">Hoje</button>
        <button class="period-btn active">Semana</button>
        <button class="period-btn">Mês</button>
        <button class="period-btn">Ano</button>
    </div>
    <button class="icon-btn" title="Notificações">
        <svg class="icon"><use href="#i-bell"/></svg>
        <span class="dot-notif" id="notif-count"></span>
    </button>
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.agendamentos.index', optional(request()->route('barbearia'))?->slug) : route('admin.agendamentos.index') }}" class="btn-primary-c">
        <svg class="icon icon-sm"><use href="#i-plus"/></svg>
        Novo Agendamento
    </a>
@endsection

@section('content')
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-home" viewBox="0 0 24 24" fill="none"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17.5v-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5h.01M7.5 13.5h4.49" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-people" viewBox="0 0 24 24" fill="none"><path d="M16.67 20.17v-1.5c0-2.07-1.68-3.75-3.75-3.75H5.42c-2.07 0-3.75 1.68-3.75 3.75v1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.17" cy="7.42" r="3.75" stroke="currentColor" stroke-width="1.6"/><path d="M22 20.17v-1.5c0-1.69-1.13-3.12-2.67-3.58M15.42 4.05a3.74 3.74 0 0 1 0 6.74" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-scissor" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-user-tag" viewBox="0 0 24 24" fill="none"><path d="M13 20.5H6.5c-1.5 0-2.5-1-2.5-2.5 0-3.5 3-5.5 6-5.5.83 0 1.63.13 2.36.37" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="6.5" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M17.13 17.92l2.32 2.32c.21.21.55.21.76 0l1.55-1.55c.21-.21.21-.55 0-.76l-2.32-2.32a.54.54 0 0 1-.16-.38v-2.18c0-.29-.24-.53-.53-.53h-2.18a.54.54 0 0 1-.38-.16L14 9.95c-.18-.18-.49-.18-.67 0l-1.55 1.55c-.18.18-.18.49 0 .67l1.65 1.65c.1.1.16.24.16.38v2.18c0 .29.24.53.53.53h2.18c.14 0 .28.06.38.16z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-wallet" viewBox="0 0 24 24" fill="none"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-chart" viewBox="0 0 24 24" fill="none"><path d="M3 22h18M5.6 18V9M10.6 18V5M15.6 18v-7M20.6 18V8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-settings" viewBox="0 0 24 24" fill="none"><path d="M3 8.5L4.5 6.5L7 7L8.13 4.84L10.5 5.5L12 4L13.5 5.5L15.87 4.84L17 7L19.5 6.5L21 8.5L20 10.5L21 12.5L19.5 14.5L17 14L15.87 16.16L13.5 15.5L12 17L10.5 15.5L8.13 16.16L7 14L4.5 14.5L3 12.5L4 10.5L3 8.5Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="10.5" r="2.5" stroke="currentColor" stroke-width="1.6"/></symbol>
        <symbol id="i-lifebuoy" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M5 5l4 4M15 15l4 4M19 5l-4 4M9 15l-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24" fill="none"><circle cx="11.5" cy="11.5" r="8.5" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-bell" viewBox="0 0 24 24" fill="none"><path d="M12 6.44v6.72M9 16.5h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8.5 2.5H15.5c.55 0 1 .45 1 1v.74c0 .3.13.59.36.78.83.69 1.27 1.91 1.27 3.45v5.95c0 1.84-1.27 3.43-3.07 3.83-.97.22-1.96.36-2.95.41-.51.03-1.02.04-1.53.04s-1.02-.01-1.53-.04c-.99-.05-1.98-.19-2.95-.41-1.8-.4-3.07-1.99-3.07-3.83V8.47c0-1.54.44-2.76 1.27-3.45.23-.19.36-.48.36-.78V3.5c0-.55.45-1 1-1z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
        <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-moon" viewBox="0 0 24 24" fill="none"><path d="M3.27 12.31c.43 4.6 4.34 8.21 8.95 8.41 3.16.13 5.97-1.18 7.86-3.34.62-.71.27-1.32-.69-1.21-.55.06-1.11.04-1.69-.06-3.58-.6-6.32-3.45-6.65-7.06-.12-1.34.07-2.62.5-3.79.34-.92-.31-1.39-1.22-1.04-4.21 1.61-7.04 5.71-6.69 10.09z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24" fill="none"><path d="M6 12h12M12 6v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M19 12l-7 7-7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 18l6-6-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-menu" viewBox="0 0 24 24" fill="none"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24" fill="none"><path d="M15 18.5H7.5c-1.5 0-2.5-1-2.5-2.5v-8c0-1.5 1-2.5 2.5-2.5H15M11 12h10M17.5 8.5L21 12l-3.5 3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-calendar-tick" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5l1.5 1.5 2.5-2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-user-plus" viewBox="0 0 24 24" fill="none"><path d="M14 7.5c0 2.07-1.68 3.75-3.75 3.75S6.5 9.57 6.5 7.5 8.18 3.75 10.25 3.75 14 5.43 14 7.5z" stroke="currentColor" stroke-width="1.6"/><path d="M2.5 20c0-3.5 3-5.5 6-5.5M19 8v6M22 11h-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-receipt" viewBox="0 0 24 24" fill="none"><path d="M4 5c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v15.5l-2.5-1.5L15 20.5 12.5 19 10 20.5 7.5 19 5 20.5 4 19.5V5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 8h8M8 11.5h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-trend-up" viewBox="0 0 24 24" fill="none"><path d="M3 17l6-6 4 4 8-8M14 7h7v7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-medal" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="9" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M8.5 14l-1.5 7 5-3 5 3-1.5-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 6.5l1 2 2.2.2-1.6 1.5.5 2.1L12 11.3l-2.1 1 .5-2.1L8.8 8.7l2.2-.2 1-2z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></symbol>
        <symbol id="i-call" viewBox="0 0 24 24" fill="none"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-scissors-2" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-activity" viewBox="0 0 24 24" fill="none"><path d="M2 12h4l3-9 6 18 3-9h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-coffee" viewBox="0 0 24 24" fill="none"><path d="M5 8h13v5c0 3.31-2.91 6-6.5 6S5 16.31 5 13V8z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M18 9h2.5a2.5 2.5 0 0 1 0 5H18M8 2v2M11 2v2M14 2v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-cake" viewBox="0 0 24 24" fill="none"><path d="M3 18.5c0-1.5 1-2.5 2.5-2.5H18c1.5 0 3 1 3 2.5V21H3v-2.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M5 16c1.5 0 1.5-1.5 3-1.5s1.5 1.5 3 1.5 1.5-1.5 3-1.5 1.5 1.5 3 1.5M12 8V4M12 4l-1.5-1.5M12 4l1.5-1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-info" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-dollar" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 6v12M8 9h5.5a2.5 2.5 0 0 1 0 5H8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <linearGradient id="bar-gradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f5b544" stop-opacity="0.9"/>
            <stop offset="100%" stop-color="#e89538" stop-opacity="0.55"/>
        </linearGradient>
        <linearGradient id="area-gradient" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#f5b544" stop-opacity="0.35"/>
            <stop offset="100%" stop-color="#f5b544" stop-opacity="0"/>
        </linearGradient>
    </defs>
</svg>

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-calendar-tick"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>{{ $agendamentosHoje->count() > 0 ? round(($confirmados + $realizados) / max($agendamentosHoje->count(), 1) * 100) : 0 }}%</span>
        </div>
        <div class="stat-label">Agendamentos Hoje</div>
        <div class="stat-value">{{ $agendamentosHoje->count() }}</div>
        <div class="stat-sub">{{ $confirmados }} confirmados · {{ $pendentes }} pendentes</div>
        <svg class="stat-spark" width="80" height="32" viewBox="0 0 80 32"><polyline points="0,20 12,22 24,16 36,18 48,12 60,14 72,8 80,6" fill="none" stroke="#f5b544" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-wallet"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>{{ $totalFaturamentoHoje > 0 ? 'Ativo' : '—' }}</span>
        </div>
        <div class="stat-label">Receita Hoje</div>
        <div class="stat-value"><small style="font-size:16px;font-weight:600;color:var(--text-muted)">R$</small>{{ number_format($totalFaturamentoHoje, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $realizados }} serviços realizados</div>
        <svg class="stat-spark" width="80" height="32" viewBox="0 0 80 32"><polyline points="0,24 12,20 24,22 36,14 48,16 60,8 72,10 80,4" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-clock"/></svg></div>
            <span class="stat-delta {{ $pendentes > 0 ? 'up' : 'down' }}"><svg class="icon icon-xs"><use href="#i-arrow-{{ $pendentes > 0 ? 'up' : 'down' }}"/></svg>{{ $pendentes }}</span>
        </div>
        <div class="stat-label">Pendentes</div>
        <div class="stat-value">{{ $pendentes }}</div>
        <div class="stat-sub">{{ $realizados }} realizados · {{ $agendamentosSemana }} nos próximos 7 dias</div>
        <svg class="stat-spark" width="80" height="32" viewBox="0 0 80 32"><polyline points="0,28 12,26 24,24 36,20 48,16 60,12 72,8 80,4" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-receipt"/></svg></div>
            <span class="stat-delta down"><svg class="icon icon-xs"><use href="#i-arrow-down"/></svg>Vencidas</span>
        </div>
        <div class="stat-label">Despesas Vencidas</div>
        <div class="stat-value"><small style="font-size:16px;font-weight:600;color:var(--text-muted)">R$</small>{{ number_format($despesasVencidas, 2, ',', '.') }}</div>
        <div class="stat-sub">R$ {{ number_format($despesasPendentes, 2, ',', '.') }} em aberto</div>
        <svg class="stat-spark" width="80" height="32" viewBox="0 0 80 32"><polyline points="0,12 12,8 24,14 36,10 48,18 60,16 72,22 80,20" fill="none" stroke="#c084fc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </div>
</section>

<section class="main-grid">
    <div class="col-stack">
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-trend-up"/></svg></div>
                    <div>
                        <h2 class="panel-title">Agendamentos de Hoje</h2>
                        <div class="panel-subtitle">{{ now()->format('d/m/Y') }} · {{ $agendamentosHoje->count() }} agendamentos</div>
                    </div>
                </div>
                <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.agendamentos.index', optional(request()->route('barbearia'))?->slug) : route('admin.agendamentos.index') }}" class="link-action">Ver todos <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg></a>
            </div>
            <div class="upcoming-list">
                @forelse($agendamentosHoje as $ag)
                @php
                    $temPlanoDash = $ag->cliente && ($ag->cliente->relationLoaded('planos')
                        ? $ag->cliente->planos->where('ativo', true)->isNotEmpty()
                        : ($ag->cliente->planoAtivo ? true : false));
                    $pagamentoDash = $ag->forma_pagamento ?? '';
                @endphp
                <div class="upcoming-item">
                    <div class="up-time">
                        <div class="h">{{ $ag->hora_inicio->format('H:i') }}</div>
                        <div class="d">{{ $ag->hora_fim ? $ag->hora_inicio->diffInMinutes($ag->hora_fim) . 'min' : '—' }}</div>
                    </div>
                    <div class="up-divider"></div>
                    <div class="up-avatar av-{{ ['amber','blue','green','purple','pink','red'][$loop->index % 6] }}">{{ mb_substr($ag->cliente->nome ?? '?', 0, 2, 'UTF-8') }}</div>
                    <div class="up-info">
                        <div class="n">{{ $ag->cliente->nome ?? 'Cliente removido' }}
                            @if($temPlanoDash)<span class="badge-c gold" style="font-size:9px;padding:0 5px;margin-left:3px;vertical-align:middle;">Plano</span>@endif
                        </div>
                        <div class="m">{{ $ag->servicos->pluck('nome')->implode(', ') }}<span class="pipe">·</span>{{ $ag->barbeiro->nome ?? '—' }}
                            @if($pagamentoDash)<span class="pipe">·</span>{{ $pagamentoDash }}@endif
                        </div>
                    </div>
                    <span class="up-status {{ $ag->status }}">{{ ucfirst($ag->status) }}</span>
                </div>
                @empty
                <div class="upcoming-item"><div class="up-info" style="text-align:center;width:100%;color:var(--text-muted);padding:20px 0;">Nenhum agendamento para hoje</div></div>
                @endforelse
            </div>
        </div>

        <div class="panel fade-in d6">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-wallet"/></svg></div>
                    <div>
                        <h2 class="panel-title">Resumo do Dia</h2>
                        <div class="panel-subtitle">Status dos agendamentos e caixa</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="revenue-stats">
                    <span class="revenue-total"><span class="revenue-currency">R$</span>{{ number_format($totalFaturamentoHoje, 2, ',', '.') }}</span>
                    <span class="revenue-delta"><svg class="icon icon-xs"><use href="#i-check"/></svg> {{ $realizados }} realizados</span>
                </div>
                <div class="revenue-period-label" style="margin-bottom:16px;">Faturamento total de hoje</div>
                <div class="donut-legend" style="width:100%;">
                    <div class="donut-row">
                        <span class="swatch" style="background:var(--info)"></span>
                        <span class="name">Confirmados</span>
                        <span class="pct">{{ $confirmados }}</span>
                    </div>
                    <div class="donut-row">
                        <span class="swatch" style="background:var(--success)"></span>
                        <span class="name">Realizados</span>
                        <span class="pct">{{ $realizados }}</span>
                    </div>
                    <div class="donut-row">
                        <span class="swatch" style="background:var(--warning)"></span>
                        <span class="name">Pendentes</span>
                        <span class="pct">{{ $pendentes }}</span>
                    </div>
                    <div class="donut-row">
                        <span class="swatch" style="background:var(--text-faint)"></span>
                        <span class="name">Próximos 7 dias</span>
                        <span class="pct">{{ $agendamentosSemana }}</span>
                    </div>
                    <div class="donut-row">
                        <span class="swatch" style="background:var(--accent)"></span>
                        <span class="name">Caixa</span>
                        <span class="pct">
                            @if($caixaHoje)
                                {{ $caixaHoje->fechado ? 'Fechado' : 'Aberto' }}
                            @else
                                <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.caixa.index', optional(request()->route('barbearia'))?->slug) : route('admin.caixa.index') }}" style="color:var(--accent);text-decoration:none;">Abrir Caixa</a>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-stack">
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-dollar"/></svg></div>
                    <div>
                        <h2 class="panel-title">Despesas</h2>
                        <div class="panel-subtitle">Vencidas e em aberto</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="donut-wrap">
                    <div style="flex:1;display:flex;flex-direction:column;gap:10px;width:100%;">
                        <div class="donut-row">
                            <span class="swatch" style="background:var(--danger)"></span>
                            <span class="name">Vencidas</span>
                            <span class="pct">R$ {{ number_format($despesasVencidas, 2, ',', '.') }}</span>
                        </div>
                        <div class="donut-row">
                            <span class="swatch" style="background:var(--warning)"></span>
                            <span class="name">Em aberto (futuras)</span>
                            <span class="pct">R$ {{ number_format($despesasPendentes, 2, ',', '.') }}</span>
                        </div>
                        <div class="donut-row" style="border-bottom:none;">
                            <span class="swatch" style="background:var(--accent)"></span>
                            <span class="name">Total pendente</span>
                            <span class="pct" style="font-weight:800;">R$ {{ number_format($despesasVencidas + $despesasPendentes, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel fade-in d6">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-activity"/></svg></div>
                    <div>
                        <h2 class="panel-title">Ações Rápidas</h2>
                        <div class="panel-subtitle">Atalhos do sistema</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="ranking-list">
                    @php
                        $quickActions = [
                            ['route' => (optional(request()->route('barbearia'))?->slug ? route('tenant.admin.agendamentos.index', optional(request()->route('barbearia'))?->slug) : route('admin.agendamentos.index')), 'label' => 'Novo Agendamento', 'icon' => 'i-plus', 'color' => 'var(--accent)'],
                            ['route' => (optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.index', optional(request()->route('barbearia'))?->slug) : route('admin.relatorios.index')), 'label' => 'Relatório de Faturamento', 'icon' => 'i-chart', 'color' => 'var(--info)'],
                            ['route' => (optional(request()->route('barbearia'))?->slug ? route('tenant.admin.caixa.index', optional(request()->route('barbearia'))?->slug) : route('admin.caixa.index')), 'label' => 'Gerenciar Caixa', 'icon' => 'i-wallet', 'color' => 'var(--success)'],
                            ['route' => (optional(request()->route('barbearia'))?->slug ? route('tenant.admin.clientes.index', optional(request()->route('barbearia'))?->slug) : route('admin.clientes.index')), 'label' => 'Clientes', 'icon' => 'i-people', 'color' => 'var(--purple)'],
                        ];
                    @endphp
                    @foreach($quickActions as $action)
                    <a href="{{ $action['route'] }}" style="text-decoration:none;color:inherit;">
                        <div class="rank-row">
                            <div class="rank-pos gold"><svg class="icon icon-sm"><use href="#{{ $action['icon'] }}"/></svg></div>
                            <div class="rank-info">
                                <div class="name">{{ $action['label'] }}</div>
                            </div>
                            <div class="rank-val">
                                <svg class="icon icon-sm" style="color:var(--text-faint)"><use href="#i-arrow-right"/></svg>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
