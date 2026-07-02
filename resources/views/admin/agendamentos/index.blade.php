@extends('layouts.app')

@section('title', 'Agendamentos')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-home"/></svg>
    <span class="sep">/</span>
    <span>Barber Control</span>
    <span class="sep">/</span>
    <span class="current">Agendamentos</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Ao vivo</span>
    <span class="pipe">·</span>
    <span>{{ \Carbon\Carbon::parse($data)->translatedFormat('l, j \d\e F \d\e Y') }}</span>
    <span class="pipe">·</span>
    <span>{{ $agendamentos->count() }} agendamentos hoje</span>
@endsection

@section('topbar-actions')
    <div class="search-box">
        <svg class="icon icon-sm"><use href="#i-search"/></svg>
        <input type="text" placeholder="Buscar cliente, serviço…" id="searchInput">
        <span class="kbd">⌘K</span>
    </div>
    <button class="icon-btn" id="themeToggle" title="Alternar tema">
        <svg class="icon"><use href="#i-sun"/></svg>
    </button>
    <button class="icon-btn" id="notifBtn" title="Notificações">
        <svg class="icon"><use href="#i-bell"/></svg>
        <span class="dot-notif" id="notif-count"></span>
    </button>
    <button class="btn-primary-c" data-bs-toggle="modal" data-bs-target="#modalNovoAgendamento">
        <svg class="icon icon-sm"><use href="#i-plus"/></svg>
        Novo Agendamento
    </button>
@endsection

@php
use Carbon\Carbon;

$totalHoje = $agendamentos->count();
$pendentes = $agendamentos->where('status', 'pendente')->count();
$confirmados = $agendamentos->where('status', 'confirmado')->count();
$realizados = $agendamentos->where('status', 'realizado')->count();
$cancelados = $agendamentos->where('status', 'cancelado')->count();
$faturamento = $agendamentos->whereIn('status', ['confirmado', 'realizado'])->sum('total');
$avatarClasses = ['av-amber', 'av-blue', 'av-green', 'av-purple', 'av-pink', 'av-red'];
$barberColors = ['#f5b544', '#60a5fa', '#4ade80', '#c084fc', '#f472b6', '#f87171'];
$statusMap = [
    'pendente' => ['label' => 'Pendente', 'cls' => 'status-pending'],
    'confirmado' => ['label' => 'Confirmado', 'cls' => 'status-confirmed'],
    'realizado' => ['label' => 'Realizado', 'cls' => 'status-completed'],
    'cancelado' => ['label' => 'Cancelado', 'cls' => 'status-canceled'],
    'ausente' => ['label' => 'Ausente', 'cls' => 'status-canceled'],
];

function getInitials($name) {
    $parts = explode(' ', trim($name));
    $i = mb_substr($parts[0], 0, 1);
    if (isset($parts[1])) $i .= mb_substr($parts[1], 0, 1);
    return strtoupper($i);
}
@endphp

@push('styles')
<style>
.icon { width: 22px; height: 22px; display: inline-flex; flex-shrink: 0; }
.icon-sm { width: 18px; height: 18px; }
.icon-xs { width: 15px; height: 15px; }

.search-box { position: relative; width: 280px; }
.search-box .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-faint); pointer-events: none; }
.search-box input { width: 100%; height: 44px; padding: 0 14px 0 42px; border-radius: 12px; border: 1px solid var(--border-strong); background: var(--card-solid); color: var(--text); font-family: inherit; font-size: 14px; transition: all 180ms; }
.search-box input::placeholder { color: var(--text-faint); }
.search-box input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }
.search-box .kbd { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 10.5px; font-weight: 700; color: var(--text-faint); border: 1px solid var(--border-strong); background: var(--bg); padding: 2px 6px; border-radius: 5px; }

