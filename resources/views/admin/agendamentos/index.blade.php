@extends('layouts.app')

@section('title', 'Agendamentos')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-home"/></svg>
    <span class="sep">/</span>
    <span>Barber Control</span>
    <span class="sep">/</span>
    <span class="current">Agendamentos</span>
@endsection

@php
use Carbon\Carbon;
$slug = request()->route('barbearia')?->slug;
$visao = $visao ?? request('visao', 'semana');
$ref = $ref ?? Carbon::parse($data ?? now()->format('Y-m-d'));
$hojeStr = Carbon::today()->format('Y-m-d');
$agendamentosDia = $agendamentosDia ?? $agendamentos;
$totalPeriodo = $agendamentos->count();
$pendentesCount = $agendamentos->where('status', '!=', 'realizado')->count();
$realizadosCount = $agendamentos->where('status', 'realizado')->count();
$totalDia = $agendamentosDia->count();
$totalServicosPeriodo = $agendamentos->sum(fn ($ag) => $ag->servicos->count());
$confirmadosPeriodo = $agendamentos->where('status', 'confirmado')->count();
$pendentesPeriodo = $agendamentos->where('status', 'pendente')->count();
$avatarClasses = ['av-amber', 'av-blue', 'av-green', 'av-purple', 'av-pink', 'av-red'];
$statusMap = [
    'pendente' => ['label' => 'Pendente', 'cls' => 'status-pending'],
    'confirmado' => ['label' => 'Confirmado', 'cls' => 'status-confirmed'],
    'realizado' => ['label' => 'Realizado', 'cls' => 'status-completed'],
    'cancelado' => ['label' => 'Cancelado', 'cls' => 'status-canceled'],
    'ausente' => ['label' => 'Ausente', 'cls' => 'status-canceled'],
];
if (!function_exists('getInitials')) {
    function getInitials($name) {
        $parts = explode(' ', trim($name));
        $i = mb_substr($parts[0] ?? '', 0, 1);
        if (isset($parts[1])) $i .= mb_substr($parts[1], 0, 1);
        return strtoupper($i);
    }
}
$diasSemanaNomes = ['seg', 'ter', 'qua', 'qui', 'sex', 'sáb', 'dom'];
$diasSemanaFull = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
$mesesPt = [1=>'janeiro',2=>'fevereiro',3=>'março',4=>'abril',5=>'maio',6=>'junho',7=>'julho',8=>'agosto',9=>'setembro',10=>'outubro',11=>'novembro',12=>'dezembro'];
if ($visao === 'dia') {
    $prevDate = $ref->copy()->subDay()->format('Y-m-d');
    $nextDate = $ref->copy()->addDay()->format('Y-m-d');
    $navPrevLabel = '← Dia anterior';
    $navNextLabel = 'Próximo dia →';
    $periodoLabel = $ref->translatedFormat('j \d\e F · l');
} elseif ($visao === 'mes') {
    $prevDate = $ref->copy()->subMonth()->format('Y-m-d');
    $nextDate = $ref->copy()->addMonth()->format('Y-m-d');
    $periodoLabel = 'Mês de ' . ($mesesPt[$ref->month] ?? $ref->format('F')) . ' de ' . $ref->year;
    $navPrevLabel = '← Mês anterior';
    $navNextLabel = 'Próximo mês →';
} else {
    $visao = 'semana';
    $prevDate = $ref->copy()->subDays(7)->format('Y-m-d');
    $nextDate = $ref->copy()->addDays(7)->format('Y-m-d');
    $periodoLabel = 'Semana ' . $semanaInicio->format('d/m') . ' a ' . $semanaFim->format('d/m/Y');
    $navPrevLabel = '← Semana anterior';
    $navNextLabel = 'Próxima semana →';
}
if (!function_exists('agUrl')) {
    function agUrl($visaoParam, $dataParam, $extra = []) {
        $params = array_merge(request()->query(), ['visao' => $visaoParam, 'data' => $dataParam], $extra);
        return url()->current() . '?' . http_build_query($params);
    }
}
$statusLabels = ['pendente'=>'Pendente','confirmado'=>'Confirmado','realizado'=>'Realizado','cancelado'=>'Cancelado','ausente'=>'Ausente'];
$agMap = [];
foreach ($agendamentos as $a) {
    $hora = $a->hora_inicio instanceof Carbon ? $a->hora_inicio->format('H:i') : substr((string) $a->hora_inicio, 0, 5);
    $dataBr = Carbon::parse($a->data)->format('d/m/Y');
    $dataIso = Carbon::parse($a->data)->format('Y-m-d');
    $svc = $a->servicos->pluck('nome')->implode(', ');
    $tel = $a->cliente->telefone ?? '';
    $wa = 'https://wa.me/55' . preg_replace('/[^0-9]/', '', (string) $tel);
    $pi = $a->clientePlano ?? $a->plano_info;
    $agMap[$a->id] = [
        'id' => $a->id,
        'nome' => $a->cliente->nome ?? '-',
        'telefone' => $tel,
        'data_br' => $dataBr,
        'data_iso' => $dataIso,
        'hora' => $hora,
        'status' => $a->status,
        'status_label' => $statusLabels[$a->status] ?? ucfirst((string) $a->status),
        'servicos' => $svc,
        'barbeiro' => $a->barbeiro->nome ?? '-',
        'plano' => $pi && $pi->plano ? $pi->plano->nome : '—',
        'pode_realizar' => in_array($a->status, ['pendente', 'confirmado']),
        'realizar_url' => $slug ? route('tenant.admin.agendamentos.realizar', [$slug, $a->id]) : route('admin.agendamentos.realizar', $a->id),
        'edit_url' => $slug ? route('tenant.admin.agendamentos.edit', [$slug, $a->id]) : route('admin.agendamentos.edit', $a->id),
        'pacote_url' => ($slug ? route('tenant.admin.clientes-planos.index', [$slug]) : route('admin.clientes-planos.index')) . '?cliente_id=' . $a->cliente_id,
        'vender_url' => ($slug ? route('tenant.admin.vendas.create', [$slug]) : route('admin.vendas.create')) . '?agendamento_id=' . $a->id,
        'whatsapp_url' => $wa,
        'cliente_id' => $a->cliente_id,
    ];
}
@endphp

@section('subtitle')
    <span class="live-dot"></span>
    <span>Ao vivo</span>
    <span class="pipe">·</span>
    <span>{{ $periodoLabel }}</span>
    <span class="pipe">·</span>
    <span>{{ $totalPeriodo }} agendamentos no período</span>
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
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: var(--card-solid); border: 1px solid var(--border); border-radius: 20px; padding: 1.75rem; position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.stat-card:hover { border-color: var(--border-strong); transform: translateY(-4px); }
.stat-card::after { content: ''; position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; border-radius: 50%; background: radial-gradient(circle, var(--accent-glow), transparent 70%); opacity: 0; transition: opacity 220ms; }
.stat-card:hover::after { opacity: 1; }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.4rem; }
.stat-icon { width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center; transition: transform 0.3s ease; }
.stat-card:hover .stat-icon { transform: scale(1.08) rotate(-5deg); }
.stat-icon.amber { background: var(--accent-glow); color: var(--accent); }
.stat-icon.green { background: var(--success-bg); color: var(--success); }
.stat-icon.blue { background: var(--info-bg); color: var(--info); }
.stat-icon.red { background: var(--danger-bg); color: var(--danger); }
.stat-label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
.stat-value { font-size: 2.4rem; font-weight: 800; letter-spacing: -1.5px; line-height: 1; }
.stat-sub { font-size: 0.78rem; color: var(--text-faint); margin-top: 10px; font-weight: 500; }
.panel { background: var(--card-solid); border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }
.panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }
.panel-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
/* alternador de visão dia|semana|mes */
.view-switch { display: flex; gap: 3px; padding: 4px; background: var(--bg); border: 1px solid var(--border); border-radius: 12px; }
.view-switch a { padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 700; color: var(--text-muted); text-decoration: none; transition: all 180ms; white-space: nowrap; }
.view-switch a:hover { color: var(--text); }
.view-switch a.active { background: var(--accent); color: #0d0d12; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.status-toggle { display: flex; gap: 3px; padding: 4px; background: var(--bg); border: 1px solid var(--border); border-radius: 12px; }
.status-toggle-btn { padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--text-muted); background: transparent; border: none; cursor: pointer; transition: all 180ms; display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; font-family: inherit; }
.status-toggle-btn:hover { color: var(--text); }
.status-toggle-btn.active { background: var(--card-solid); color: var(--text); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.status-toggle-btn .count { background: var(--border-strong); padding: 1px 7px; border-radius: 6px; font-size: 11px; font-weight: 700; min-width: 22px; text-align: center; }
.status-toggle-btn.active .count { background: var(--accent); color: #0d0d12; }
.status-toggle-btn.active svg { color: var(--accent); }
.toolbar { padding: 1rem 1.5rem; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; border-bottom: 1px solid var(--border); }
.toolbar-spacer { flex: 1; }
.result-count { font-size: 13px; color: var(--text-muted); }
.result-count strong { color: var(--text); font-weight: 700; }
.table-wrap { overflow-x: auto; padding: 0 1.5rem; }
.appointments-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; min-width: 920px; }
.appointments-table thead th { text-align: center; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); padding: 0 22px 8px; border-bottom: none; background: transparent; white-space: nowrap; }
.appointments-table tbody td { padding: 1rem 22px; border-bottom: none; font-size: 14px; vertical-align: middle; }
.appointments-table tbody tr { background: var(--bg-input); transition: all 0.2s ease; }
.appointments-table tbody tr:hover { background: var(--card-hover); transform: translateX(4px); }
.appointments-table tbody td:first-child { border-radius: 14px 0 0 14px; }
.appointments-table tbody td:last-child { border-radius: 0 14px 14px 0; }
.time-cell { font-weight: 800; font-size: 17px; font-variant-numeric: tabular-nums; letter-spacing: -0.01em; color: var(--accent); background: var(--accent-glow); border-radius: 12px; padding: 10px 12px; display: inline-flex; flex-direction: column; min-width: 74px; text-align: center; }
.time-cell .duration { display: flex; align-items: center; justify-content: center; gap: 4px; font-size: 11px; font-weight: 600; color: var(--accent); margin-top: 3px; opacity: 0.75; }
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
.actions-cell { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap; }
.action-btn { height: 34px; min-width: 90px; padding: 0 12px; border-radius: 9px; border: none; background: var(--info); color: #fff; display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: all 150ms; font-size: 12px; font-weight: 700; font-family: inherit; white-space: nowrap; text-decoration: none; }
.action-btn:hover { color: #fff; opacity: 0.88; transform: translateY(-1px); box-shadow: 0 6px 16px -6px currentColor; }
.action-btn.warning { background: var(--warning); color: #0d0d12; }
.action-btn.success { background: var(--success); }
.action-btn.danger { background: var(--danger); }
.action-btn.info { background: var(--info); }
.action-btn.neutral { background: var(--border-strong); color: var(--text); }
.action-label { font-size: 12px; }
/* ===== SEMANA ===== */
.week-scroll { display: grid; grid-template-columns: repeat(7, minmax(200px, 1fr)); gap: 12px; padding: 1.25rem 1.5rem; overflow-x: auto; }
.wk-day { background: var(--bg-input); border: 1.5px solid var(--border); border-radius: 16px; min-height: 220px; display: flex; flex-direction: column; overflow: hidden; animation: fadeInUp 400ms ease both; }
.wk-day.today { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.wk-head { padding: 12px 14px; background: var(--card-solid); border-bottom: 1px solid var(--border); text-align: center; text-decoration: none; color: inherit; display: block; transition: all 150ms; }
.wk-day.today .wk-head { background: var(--accent); color: #0d0d12; }
.wk-head:hover { filter: brightness(0.97); }
.wk-head .wk-dow { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; opacity: 0.7; }
.wk-day.today .wk-head .wk-dow { opacity: 0.8; }
.wk-head .wk-date { font-size: 16px; font-weight: 800; }
.wk-head .wk-total { font-size: 11px; font-weight: 600; opacity: 0.75; margin-top: 2px; }
.wk-today-seal { display: inline-block; font-size: 10px; font-weight: 800; background: #0d0d12; color: var(--accent); padding: 1px 8px; border-radius: 999px; margin-top: 4px; }
.wk-day:not(.today) .wk-today-seal { display: none; }
.wk-day.today .wk-today-seal { background: #0d0d12; color: #fff; }
.wk-body { padding: 10px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
.wk-card { background: var(--card-solid); border: 1px solid var(--border); border-radius: 12px; padding: 10px 11px; cursor: pointer; transition: all 150ms; animation: fadeInUp 350ms ease both; }
.wk-card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 8px 20px -10px rgba(0,0,0,0.35); }
.wk-card .wk-time { font-weight: 800; font-size: 13.5px; color: var(--accent); display: flex; align-items: center; gap: 5px; }
.wk-card .wk-name { font-weight: 700; font-size: 13.5px; margin-top: 3px; line-height: 1.25; }
.wk-card .wk-meta { font-size: 11.5px; color: var(--text-muted); margin-top: 3px; line-height: 1.35; }
.wk-card .wk-detail-btn { margin-top: 8px; width: 100%; height: 32px; border-radius: 9px; border: none; background: var(--accent); color: #0d0d12; font-weight: 800; font-size: 12px; cursor: pointer; transition: all 150ms; font-family: inherit; }
.wk-card .wk-detail-btn:hover { background: var(--accent-hover); }
.wk-empty { font-size: 12px; color: var(--text-faint); text-align: center; padding: 18px 6px; }
/* ===== MÊS ===== */
.mes-wrap { padding: 1.25rem 1.5rem; overflow-x: auto; }
.mes-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; min-width: 760px; }
.mes-dow { text-align: center; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); padding: 4px 0; }
.mes-cell { background: var(--bg-input); border: 1px solid var(--border); border-radius: 12px; min-height: 128px; padding: 8px; display: flex; flex-direction: column; gap: 5px; animation: fadeInUp 350ms ease both; }
.mes-cell.out { opacity: 0.45; }
.mes-cell.today { border: 1.5px solid var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.mes-top { display: flex; align-items: center; justify-content: space-between; }
.mes-num { font-weight: 800; font-size: 14px; width: 26px; height: 26px; border-radius: 8px; display: grid; place-items: center; }
.mes-cell.today .mes-num { background: var(--accent); color: #0d0d12; }
.mes-total { font-size: 11px; font-weight: 700; color: var(--accent); text-decoration: none; }
.mes-total:hover { text-decoration: underline; }
.mes-chip { font-size: 11px; font-weight: 600; background: var(--card-solid); border: 1px solid var(--border); border-radius: 8px; padding: 4px 7px; cursor: pointer; text-align: left; transition: all 130ms; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text); font-family: inherit; line-height: 1.3; }
.mes-chip:hover { border-color: var(--accent); color: var(--accent); transform: translateX(2px); }
.mes-chip b { color: var(--accent); }
.mes-more { font-size: 11px; color: var(--text-muted); text-align: center; text-decoration: none; font-weight: 700; }
.mes-more:hover { color: var(--accent); }
.mes-empty { font-size: 11px; color: var(--text-faint); text-align: center; }
/* modal detalhes */
.detalhe-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 14px; }
.detalhe-btn { min-height: 44px; border-radius: 11px; border: 1px solid var(--border-strong); background: var(--bg-input); color: var(--text); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 150ms; text-decoration: none; font-family: inherit; padding: 8px 10px; }
.detalhe-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-1px); }
.detalhe-btn.primary { background: var(--accent); border-color: var(--accent); color: #0d0d12; }
.detalhe-btn.primary:hover { background: var(--accent-hover); color: #0d0d12; }
.detalhe-btn.wide { grid-column: span 2; }
.panel-footer { padding: 0.5rem 1.5rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.result-info { font-size: 13px; color: var(--text-muted); }
.result-info strong { color: var(--text); font-weight: 700; }
.live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px var(--success-bg); animation: pulse-dot 2s infinite; display: inline-block; }
.pipe { color: var(--text-faint); }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
.fade-in { animation: fadeInUp 400ms ease both; }
.d1 { animation-delay: 50ms; } .d2 { animation-delay: 100ms; } .d3 { animation-delay: 150ms; } .d4 { animation-delay: 200ms; } .d5 { animation-delay: 250ms; }
.nav-btns { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.btn-ghost-c { height: 34px; padding: 0 12px; border-radius: 10px; background: transparent; color: var(--text-muted); border: 1px solid var(--border-strong); font-weight: 700; font-size: 12.5px; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; transition: all 180ms; font-family: inherit; text-decoration: none; white-space: nowrap; }
.btn-ghost-c:hover { color: var(--text); border-color: var(--accent); background: var(--accent-glow); }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .search-box { width: 220px; } }
@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr; } .table-wrap { padding: 0 1rem; }
  .appointments-table thead { display: none; } .appointments-table, .appointments-table tbody, .appointments-table tr, .appointments-table td { display: block; width: 100%; }
  .appointments-table tr { padding: 14px 16px; border: 1px solid var(--border); border-radius: 16px; margin-bottom: 12px; background: var(--bg-input); }
  .appointments-table tbody td { padding: 6px 0; border: none; display: flex; justify-content: space-between; align-items: center; }
  .appointments-table tbody td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; margin-right: 12px; flex-shrink: 0; }
  .actions-cell { justify-content: flex-end; flex-wrap: wrap; gap: 6px; }
  .week-scroll { grid-template-columns: repeat(7, minmax(220px, 1fr)); }
  .detalhe-grid { grid-template-columns: 1fr; } .detalhe-btn.wide { grid-column: span 1; }
}
</style>
@endpush

@section('content')

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-home" viewBox="0 0 24 24" fill="none"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17.5v-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5h.01M7.5 13.5h4.49" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-people" viewBox="0 0 24 24" fill="none"><path d="M16.67 20.17v-1.5c0-2.07-1.68-3.75-3.75-3.75H5.42c-2.07 0-3.75 1.68-3.75 3.75v1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.17" cy="7.42" r="3.75" stroke="currentColor" stroke-width="1.6"/><path d="M22 20.17v-1.5c0-1.69-1.13-3.12-2.67-3.58M15.42 4.05a3.74 3.74 0 0 1 0 6.74" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-scissor" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-close" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24" fill="none"><circle cx="11.5" cy="11.5" r="8.5" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-bell" viewBox="0 0 24 24" fill="none"><path d="M12 6.44v6.72M9 16.5h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M8.5 2.5H15.5c.55 0 1 .45 1 1v.74c0 .3.13.59.36.78.83.69 1.27 1.91 1.27 3.45v5.95c0 1.84-1.27 3.43-3.07 3.83-.97.22-1.96.36-2.95.41-.51.03-1.02.04-1.53.04s-1.02-.01-1.53-.04c-.99-.05-1.98-.19-2.95-.41-1.8-.4-3.07-1.99-3.07-3.83V8.47c0-1.54.44-2.76 1.27-3.45.23-.19.36-.48.36-.78V3.5c0-.55.45-1 1-1z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
        <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-moon" viewBox="0 0 24 24" fill="none"><path d="M3.27 12.31c.43 4.6 4.34 8.21 8.95 8.41 3.16.13 5.97-1.18 7.86-3.34.62-.71.27-1.32-.69-1.21-.55.06-1.11.04-1.69-.06-3.58-.6-6.32-3.45-6.65-7.06-.12-1.34.07-2.62.5-3.79.34-.92-.31-1.39-1.22-1.04-4.21 1.61-7.04 5.71-6.69 10.09z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24" fill="none"><path d="M6 12h12M12 6v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-call" viewBox="0 0 24 24" fill="none"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-calendar-tick" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5l1.5 1.5 2.5-2.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24" fill="none"><path d="M13.26 3.6l-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.98 1.87 1.8l3.22-.55c.45-.08 1.08-.41 1.39-.75L18.86 8.6c.75-.81.8-2.01-.02-2.79l-1.6-1.54c-.83-.79-2.16-.76-2.98.08z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.47 5.08l3.43 3.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    </defs>
</svg>

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top"><div class="stat-icon amber"><svg class="icon"><use href="#i-calendar-tick"/></svg></div></div>
        <div class="stat-label">Agendamentos no período</div>
        <div class="stat-value">{{ $totalPeriodo }}</div>
        <div class="stat-sub">{{ $confirmadosPeriodo }} confirmados · {{ $pendentesPeriodo }} pendentes</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top"><div class="stat-icon green"><svg class="icon"><use href="#i-scissor"/></svg></div></div>
        <div class="stat-label">Serviços no período</div>
        <div class="stat-value">{{ $totalServicosPeriodo }}</div>
        <div class="stat-sub">{{ $realizadosCount }} realizados</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top"><div class="stat-icon blue"><svg class="icon"><use href="#i-check"/></svg></div></div>
        <div class="stat-label">Confirmados</div>
        <div class="stat-value">{{ $confirmadosPeriodo }}</div>
        <div class="stat-sub">{{ $totalPeriodo > 0 ? round(($confirmadosPeriodo / $totalPeriodo) * 100) : 0 }}% do período</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top"><div class="stat-icon red"><svg class="icon"><use href="#i-people"/></svg></div></div>
        <div class="stat-label">Pendentes</div>
        <div class="stat-value">{{ $pendentesCount }}</div>
        <div class="stat-sub">{{ $totalDia }} no dia de referência</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-calendar"/></svg></div>
            <div>
                <h2 class="panel-title">Lista de agendamentos</h2>
                <div class="panel-subtitle" id="panelDate">{{ $periodoLabel }}</div>
            </div>
        </div>
        <div class="view-switch" role="tablist" aria-label="Alternar visão">
            <a href="{{ agUrl('dia', $ref->format('Y-m-d')) }}" class="{{ $visao === 'dia' ? 'active' : '' }}">Dia</a>
            <a href="{{ agUrl('semana', $ref->format('Y-m-d')) }}" class="{{ $visao === 'semana' ? 'active' : '' }}">Semana</a>
            <a href="{{ agUrl('mes', $ref->format('Y-m-d')) }}" class="{{ $visao === 'mes' ? 'active' : '' }}">Mês</a>
        </div>
        <div class="nav-btns">
            <a class="btn-ghost-c" href="{{ agUrl($visao, $prevDate) }}">{{ $navPrevLabel }}</a>
            <a class="btn-ghost-c" href="{{ agUrl($visao, Carbon::today()->format('Y-m-d')) }}">Hoje</a>
            <a class="btn-ghost-c" href="{{ agUrl($visao, $nextDate) }}">{{ $navNextLabel }}</a>
            <input type="date" id="datePicker" value="{{ $ref->format('Y-m-d') }}" onchange="irParaData(this.value)" style="height:34px;padding:0 8px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
        </div>
    </div>

    <div class="toolbar">
        <select id="filterBarbeiro" class="form-control" style="width:auto;min-width:160px;height:34px;padding:0 10px;border-radius:8px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;" onchange="filtrar()">
            <option value="">Todos os profissionais</option>
            @foreach($barbeiros as $b)
            <option value="{{ $b->id }}" {{ (string)$barbeiroId === (string)$b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
            @endforeach
        </select>
        <select id="filterBarbearia" class="form-control" style="width:auto;min-width:160px;height:34px;padding:0 10px;border-radius:8px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;" onchange="filtrar()">
            <option value="">Todas as unidades</option>
            @foreach($barbearias as $b)
            <option value="{{ $b->id }}" {{ (string)$barbeariaId === (string)$b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
            @endforeach
        </select>
        <div class="toolbar-spacer"></div>
        <div class="status-toggle" id="statusToggle">
            <button type="button" class="status-toggle-btn active" id="btnPendentes" onclick="toggleLista('pendentes')">
                <svg class="icon icon-xs"><use href="#i-clock"/></svg>
                Pendentes
                <span class="count" id="countPendentes">{{ $pendentesCount }}</span>
            </button>
            <button type="button" class="status-toggle-btn" id="btnRealizados" onclick="toggleLista('realizados')">
                <svg class="icon icon-xs"><use href="#i-check"/></svg>
                Realizados
                <span class="count" id="countRealizados">{{ $realizadosCount }}</span>
            </button>
        </div>
        <div class="result-count" id="resultCount"><strong>{{ $totalPeriodo }}</strong> de <strong>{{ $totalPeriodo }}</strong> resultados</div>
    </div>

    {{-- ============ VISÃO DIA ============ --}}
    <div id="view-dia" style="display:{{ $visao === 'dia' ? 'block' : 'none' }};">
        <div class="table-wrap">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th style="width:110px">Hora</th>
                        <th>Cliente</th>
                        <th>Serviços</th>
                        <th class="right" style="width:340px">Ações</th>
                    </tr>
                </thead>
                <tbody id="appointmentsBody">
                    @forelse($agendamentosDia as $agendamento)
                    @php
                        $avClass = $avatarClasses[($agendamento->cliente_id ?? 0) % 6];
                        try { $hiC = Carbon::parse($agendamento->hora_inicio); $hfC = Carbon::parse($agendamento->hora_fim); $diff = $hiC->diffInMinutes($hfC); } catch (\Exception $e) { $diff = 30; }
                        $durationLabel = $diff >= 60 ? floor($diff / 60) . 'h ' . ($diff % 60) . 'min' : $diff . ' min';
                        $serviceNames = $agendamento->servicos->pluck('nome')->implode(', ');
                        $serviceMeta = $agendamento->servicos->count() . ' serviço(s) · ' . ($agendamento->barbeiro->nome ?? '-');
                        $planoInfo = $agendamento->clientePlano ?? $agendamento->plano_info;
                        $temPlano = (bool) $planoInfo;
                        $resumo = $temPlano ? $agendamento->resumo_plano : null;
                        $initials = getInitials($agendamento->cliente->nome ?? '?');
                        $horaFmt = $agendamento->hora_inicio instanceof Carbon ? $agendamento->hora_inicio->format('H:i') : substr((string)$agendamento->hora_inicio, 0, 5);
                        $searchBlob = mb_strtolower(($agendamento->cliente->nome ?? '') . ' ' . ($agendamento->cliente->telefone ?? '') . ' ' . $serviceNames . ' ' . ($agendamento->barbeiro->nome ?? ''), 'UTF-8');
                        $realizarUrl = $slug ? route('tenant.admin.agendamentos.realizar', [$slug, $agendamento->id]) : route('admin.agendamentos.realizar', $agendamento->id);
                        $pacoteUrl = $slug ? route('tenant.admin.clientes-planos.index', [$slug]) . '?cliente_id=' . $agendamento->cliente_id : route('admin.clientes-planos.index') . '?cliente_id=' . $agendamento->cliente_id;
                        $editUrl = $slug ? route('tenant.admin.agendamentos.edit', [$slug, $agendamento]) : route('admin.agendamentos.edit', $agendamento);
                        $waUrl = 'https://wa.me/55' . preg_replace('/[^0-9]/', '', $agendamento->cliente->telefone ?? '');
                    @endphp
                    <tr data-status="{{ $agendamento->status }}" data-search="{{ e($searchBlob) }}" data-ag-id="{{ $agendamento->id }}" style="cursor:pointer" onclick="abrirDetalhes({{ $agendamento->id }})">
                        <td data-label="Hora">
                            <div class="time-cell">{{ $horaFmt }}@if($agendamento->encaixe)<span style="font-size:10px;font-weight:800;">🔀</span>@endif<span class="duration"><svg class="icon"><use href="#i-clock"/></svg>{{ $durationLabel }}</span></div>
                            <div style="margin-top:6px;"><span class="status-badge {{ $statusMap[$agendamento->status]['cls'] ?? 'status-pending' }}">{{ $statusMap[$agendamento->status]['label'] ?? ucfirst($agendamento->status) }}</span></div>
                        </td>
                        <td data-label="Cliente">
                            <div class="client-cell">
                                <div class="client-avatar {{ $avClass }}">{{ $initials }}</div>
                                <div style="min-width:0;flex:1">
                                    <div class="client-name">{{ $agendamento->cliente->nome }} @if($temPlano)<span class="badge-c gold" style="font-size:10px;padding:1px 6px;margin-left:4px;background:var(--accent-glow);color:var(--accent);border-radius:6px;font-weight:700;" title="{{ $planoInfo->plano->nome ?? 'Plano' }}">📦 {{ $planoInfo->plano->nome ?? 'Plano' }}</span>@endif @if($agendamento->encaixe)<span style="font-size:9px;font-weight:800;background:var(--accent-glow);color:var(--accent);padding:1px 6px;margin-left:4px;border-radius:6px;border:1px dashed var(--accent);" title="Encaixe">🔀 Encaixe</span>@endif</div>
                                    <div class="client-meta"><svg class="icon icon-sm"><use href="#i-call"/></svg>{{ $agendamento->cliente->telefone }}</div>
                                    @if($temPlano && $resumo)
                                    <div class="small" style="font-size:11px;line-height:1.3;margin-top:3px;color:var(--text-muted)">📋 {{ $planoInfo->plano->nome ?? '' }} · {{ $planoInfo->total_restante }}/{{ $planoInfo->total_contratada }} restantes</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td data-label="Serviços">
                            <div class="service-cell">
                                <div class="svc-name">{{ $serviceNames }}</div>
                                <div class="svc-meta">{{ $serviceMeta }} · R$ {{ number_format($agendamento->total ?? 0, 2, ',', '.') }}</div>
                                @if($temPlano && $resumo)
                                    <div style="margin-top:6px;display:flex;flex-direction:column;gap:3px">
                                    @foreach($resumo['servicos_status'] as $st)
                                        <small style="font-size:11px;display:flex;align-items:center;gap:5px">
                                            <span>{{ $st['servico_nome'] }} R$ {{ number_format($st['preco'],2,',','.') }}</span>
                                            @if($st['dentro'])<span class="badge bg-success" style="font-size:9px;padding:2px 5px">dentro</span>
                                            @else<span class="badge bg-danger" style="font-size:9px;padding:2px 5px">fora • {{ $st['motivo'] }}</span>@endif
                                        </small>
                                    @endforeach
                                    @if($resumo['valor_excedente']>0)
                                        <small style="font-size:11px;color:var(--danger);font-weight:700">Excedente: R$ {{ number_format($resumo['valor_excedente'],2,',','.') }} a pagar</small>
                                    @elseif($resumo['dentro'])
                                        <small style="font-size:11px;color:var(--success);font-weight:600">✓ Dentro da cota — sem cobrança</small>
                                    @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td data-label="Ações" onclick="event.stopPropagation()">
                            <div class="actions-cell">
                                @if(in_array($agendamento->status, ['pendente', 'confirmado']))
                                <button class="action-btn warning" data-action="{{ $realizarUrl }}" data-cliente-id="{{ $agendamento->cliente_id }}" onclick="event.stopPropagation();abrirModalRealizar(this, '{{ addslashes($agendamento->cliente->nome) }}', '{{ $horaFmt }}')">
                                    <span class="action-label">✔ Realizar</span>
                                </button>
                                @endif
                                <a href="{{ $pacoteUrl }}" class="action-btn neutral" onclick="event.stopPropagation()" title="Criar pacote"><span class="action-label">📦 Pacote</span></a>
                                <a href="{{ $editUrl }}" class="action-btn" onclick="event.stopPropagation()"><span class="action-label">✏️ Editar</span></a>
                                <a href="{{ $waUrl }}" class="action-btn success" target="_blank" onclick="event.stopPropagation()"><span class="action-label">💬 WhatsApp</span></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:40px 22px;color:var(--text-muted);">Nenhum agendamento encontrado para esta data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============ VISÃO SEMANA ============ --}}
    <div id="view-semana" style="display:{{ $visao === 'semana' ? 'block' : 'none' }};">
        <div class="week-scroll" id="weekScroll">
            @foreach($semanaDias as $idx => $dia)
            @php
                $diaStr = $dia->format('Y-m-d');
                $lista = $agendamentosPorData->get($diaStr, collect())->sortBy(fn($a) => $a->hora_inicio instanceof Carbon ? $a->hora_inicio->format('H:i') : (string)$a->hora_inicio)->values();
                $isToday = $diaStr === $hojeStr;
            @endphp
            <div class="wk-day {{ $isToday ? 'today' : '' }} fade-in" style="animation-delay:{{ $idx * 60 }}ms" data-dia="{{ $diaStr }}">
                <a class="wk-head" href="{{ agUrl('dia', $diaStr) }}" title="Ver dia {{ $dia->format('d/m/Y') }}">
                    <div class="wk-dow">{{ $diasSemanaNomes[$idx] ?? $dia->translatedFormat('D') }}</div>
                    <div class="wk-date">{{ $dia->format('d/m') }}</div>
                    <div class="wk-total" data-wk-total>{{ $lista->count() }} ag.</div>
                    @if($isToday)<span class="wk-today-seal">Hoje</span>@endif
                </a>
                <div class="wk-body">
                    @forelse($lista as $ag)
                    @php
                        $horaFmt = $ag->hora_inicio instanceof Carbon ? $ag->hora_inicio->format('H:i') : substr((string)$ag->hora_inicio, 0, 5);
                        $svcN = $ag->servicos->pluck('nome')->implode(', ');
                        $searchBlob = mb_strtolower(($ag->cliente->nome ?? '') . ' ' . ($ag->cliente->telefone ?? '') . ' ' . $svcN . ' ' . ($ag->barbeiro->nome ?? ''), 'UTF-8');
                    @endphp
                    <div class="wk-card" data-status="{{ $ag->status }}" data-search="{{ e($searchBlob) }}" data-ag-id="{{ $ag->id }}" onclick="abrirDetalhes({{ $ag->id }})">
                        <div class="wk-time">{{ $horaFmt }} @if($ag->encaixe)<span title="Encaixe">🔀</span>@endif</div>
                        <div class="wk-name">{{ $ag->cliente->nome ?? '-' }}</div>
                        <div class="wk-meta">{{ $svcN }}<br><span>👤 {{ $ag->barbeiro->nome ?? '-' }}</span></div>
                        <button class="wk-detail-btn" onclick="event.stopPropagation();abrirDetalhes({{ $ag->id }})">Ver detalhes</button>
                    </div>
                    @empty
                    <div class="wk-empty">Sem agendamentos</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ============ VISÃO MÊS ============ --}}
    <div id="view-mes" style="display:{{ $visao === 'mes' ? 'block' : 'none' }};">
        <div class="mes-wrap">
            <div class="mes-grid">
                @foreach(['seg','ter','qua','qui','sex','sáb','dom'] as $dow)
                <div class="mes-dow">{{ $dow }}</div>
                @endforeach
                @foreach(($mesGrade ?? collect()) as $semana)
                    @foreach($semana as $dia)
                    @php
                        $diaStr = $dia->format('Y-m-d');
                        $lista = isset($agendamentosPorData) ? ($agendamentosPorData->get($diaStr, collect())->sortBy(fn($a) => $a->hora_inicio instanceof Carbon ? $a->hora_inicio->format('H:i') : (string)$a->hora_inicio)->values()) : collect();
                        $isToday = $diaStr === $hojeStr;
                        $isOut = $dia->month !== $ref->month;
                        $visiveis = $lista->take(4);
                        $restantes = $lista->count() - $visiveis->count();
                    @endphp
                    <div class="mes-cell {{ $isToday ? 'today' : '' }} {{ $isOut ? 'out' : '' }}" data-dia="{{ $diaStr }}">
                        <div class="mes-top">
                            <span class="mes-num">{{ $dia->day }}</span>
                            <a class="mes-total" href="{{ agUrl('dia', $diaStr) }}">{{ $lista->count() }} ag.</a>
                        </div>
                        @forelse($visiveis as $ag)
                        @php
                            $horaFmt = $ag->hora_inicio instanceof Carbon ? $ag->hora_inicio->format('H:i') : substr((string)$ag->hora_inicio, 0, 5);
                            $svcN = $ag->servicos->pluck('nome')->implode(', ');
                            $searchBlob = mb_strtolower(($ag->cliente->nome ?? '') . ' ' . ($ag->cliente->telefone ?? '') . ' ' . $svcN . ' ' . ($ag->barbeiro->nome ?? ''), 'UTF-8');
                        @endphp
                        <button class="mes-chip" data-status="{{ $ag->status }}" data-search="{{ e($searchBlob) }}" data-ag-id="{{ $ag->id }}" onclick="abrirDetalhes({{ $ag->id }})" title="{{ $ag->cliente->nome ?? '' }} — {{ $svcN }}"><b>{{ $horaFmt }}</b> {{ \Illuminate\Support\Str::limit($ag->cliente->nome ?? '-', 14) }}</button>
                        @empty
                        <div class="mes-empty">—</div>
                        @endforelse
                        @if($restantes > 0)
                        <a class="mes-more" href="{{ agUrl('dia', $diaStr) }}">+{{ $restantes }} mais</a>
                        @endif
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <div class="panel-footer">
        <div class="result-info">Mostrando <strong id="showingCount">{{ $totalPeriodo }}</strong> de <strong>{{ $totalPeriodo }}</strong> agendamentos</div>
    </div>
</section>

{{-- Modal detalhes --}}
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border);border-radius:18px;">
            <div class="modal-header" style="border-color:var(--border)">
                <div>
                    <h5 class="modal-title fw-bold" id="detTitulo" style="margin:0"></h5>
                    <div class="small" id="detSubtitulo" style="color:var(--text-muted)"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:6px;font-size:14px;">
                    <div>📞 <strong>Telefone:</strong> <span id="detTelefone"></span></div>
                    <div>💈 <strong>Serviços:</strong> <span id="detServicos"></span></div>
                    <div>👤 <strong>Profissional:</strong> <span id="detBarbeiro"></span></div>
                    <div>📦 <strong>Plano:</strong> <span id="detPlano"></span></div>
                </div>
                <div class="detalhe-grid">
                    <button class="detalhe-btn primary" id="detRealizar" onclick="realizarDoDetalhe()">✔ Realizar</button>
                    <a class="detalhe-btn" id="detEditar" href="#">✏️ Editar</a>
                    <a class="detalhe-btn" id="detVerDia" href="#">📅 Ver dia</a>
                    <a class="detalhe-btn" id="detPacote" href="#">📦 Criar pacote</a>
                    <a class="detalhe-btn" id="detVender" href="#">🛒 Vender</a>
                    <a class="detalhe-btn" id="detWhatsapp" href="#" target="_blank">💬 WhatsApp</a>
                    <button class="detalhe-btn" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <p id="realizarInfo" class="mb-2"></p>
                    <div id="realizarPlanoInfo" class="alert alert-light border small" style="display:none"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Forma de Pagamento *</label>
                        <select name="forma_pagamento" id="realizarForma" class="form-control" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Pix">Pix</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Plano">Plano</option>
                        </select>
                    </div>
                    <div class="mb-3" id="realizarUsarPlanoWrap" style="display:none">
                        <div class="form-check">
                            <input type="checkbox" name="usar_plano" value="1" id="realizarUsarPlano" class="form-check-input">
                            <label for="realizarUsarPlano" class="form-check-label fw-semibold">Usar cota do plano (desconta 1 cota por serviço)</label>
                        </div>
                        <small class="text-muted">Se marcado e dentro da cota, não lança no Caixa (já pago via mensalidade).</small>
                        <input type="hidden" name="cliente_plano_id" id="realizarClientePlanoId">
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
            <form action="{{ $slug ? route('tenant.admin.agendamentos.store', $slug) : route('admin.agendamentos.store') }}" method="POST" id="formNovoAgendamento">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success py-2">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger py-2">{{ session('error') }}</div>
                    @endif
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
                            <label>Data <span class="text-danger">*</span></label>
                            <input type="date" name="data" class="form-control" id="dataAgendamento" value="{{ request('data', now()->format('Y-m-d')) }}" required>
                        </div>
                        @php
                        $__tenantAg = request()->route('barbearia');
                        $agLivewireBarbeariaId = $__tenantAg?->id;
                        $agLivewireTenantIds = $__tenantAg?->tenantTreeIds() ?? [];
                        @endphp
                        <div class="col-md-12 mb-2">
                            <label>Cliente <span class="text-muted" style="font-weight:normal">(busque por nome ou telefone)</span></label>
                            @livewire('admin.buscar-cliente', ['barbearia_id' => $agLivewireBarbeariaId, 'tenantIds' => $agLivewireTenantIds])
                            <small class="text-muted">Se não encontrar, preencha nome/telefone abaixo para criar novo cliente.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Nome do cliente <span class="text-muted" style="font-weight:normal">(novo)</span></label>
                            <input type="text" name="cliente_nome_manual" id="clienteNomeManual" class="form-control" placeholder="Ex: João Silva">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Telefone <span class="text-muted" style="font-weight:normal">(opcional)</span></label>
                            <input type="tel" name="cliente_telefone_manual" id="clienteTelefoneManual" class="form-control" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Barbeiro <span class="text-danger">*</span></label>
                            <select name="barbeiro_id" class="form-control" id="barbeiroSelect" required>
                                <option value="">Selecione...</option>
                                @foreach($barbeiros as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Horário <span class="text-danger">*</span></label>
                            <select name="hora_inicio" class="form-control" id="horarioSelect" required>
                                <option value="">Selecione barbeiro e data primeiro</option>
                            </select>
                            <small class="text-muted" id="horarioHelp">Mesma disponibilidade do agendamento do cliente + bloqueios.</small>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3" style="background:var(--bg-input)">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="encaixeFlag" name="encaixe" value="1">
                                    <label class="form-check-label fw-semibold" for="encaixeFlag" title="Cliente entrou entre um horário e outro; libera sobreposição e sinaliza só o intervalo dele.">🔀 Encaixe (cliente entrou entre horários)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="permitirPassado" value="1">
                                    <label class="form-check-label fw-semibold" for="permitirPassado" title="Para lançar no fim do dia um horário vago que já passou (só mostra se não tinha ninguém).">⏪ Permitir horários já passados (lançamento no fim do dia)</label>
                                </div>
                                <div class="mt-2" id="horaFimEncaixeWrap" style="display:none">
                                    <label class="form-label small mb-1">Fim do encaixe <span class="text-muted" style="font-weight:normal">(opcional — sinaliza só o intervalo do encaixe)</span></label>
                                    <input type="time" name="hora_fim_manual" id="horaFimEncaixe" class="form-control" style="max-width:180px" placeholder="Ex: 16:20">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Serviços <span class="text-danger">*</span></label>
                            <div class="border rounded p-2" style="max-height:150px;overflow-y:auto" id="servicosList">
                                @foreach($servicos as $s)
                                <div class="form-check">
                                    <input class="form-check-input servico-check" type="checkbox" name="servico_ids[]" value="{{ $s->id }}" id="servico{{ $s->id }}">
                                    <label class="form-check-label small" for="servico{{ $s->id }}">{{ $s->nome }} - R$ {{ number_format($s->preco, 2, ',', '.') }} <small class="text-muted">({{ $s->duracao_minutos }}min)</small></label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 mb-2" id="clientePlanoBox" style="display:none"></div>
                        <div class="col-md-6 mb-3">
                            <label>Forma de Pagamento</label>
                            <select name="forma_pagamento" id="formaPagamentoSelect" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Pix">Pix</option>
                                <option value="Boleto">Boleto</option>
                                <option value="Plano">Plano</option>
                            </select>
                            <small class="text-muted" id="formaHelp">Se plano dentro da cota, não gera cobrança extra.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="usar_plano" value="1" id="usarPlano">
                                <label class="form-check-label" for="usarPlano">Usar cota do plano</label>
                            </div>
                            <input type="hidden" name="cliente_plano_id" id="clientePlanoIdInput">
                            <small class="text-muted" id="usarPlanoHelp" style="display:block">Selecione cliente para ver plano.</small>
                        </div>
                        <div class="col-12 mb-2" id="planoCotasPreview" style="display:none"></div>
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
let currentVisao = '{{ $visao }}';
let currentDate = '{{ $ref->format('Y-m-d') }}';
let listMode = 'pendentes';

function buildUrl(visao, data) {
    const params = new URLSearchParams(window.location.search);
    params.set('visao', visao);
    params.set('data', data);
    return window.location.pathname + '?' + params.toString();
}
function trocarVisao(v) { window.location.href = buildUrl(v, currentDate); }
function mudarPeriodo(delta) {
    const d = new Date(currentDate + 'T12:00:00');
    if (currentVisao === 'dia') d.setDate(d.getDate() + delta);
    else if (currentVisao === 'semana') d.setDate(d.getDate() + (delta * 7));
    else d.setMonth(d.getMonth() + delta);
    irParaData(d.toISOString().split('T')[0]);
}
function irParaData(data) {
    currentDate = data;
    const el = document.getElementById('datePicker');
    if (el) el.value = data;
    window.location.href = buildUrl(currentVisao, data);
}
function filtrar() {
    const params = new URLSearchParams(window.location.search);
    const barbeiro = document.getElementById('filterBarbeiro')?.value;
    const barbearia = document.getElementById('filterBarbearia')?.value;
    params.set('visao', currentVisao);
    params.set('data', currentDate);
    if (barbeiro) params.set('barbeiro_id', barbeiro); else params.delete('barbeiro_id');
    if (barbearia) params.set('barbearia_id', barbearia); else params.delete('barbearia_id');
    window.location.search = params.toString();
}
function toggleLista(mode) {
    listMode = mode;
    document.getElementById('btnPendentes')?.classList.toggle('active', mode === 'pendentes');
    document.getElementById('btnRealizados')?.classList.toggle('active', mode === 'realizados');
    aplicaFiltros();
}
function matchMode(status) {
    if (listMode === 'realizados') return status === 'realizado';
    return status !== 'realizado';
}
function aplicaFiltros() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
    // DIA
    const rows = [...document.querySelectorAll('#appointmentsBody tr[data-status]')];
    let shownDia = 0;
    rows.forEach(row => {
        const st = row.dataset.status || '';
        const txt = (row.dataset.search || row.textContent.toLowerCase());
        const ok = matchMode(st) && (!q || txt.includes(q));
        row.style.display = ok ? '' : 'none';
        if (ok) shownDia++;
    });
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();
    if (currentVisao === 'dia' && shownDia === 0 && rows.length > 0) {
        const tbody = document.getElementById('appointmentsBody');
        const tr = document.createElement('tr');
        tr.id = 'emptyRow';
        tr.innerHTML = '<td colspan="4" style="text-align:center;padding:40px 22px;color:var(--text-muted);">Nenhum agendamento encontrado para este filtro.</td>';
        tbody.appendChild(tr);
    }
    // SEMANA
    let shownSem = 0;
    document.querySelectorAll('#view-semana .wk-day').forEach(day => {
        let vis = 0;
        day.querySelectorAll('.wk-card').forEach(card => {
            const st = card.dataset.status || '';
            const txt = (card.dataset.search || card.textContent.toLowerCase());
            const ok = matchMode(st) && (!q || txt.includes(q));
            card.style.display = ok ? '' : 'none';
            if (ok) { vis++; shownSem++; }
        });
        let emp = day.querySelector('.wk-empty-filter');
        if (vis === 0) {
            if (!emp && day.querySelectorAll('.wk-card').length > 0) {
                emp = document.createElement('div');
                emp.className = 'wk-empty wk-empty-filter';
                emp.textContent = 'Nenhum resultado neste filtro';
                day.querySelector('.wk-body')?.appendChild(emp);
            }
        } else if (emp) emp.remove();
        const totalEl = day.querySelector('[data-wk-total]');
        if (totalEl) totalEl.textContent = vis + ' ag.';
    });
    // MÊS
    let shownMes = 0;
    document.querySelectorAll('#view-mes .mes-cell').forEach(cell => {
        let vis = 0;
        cell.querySelectorAll('.mes-chip').forEach(chip => {
            const st = chip.dataset.status || '';
            const txt = (chip.dataset.search || chip.textContent.toLowerCase());
            const ok = matchMode(st) && (!q || txt.includes(q));
            chip.style.display = ok ? '' : 'none';
            if (ok) { vis++; shownMes++; }
        });
        const totalEl = cell.querySelector('.mes-total');
        if (totalEl) {
            const all = cell.querySelectorAll('.mes-chip').length;
            totalEl.textContent = (q || listMode === 'realizados' ? vis + '/' + all : all) + ' ag.';
        }
    });
    const shown = currentVisao === 'dia' ? shownDia : (currentVisao === 'semana' ? shownSem : shownMes);
    const totalAll = document.querySelectorAll('#view-' + currentVisao + ' [data-status]').length || rows.length;
    const sc = document.getElementById('showingCount');
    if (sc) sc.textContent = shown;
    const rc = document.getElementById('resultCount');
    if (rc) rc.innerHTML = '<strong>' + shown + '</strong> de <strong>' + totalAll + '</strong> resultados';
}

/* ---- Modal detalhes ---- */
window.AG_MAP = {!! json_encode($agMap ?? []) !!};
let detalheAtual = null;
function abrirDetalhes(id) {
    const d = window.AG_MAP[id];
    if (!d) return;
    detalheAtual = d;
    document.getElementById('detTitulo').textContent = d.nome;
    document.getElementById('detSubtitulo').textContent = d.data_br + ' · ' + d.hora + ' · ' + d.status_label;
    document.getElementById('detTelefone').textContent = d.telefone || '—';
    document.getElementById('detServicos').textContent = d.servicos || '—';
    document.getElementById('detBarbeiro').textContent = d.barbeiro || '—';
    document.getElementById('detPlano').textContent = d.plano || '—';
    document.getElementById('detEditar').href = d.edit_url;
    document.getElementById('detPacote').href = d.pacote_url;
    document.getElementById('detVender').href = d.vender_url;
    document.getElementById('detWhatsapp').href = d.whatsapp_url;
    const params = new URLSearchParams(window.location.search);
    params.set('visao', 'dia'); params.set('data', d.data_iso);
    document.getElementById('detVerDia').href = window.location.pathname + '?' + params.toString();
    document.getElementById('detRealizar').style.display = d.pode_realizar ? '' : 'none';
    new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
}
function realizarDoDetalhe() {
    if (!detalheAtual) return;
    const m = bootstrap.Modal.getInstance(document.getElementById('modalDetalhes'));
    if (m) m.hide();
    const fakeBtn = document.createElement('button');
    fakeBtn.dataset.action = detalheAtual.realizar_url;
    fakeBtn.dataset.clienteId = detalheAtual.cliente_id;
    setTimeout(() => abrirModalRealizar(fakeBtn, detalheAtual.nome, detalheAtual.hora), 300);
}

document.getElementById('searchInput')?.addEventListener('input', aplicaFiltros);
document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
});
// rola automaticamente até o dia atual na semana
(function scrollToToday() {
    const today = document.querySelector('#view-semana .wk-day.today');
    if (today && currentVisao === 'semana') {
        setTimeout(() => today.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' }), 250);
    }
})();
(function initLista() { aplicaFiltros(); })();

/* ---- Realizar (reaproveitado) ---- */
let currentRealizarPlano = null;
function abrirModalRealizar(btn, nome, hora) {
    document.getElementById('realizarInfo').textContent = 'Realizar serviço de ' + nome + ' às ' + hora + '?';
    document.getElementById('formRealizar').action = btn.dataset.action;
    document.getElementById('realizarPlanoInfo').style.display='none';
    document.getElementById('realizarPlanoInfo').innerHTML='';
    document.getElementById('realizarUsarPlanoWrap').style.display='none';
    document.getElementById('realizarUsarPlano').checked=false;
    document.getElementById('realizarClientePlanoId').value='';
    currentRealizarPlano = null;
    const m = btn.dataset.action.match(/agendamentos\/(\d+)/);
    const agendamentoId = m ? m[1] : null;
    const tr = btn.closest?.('tr[data-status]');
    if (agendamentoId) {
        const info = (window.agPlanosMap && window.agPlanosMap[agendamentoId]) ? window.agPlanosMap[agendamentoId] : null;
        if (info && info.plano_id) { currentRealizarPlano = info; showRealizarPlano(info); }
        else if (tr?.dataset?.clienteId || btn.dataset.clienteId) fetchClientePlano(tr?.dataset?.clienteId || btn.dataset.clienteId).then(showRealizarPlano);
    }
    new bootstrap.Modal(document.getElementById('modalRealizar')).show();
}
function showRealizarPlano(info){
    if (!info || !info.plano_id) {
        document.getElementById('realizarPlanoInfo').style.display='none';
        document.getElementById('realizarUsarPlanoWrap').style.display='none';
        return;
    }
    currentRealizarPlano = info;
    const box = document.getElementById('realizarPlanoInfo');
    const wrap = document.getElementById('realizarUsarPlanoWrap');
    const venc = info.vencimento_br || info.vencimento || '-';
    const saldo = (info.total_restante ?? '-') + '/' + (info.total_contratada ?? '-');
    const pagoBadge = info.pago ? '<span class="badge bg-success">Pago</span>' : '<span class="badge bg-warning text-dark">Pagamento pendente</span>';
    const expBadge = info.expirado ? '<span class="badge bg-danger">VENCIDO</span>' : '<span class="badge bg-success">'+(info.dias_para_vencer ?? '?')+' dias restantes</span>';
    const quotasHtml = (info.quotas||[]).map(q=> `${q.servico_nome}: <b>${q.usada}/${q.quantidade}</b> (${q.restante} rest.)`).join('<br>');
    let servicosHtml = '';
    let excedenteHtml = '';
    let valorExcedente = info.valor_excedente ?? 0;
    let valorTotal = info.valor_total ?? 0;
    let valorCoberto = info.valor_coberto ?? 0;
    if (info.servicos_status && info.servicos_status.length) {
        servicosHtml = '<div style="margin-top:8px;padding-top:8px;border-top:1px solid #dee2e6"><strong>Serviços deste agendamento:</strong><br>' +
            info.servicos_status.map(s=>{
                const badge = s.dentro ? '<span class="badge bg-success">dentro</span>' : '<span class="badge bg-danger">FORA</span>';
                const preco = parseFloat(s.preco).toFixed(2).replace('.', ',');
                return `· ${s.servico_nome} (R$ ${preco}) ${badge} <small class="text-muted">- ${s.motivo}${s.dentro?' ('+s.restante+' restantes)':''}</small>`;
            }).join('<br>') + '</div>';
        if (valorExcedente > 0) excedenteHtml = `<div class="alert alert-warning py-2 small mb-0 mt-2">⚠️ <strong>Excedente: R$ ${parseFloat(valorExcedente).toFixed(2).replace('.', ',')}</strong> — será cobrado no Caixa.</div>`;
        else if (info.dentro) excedenteHtml = `<div class="alert alert-success py-2 small mb-0 mt-2">✅ Tudo dentro da cota — <strong>sem cobrança</strong>.</div>`;
        else if (info.expirado) excedenteHtml = `<div class="alert alert-danger py-2 small mb-0 mt-2">❌ Plano vencido — todos os serviços serão cobrados (R$ ${parseFloat(valorTotal).toFixed(2).replace('.',',')})</div>`;
        else if (!info.pago) excedenteHtml = `<div class="alert alert-danger py-2 small mb-0 mt-2">❌ Plano com pagamento pendente — todos os serviços serão cobrados.</div>`;
    }
    box.innerHTML = `<strong>Plano:</strong> ${info.plano_nome} · <strong>Saldo:</strong> ${saldo} · <strong>Validade:</strong> ${venc} ${expBadge} · ${pagoBadge}<br><small>${quotasHtml}</small>` + servicosHtml + excedenteHtml;
    box.style.display='block';
    wrap.style.display='block';
    document.getElementById('realizarClientePlanoId').value = info.id;
    if (info.dentro && !info.expirado && info.pago) {
        document.getElementById('realizarUsarPlano').checked = true;
        document.getElementById('realizarForma').value = 'Plano';
    } else if (!info.dentro && valorExcedente>0 && !info.expirado && info.pago) {
        document.getElementById('realizarUsarPlano').checked = true;
        document.getElementById('realizarForma').value = 'Plano';
    }
}
document.getElementById('realizarForma')?.addEventListener('change', function(){
    if (this.value==='Plano' && currentRealizarPlano) document.getElementById('realizarUsarPlano').checked = true;
});

const themeToggle = document.getElementById('themeToggle');
const htmlEl = document.documentElement;
themeToggle?.addEventListener('click', function() {
    const isDark = htmlEl.getAttribute('data-bs-theme') === 'dark';
    htmlEl.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
    this.innerHTML = isDark ? '<svg class="icon"><use href="#i-moon"/></svg>' : '<svg class="icon"><use href="#i-sun"/></svg>';
});

const horariosUrl = '{{ $slug ? route("tenant.admin.agendamentos.horarios", $slug) : route("admin.agendamentos.horarios") }}';
function getServicosSelecionados() {
    const vals = [];
    document.querySelectorAll('.servico-check:checked').forEach(el => vals.push(el.value));
    return vals;
}
function carregarHorarios() {
    const barbeiroId = document.getElementById('barbeiroSelect')?.value;
    const data = document.getElementById('dataAgendamento')?.value;
    const select = document.getElementById('horarioSelect');
    if (!barbeiroId || !data) {
        if (select) select.innerHTML = '<option value="">Selecione barbeiro e data primeiro</option>';
        return;
    }
    if (select) select.innerHTML = '<option value="">Carregando...</option>';
    const servicos = getServicosSelecionados();
    const encaixe = document.getElementById('encaixeFlag')?.checked || false;
    const permitirPassado = document.getElementById('permitirPassado')?.checked || false;
    const qs = new URLSearchParams();
    qs.set('barbeiro_id', barbeiroId);
    qs.set('data', data);
    servicos.forEach(id => qs.append('servico_ids[]', id));
    if (encaixe) qs.set('encaixe', 1);
    if (permitirPassado) qs.set('permitir_passado', 1);
    fetch(horariosUrl + '?' + qs.toString(), { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            if (!Array.isArray(res)) res = [];
            if (res.length === 0) {
                select.innerHTML = '<option value="">Nenhum horário disponível</option>';
                document.getElementById('horarioHelp').textContent = 'Nenhum horário para este barbeiro no dia selecionado.';
                document.getElementById('horarioHelp').style.color = 'var(--danger)';
            } else {
                select.innerHTML = '<option value="">Selecione...</option>';
                res.forEach(function(h) {
                    const opt = document.createElement('option');
                    opt.value = h; opt.textContent = h;
                    select.appendChild(opt);
                });
                document.getElementById('horarioHelp').style.color = 'var(--text-muted)';
            }
        })
        .catch(() => { select.innerHTML = '<option value="">Erro ao carregar horários</option>'; });
}
document.getElementById('barbeiroSelect')?.addEventListener('change', carregarHorarios);
document.getElementById('dataAgendamento')?.addEventListener('change', carregarHorarios);
document.querySelectorAll('.servico-check').forEach(el => el.addEventListener('change', carregarHorarios));
document.getElementById('encaixeFlag')?.addEventListener('change', function() {
    document.getElementById('horaFimEncaixeWrap').style.display = this.checked ? 'block' : 'none';
    carregarHorarios();
});
document.getElementById('permitirPassado')?.addEventListener('change', carregarHorarios);
document.getElementById('modalNovoAgendamento')?.addEventListener('shown.bs.modal', carregarHorarios);
document.getElementById('formNovoAgendamento')?.addEventListener('submit', function(e) {
    const clienteId = document.querySelector('input[name="cliente_id"]')?.value?.trim();
    const nomeManual = document.getElementById('clienteNomeManual')?.value?.trim();
    if (!clienteId && !nomeManual) {
        e.preventDefault();
        Swal.fire({title:'Informe o cliente', text:'Selecione um existente ou digite o nome.', icon:'warning'});
        return false;
    }
    if (getServicosSelecionados().length===0) {
        e.preventDefault();
        Swal.fire({title:'Selecione o serviço', text:'Escolha ao menos um serviço.', icon:'warning'});
        return false;
    }
    if (!document.getElementById('horarioSelect')?.value) {
        e.preventDefault();
        Swal.fire({title:'Selecione o horário', text:'Escolha um horário disponível.', icon:'warning'});
        return false;
    }
});
window.agPlanosMap = {!! json_encode($agPlanosMap ?? []) !!};
const clientePlanoInfoUrlTemplate = '{{ $slug ? route("tenant.admin.clientes-planos.cliente-plano-info", [$slug, "__ID__"]) : route("admin.clientes-planos.cliente-plano-info", "__ID__") }}';
async function fetchClientePlano(clienteId){
    if(!clienteId) return null;
    try{
        const url = clientePlanoInfoUrlTemplate.replace('__ID__', clienteId);
        const r = await fetch(url, {headers:{'Accept':'application/json'}});
        if(!r.ok) return null;
        const j = await r.json();
        return (j.planos && j.planos[0]) ? j.planos[0] : null;
    }catch(e){ return null; }
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function(){
    try { new bootstrap.Modal(document.getElementById('modalNovoAgendamento')).show(); } catch(e){}
});
@endif
</script>
@endpush