.icon-btn { width: 44px; height: 44px; border-radius: 12px; border: 1px solid var(--border-strong); background: var(--card-solid); color: var(--text-muted); display: grid; place-items: center; cursor: pointer; transition: all 180ms; position: relative; }
.icon-btn:hover { color: var(--text); border-color: var(--accent); transform: translateY(-1px); }
.icon-btn .dot-notif { position: absolute; top: 10px; right: 11px; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; border: 2px solid var(--card-solid); }

.btn-primary-c { height: 44px; padding: 0 18px; border-radius: 12px; background: var(--accent); color: #0d0d12; border: none; font-weight: 700; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 180ms; box-shadow: 0 8px 22px -8px var(--accent-glow); font-family: inherit; }
.btn-primary-c:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 12px 28px -8px var(--accent-glow); }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
.stat-card { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 22px; position: relative; overflow: hidden; transition: all 220ms; }
.stat-card:hover { border-color: var(--border-strong); transform: translateY(-2px); }
.stat-card::after { content: ''; position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle, var(--accent-glow), transparent 70%); opacity: 0; transition: opacity 220ms; }
.stat-card:hover::after { opacity: 1; }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; }
.stat-icon.amber { background: var(--accent-glow); color: var(--accent); }
.stat-icon.green { background: var(--success-bg); color: var(--success); }
.stat-icon.blue { background: var(--info-bg); color: var(--info); }
.stat-icon.red { background: var(--danger-bg); color: var(--danger); }
.stat-label { font-size: 12.5px; color: var(--text-muted); font-weight: 500; margin-bottom: 6px; }
.stat-value { font-size: 28px; font-weight: 800; letter-spacing: -0.025em; line-height: 1; }
.stat-sub { font-size: 11.5px; color: var(--text-faint); margin-top: 10px; font-weight: 500; }

.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 17px; font-weight: 700; margin: 0; letter-spacing: -0.015em; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }

.status-tabs { display: flex; gap: 3px; padding: 4px; background: var(--bg); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; scrollbar-width: none; }
.status-tabs::-webkit-scrollbar { display: none; }
.status-tab { padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--text-muted); background: transparent; border: none; cursor: pointer; transition: all 180ms; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; font-family: inherit; }
.status-tab:hover { color: var(--text); }
.status-tab.active { background: var(--card-solid); color: var(--text); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.status-tab .count { background: var(--border-strong); padding: 1px 7px; border-radius: 6px; font-size: 11px; font-weight: 700; min-width: 22px; text-align: center; }
.status-tab.active .count { background: var(--accent); color: #0d0d12; }

.toolbar { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: rgba(0,0,0,0.02); }
.toolbar-spacer { flex: 1; }
.result-count { font-size: 13px; color: var(--text-muted); }
.result-count strong { color: var(--text); font-weight: 700; }

.table-wrap { overflow-x: auto; }
.appointments-table { width: 100%; border-collapse: collapse; min-width: 920px; }
.appointments-table thead th { text-align: left; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--text-faint); padding: 14px 22px; border-bottom: 1px solid var(--border); background: rgba(0,0,0,0.04); white-space: nowrap; }
.appointments-table thead th.right { text-align: right; }
.appointments-table tbody td { padding: 18px 22px; border-bottom: 1px solid var(--border); font-size: 14px; vertical-align: middle; }
.appointments-table tbody tr { transition: background 150ms; }
.appointments-table tbody tr:hover { background: rgba(245, 181, 68, 0.03); }
.appointments-table tbody tr:last-child td { border-bottom: none; }

.time-cell { font-weight: 700; font-size: 14.5px; font-variant-numeric: tabular-nums; letter-spacing: -0.01em; }
.time-cell .duration { display: flex; align-items: center; gap: 4px; font-size: 11.5px; font-weight: 500; color: var(--text-faint); margin-top: 4px; }
.time-cell .duration .icon { width: 12px; height: 12px; }

.client-cell { display: flex; align-items: center; gap: 12px; }
.client-avatar { width: 38px; height: 38px; border-radius: 50%; display: grid; place-items: center; font-weight: 700; color: white; font-size: 12.5px; flex-shrink: 0; letter-spacing: -0.02em; }
.av-amber { background: linear-gradient(135deg, #f5b544, #e89538); }
.av-blue { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
.av-green { background: linear-gradient(135deg, #4ade80, #22c55e); }
.av-red { background: linear-gradient(135deg, #f87171, #ef4444); }
.av-purple { background: linear-gradient(135deg, #c084fc, #a855f7); }
.av-pink { background: linear-gradient(135deg, #f472b6, #ec4899); }
.client-name { font-weight: 600; font-size: 14px; line-height: 1.2; }
.client-meta { font-size: 12px; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 5px; }

.barber-cell { display: flex; align-items: center; gap: 10px; }
.barber-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.barber-name { font-weight: 500; font-size: 13.5px; }
.barber-role { font-size: 11.5px; color: var(--text-muted); }

.service-cell .svc-name { font-weight: 600; font-size: 14px; }
.service-cell .svc-meta { font-size: 12px; color: var(--text-muted); margin-top: 3px; }

.status-badge { display: inline-flex; align-items: center; gap: 7px; padding: 5px 11px 5px 9px; border-radius: 999px; font-size: 12px; font-weight: 600; letter-spacing: 0.01em; white-space: nowrap; }
.status-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.status-pending { background: var(--warning-bg); color: var(--warning); }
.status-pending::before { background: var(--warning); animation: pulse-dot 2s infinite; }
.status-confirmed { background: var(--info-bg); color: var(--info); }
.status-confirmed::before { background: var(--info); }
.status-completed { background: var(--success-bg); color: var(--success); }
.status-completed::before { background: var(--success); }
.status-canceled { background: var(--danger-bg); color: var(--danger); }
.status-canceled::before { background: var(--danger); }

.value-cell { font-weight: 700; font-size: 14.5px; font-variant-numeric: tabular-nums; letter-spacing: -0.01em; text-align: right; }
.value-cell .sub { display: block; font-size: 11.5px; color: var(--text-faint); font-weight: 500; margin-top: 3px; }

.actions-cell { display: flex; gap: 4px; justify-content: flex-end; }
.action-btn { width: 34px; height: 34px; border-radius: 9px; border: 1px solid var(--border); background: transparent; color: var(--text-muted); display: grid; place-items: center; cursor: pointer; transition: all 150ms; }
.action-btn:hover { color: var(--accent); border-color: var(--accent); background: var(--accent-glow); }
.action-btn.danger:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-bg); }
.action-btn.info:hover { color: var(--info); border-color: var(--info); background: var(--info-bg); }
.action-btn.success:hover { color: var(--success); border-color: var(--success); background: var(--success-bg); }

.panel-footer { padding: 14px 24px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.result-info { font-size: 13px; color: var(--text-muted); }
.result-info strong { color: var(--text); font-weight: 700; }

.live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px var(--success-bg); animation: pulse-dot 2s infinite; display: inline-block; }
.pipe { color: var(--text-faint); }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.fade-in { animation: fadeInUp 400ms ease both; }
.d1 { animation-delay: 50ms; } .d2 { animation-delay: 100ms; } .d3 { animation-delay: 150ms; } .d4 { animation-delay: 200ms; } .d5 { animation-delay: 250ms; }

@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .search-box { width: 220px; } }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .appointments-table thead { display: none; } .appointments-table, .appointments-table tbody, .appointments-table tr, .appointments-table td { display: block; width: 100%; } .appointments-table tr { padding: 14px 18px; border-bottom: 1px solid var(--border); } .appointments-table tbody td { padding: 6px 0; border: none; display: flex; justify-content: space-between; align-items: center; } .appointments-table tbody td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; margin-right: 12px; } .actions-cell { justify-content: flex-end; } }
</style>
@endpush

@section('content')

@php
$slug = request()->route('barbearia')?->slug;
@endphp

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
        <symbol id="i-filter" viewBox="0 0 24 24" fill="none"><path d="M5.5 6h13l-4.5 6v5l-4 3v-8L5.5 6z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
        <symbol id="i-more" viewBox="0 0 24 24" fill="none"><path d="M5 12h.01M12 12h.01M19 12h.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></symbol>
        <symbol id="i-call" viewBox="0 0 24 24" fill="none"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-message" viewBox="0 0 24 24" fill="none"><path d="M17 9.5v3.6c0 .35-.27.62-.62.62h-3.99c-.79 0-1.55.31-2.11.86l-3.42 3.41c-.32.32-.86.09-.86-.36V7.91c0-1.61 1.3-2.91 2.91-2.91h7.18c1.61 0 2.91 1.3 2.91 2.91V9.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M21 9.5v3.6c0 .35-.27.62-.62.62h-3.99c-.79 0-1.55.31-2.11.86l-3.42 3.41c-.32.32-.86.09-.86-.36V7.91" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-close" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-chevron-down" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-logout" viewBox="0 0 24 24" fill="none"><path d="M15 18.5H7.5c-1.5 0-2.5-1-2.5-2.5v-8c0-1.5 1-2.5 2.5-2.5H15M11 12h10M17.5 8.5L21 12l-3.5 3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-up" viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-down" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M19 12l-7 7-7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-menu" viewBox="0 0 24 24" fill="none"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-arrow-left" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 18l6-6-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-calendar-tick" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5l1.5 1.5 2.5-2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-receipt" viewBox="0 0 24 24" fill="none"><path d="M4 5c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v15.5l-2.5-1.5L15 20.5 12.5 19 10 20.5 7.5 19 5 20.5 4 19.5V5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 8h8M8 11.5h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-user-plus" viewBox="0 0 24 24" fill="none"><path d="M14 7.5c0 2.07-1.68 3.75-3.75 3.75S6.5 9.57 6.5 7.5 8.18 3.75 10.25 3.75 14 5.43 14 7.5z" stroke="currentColor" stroke-width="1.6"/><path d="M2.5 20c0-3.5 3-5.5 6-5.5M19 8v6M22 11h-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24" fill="none"><path d="M13.26 3.6l-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.98 1.87 1.8l3.22-.55c.45-.08 1.08-.41 1.39-.75L18.86 8.6c.75-.81.8-2.01-.02-2.79l-1.6-1.54c-.83-.79-2.16-.76-2.98.08z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.47 5.08l3.43 3.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    </defs>
</svg>

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-calendar-tick"/></svg></div>
        </div>
        <div class="stat-label">Agendamentos hoje</div>
        <div class="stat-value">{{ $totalHoje }}</div>
        <div class="stat-sub">{{ $confirmados }} confirmados · {{ $pendentes }} pendentes</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-wallet"/></svg></div>
        </div>
        <div class="stat-label">Faturamento previsto</div>
        <div class="stat-value">R$ {{ number_format($faturamento, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $realizados }} realizados hoje</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-check"/></svg></div>
        </div>
        <div class="stat-label">Confirmados</div>
        <div class="stat-value">{{ $confirmados }}</div>
        <div class="stat-sub">{{ $totalHoje > 0 ? round(($confirmados / $totalHoje) * 100) : 0 }}% do total</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon red"><svg class="icon"><use href="#i-people"/></svg></div>
        </div>
        <div class="stat-label">Pendentes</div>
        <div class="stat-value">{{ $pendentes }}</div>
        <div class="stat-sub">{{ $cancelados }} cancelados</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-calendar"/></svg></div>
            <div>
                <h2 class="panel-title">Lista de agendamentos</h2>
                <div class="panel-subtitle" id="panelDate">{{ \Carbon\Carbon::parse($data)->translatedFormat('j \d\e F · l') }}</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <button class="btn-ghost-c" style="height:32px;padding:0 10px;font-size:12px;" onclick="mudarData(-1)">&larr; Dia anterior</button>
            <button class="btn-ghost-c" style="height:32px;padding:0 10px;font-size:12px;" onclick="mudarData(0)">Hoje</button>
            <button class="btn-ghost-c" style="height:32px;padding:0 10px;font-size:12px;" onclick="mudarData(1)">Próximo dia &rarr;</button>
            <input type="date" id="datePicker" value="{{ $data }}" onchange="irParaData(this.value)" style="height:32px;padding:0 8px;border-radius:8px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
        </div>
        <div class="status-tabs" id="statusTabs">
            <button class="status-tab active" data-status="all">Todos <span class="count">{{ $totalHoje }}</span></button>
            <button class="status-tab" data-status="pendente">Pendente <span class="count">{{ $pendentes }}</span></button>
            <button class="status-tab" data-status="confirmado">Confirmado <span class="count">{{ $confirmados }}</span></button>
            <button class="status-tab" data-status="realizado">Realizado <span class="count">{{ $realizados }}</span></button>
            <button class="status-tab" data-status="cancelado">Cancelado <span class="count">{{ $cancelados }}</span></button>
        </div>
    </div>

    <div class="toolbar">
        <select id="filterBarbeiro" class="form-control" style="width:auto;min-width:160px;height:34px;padding:0 10px;border-radius:8px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;" onchange="filtrar()">
            <option value="">Todos os barbeiros</option>
            @foreach($barbeiros as $b)
            <option value="{{ $b->id }}" {{ $barbeiroId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
            @endforeach
        </select>
        <select id="filterBarbearia" class="form-control" style="width:auto;min-width:160px;height:34px;padding:0 10px;border-radius:8px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;" onchange="filtrar()">
            <option value="">Todas as unidades</option>
            @foreach($barbearias as $b)
            <option value="{{ $b->id }}" {{ $barbeariaId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
            @endforeach
        </select>
        <div class="toolbar-spacer"></div>
        <div class="result-count" id="resultCount"><strong>{{ $totalHoje }}</strong> de <strong>{{ $totalHoje }}</strong> resultados</div>
    </div>

    <div class="table-wrap">
        <table class="appointments-table">
            <thead>
                <tr>
                    <th style="width:110px">Hora</th>
                    <th>Cliente</th>
                    <th>Barbeiro</th>
                    <th>Serviços</th>
                    <th>Status</th>
                    <th class="right">Valor</th>
                    <th class="right" style="width:120px">Ações</th>
                </tr>
            </thead>
            <tbody id="appointmentsBody">
                @forelse($agendamentos as $agendamento)
                @php
                    $s = $statusMap[$agendamento->status] ?? ['label' => ucfirst($agendamento->status), 'cls' => 'status-pending'];
                    $avClass = $avatarClasses[$agendamento->cliente_id % 6];
                    $barberColor = $barberColors[$agendamento->barbeiro_id % 6];
                    $diff = Carbon::parse($agendamento->hora_inicio)->diffInMinutes(Carbon::parse($agendamento->hora_fim));
                    $durationLabel = $diff >= 60 ? floor($diff / 60) . 'h ' . ($diff % 60) . 'min' : $diff . ' min';
                    $serviceNames = $agendamento->servicos->pluck('nome')->implode(', ');
                    $serviceMeta = $agendamento->servicos->count() . ' serviço(s)';
                    $pagamento = $agendamento->forma_pagamento ?? '—';
                    $temPlano = $agendamento->cliente->relationLoaded('planos')
                        ? $agendamento->cliente->planos->where('ativo', true)->isNotEmpty()
                        : ($agendamento->cliente->planoAtivo ? true : false);
                    $initials = getInitials($agendamento->cliente->nome);
                @endphp
                <tr data-status="{{ $agendamento->status }}">
                    <td data-label="Hora">
                        <div class="time-cell">{{ $agendamento->hora_inicio instanceof \Carbon\Carbon ? $agendamento->hora_inicio->format('H:i') : $agendamento->hora_inicio }}<span class="duration"><svg class="icon"><use href="#i-clock"/></svg>{{ $durationLabel }}</span></div>
                    </td>
                    <td data-label="Cliente">
                        <div class="client-cell">
                            <div class="client-avatar {{ $avClass }}">{{ $initials }}</div>
                            <div>
                                <div class="client-name">{{ $agendamento->cliente->nome }} @if($temPlano)<span class="badge-c gold" style="font-size:10px;padding:1px 6px;margin-left:4px;">Plano</span>@endif</div>
                                <div class="client-meta"><svg class="icon icon-sm"><use href="#i-call"/></svg>{{ $agendamento->cliente->telefone }}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Barbeiro">
                        <div class="barber-cell">
                            <span class="barber-dot" style="background:{{ $barberColor }}"></span>
                            <div>
                                <div class="barber-name">{{ $agendamento->barbeiro->nome }}</div>
                                <div class="barber-role">Barbeiro</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Serviços">
                        <div class="service-cell">
                            <div class="svc-name">{{ $serviceNames }}</div>
                            <div class="svc-meta">{{ $serviceMeta }}</div>
                        </div>
                    </td>
                    <td data-label="Status"><span class="status-badge {{ $s['cls'] }}">{{ $s['label'] }}</span></td>
                    <td data-label="Valor" class="value-cell">R$ {{ number_format($agendamento->total, 2, ',', '.') }}@if($pagamento !== '—')<span class="badge-c outlined" style="font-size:10px;padding:1px 5px;margin-top:2px;">{{ $pagamento }}</span>@endif</td>
                    <td data-label="Ações">
                        <div class="actions-cell">
                            @php
                            $realizarUrl = $slug
                                ? route('tenant.admin.agendamentos.realizar', [$slug, $agendamento->id])
                                : route('admin.agendamentos.realizar', $agendamento->id);
                            @endphp
                            @php $horaAgd = $agendamento->hora_inicio instanceof \Carbon\Carbon ? $agendamento->hora_inicio->format('H:i') : $agendamento->hora_inicio; @endphp
                            @if(in_array($agendamento->status, ['pendente', 'confirmado']))
                            <button class="action-btn warning" title="Realizar" data-action="{{ $realizarUrl }}" onclick="abrirModalRealizar(this, '{{ addslashes($agendamento->cliente->nome) }}', '{{ $horaAgd }}')">
                                <svg class="icon icon-sm"><use href="#i-check-double"/></svg>
                            </button>
                            @endif
                            <a href="{{ $slug ? route('tenant.admin.agendamentos.edit', [$slug, $agendamento]) : route('admin.agendamentos.edit', $agendamento) }}" class="action-btn" title="Editar">
                                <svg class="icon icon-sm"><use href="#i-edit"/></svg>
                            </a>
                            <a href="tel:{{ $agendamento->cliente->telefone }}" class="action-btn info" title="Ligar" target="_blank">
                                <svg class="icon icon-sm"><use href="#i-call"/></svg>
                            </a>
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $agendamento->cliente->telefone) }}" class="action-btn success" title="WhatsApp" target="_blank">
                                <svg class="icon icon-sm"><use href="#i-message"/></svg>
                            </a>
                            <button class="action-btn danger" title="Excluir" onclick="confirmarExclusao('{{ $slug ? route('tenant.admin.agendamentos.destroy', [$slug, $agendamento]) : route('admin.agendamentos.destroy', $agendamento) }}')">
                                <svg class="icon icon-sm"><use href="#i-close"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px 22px;color:var(--text-muted);">Nenhum agendamento encontrado para esta data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="panel-footer">
        <div class="result-info">Mostrando <strong id="showingCount">{{ $totalHoje }}</strong> de <strong>{{ $totalHoje }}</strong> agendamentos</div>
    </div>
</section>

@push('modals')
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
@endpush

<div class="modal fade" id="modalNovoAgendamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ $slug ? route('tenant.admin.agendamentos.store', $slug) : route('admin.agendamentos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="data" value="{{ request('data', now()->format('Y-m-d')) }}">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Barbearia</label>
                            <select name="barbearia_id" class="form-control" id="barbeariaSelect">
                                <option value="">Selecione...</option>
                                @foreach($barbearias as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Cliente</label>
                            @livewire('admin.buscar-cliente')
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Barbeiro</label>
                            <select name="barbeiro_id" class="form-control" id="barbeiroSelect" required>
                                <option value="">Selecione...</option>
                                @foreach($barbeiros as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Horário</label>
                            <select name="hora_inicio" class="form-control" id="horarioSelect" required>
                                <option value="">Selecione barbeiro e data primeiro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Serviços</label>
                            <div class="border rounded p-2" style="max-height:150px;overflow-y:auto">
                                @foreach($servicos as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servico_ids[]" value="{{ $s->id }}" id="servico{{ $s->id }}">
                                    <label class="form-check-label small" for="servico{{ $s->id }}">{{ $s->nome }} - R$ {{ number_format($s->preco, 2, ',', '.') }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Forma de Pagamento</label>
                            <select name="forma_pagamento" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Pix">Pix</option>
                                <option value="Boleto">Boleto</option>
                                <option value="Plano">Plano</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="usar_plano" value="1" id="usarPlano">
                                <label class="form-check-label" for="usarPlano">Usar cota do plano</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Observações</label>
                            <textarea name="observacoes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentDate = '{{ $data }}';

function mudarData(delta) {
    const d = new Date(currentDate);
    d.setDate(d.getDate() + delta);
    irParaData(d.toISOString().split('T')[0]);
}

function irParaData(data) {
    currentDate = data;
    document.getElementById('datePicker').value = data;
    const params = new URLSearchParams(window.location.search);
    params.set('data', data);
    window.location.search = params.toString();
}

function abrirModalRealizar(btn, nome, hora) {
    document.getElementById('realizarInfo').textContent = 'Realizar serviço de ' + nome + ' às ' + hora + '?';
    document.getElementById('formRealizar').action = btn.dataset.action;
    new bootstrap.Modal(document.getElementById('modalRealizar')).show();
}

function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: 'Esta ação não pode ser desfeita.',
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

$('#barbeiroSelect').change(function() {
    const barbeiroId = $(this).val();
    const data = $('input[name="data"]').val();
    if (barbeiroId && data) {
        $.get('{{ route("admin.agendamentos.horarios") }}', { barbeiro_id: barbeiroId, data: data }, function(res) {
            const select = $('#horarioSelect');
            select.html('<option value="">Selecione...</option>');
            res.forEach(function(h) { select.append('<option value="' + h + '">' + h + '</option>'); });
        });
    }
});

const statusTabs = document.querySelectorAll('.status-tab');
const rows = document.querySelectorAll('#appointmentsBody tr[data-status]');
const resultCount = document.getElementById('resultCount');
const showingCount = document.getElementById('showingCount');

statusTabs.forEach(tab => {
    tab.addEventListener('click', function() {
        statusTabs.forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.status;
        let visible = 0;
        rows.forEach(row => {
            const match = filter === 'all' || row.dataset.status === filter;
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        const total = rows.length;
        resultCount.innerHTML = '<strong>' + visible + '</strong> de <strong>' + total + '</strong> resultados';
        showingCount.textContent = visible;
    });
});

const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;
themeToggle.addEventListener('click', function() {
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
    this.innerHTML = isDark
        ? '<svg class="icon"><use href="#i-moon"/></svg>'
        : '<svg class="icon"><use href="#i-sun"/></svg>';
});

document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-box input').focus();
    }
});

function filtrar() {
    const params = new URLSearchParams(window.location.search);
    const barbeiro = document.getElementById('filterBarbeiro').value;
    const barbearia = document.getElementById('filterBarbearia').value;
    if (barbeiro) params.set('barbeiro_id', barbeiro); else params.delete('barbeiro_id');
    if (barbearia) params.set('barbearia_id', barbearia); else params.delete('barbearia_id');
    window.location.search = params.toString();
}

document.getElementById('searchInput')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const allRows = document.querySelectorAll('#appointmentsBody tr[data-status]');
    allRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
});
</script>
@endpush
