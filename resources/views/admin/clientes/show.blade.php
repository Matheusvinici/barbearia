@extends('layouts.app')

@php
$slug = request()->route('barbearia')?->slug;
$vendaCreateUrl = $slug
    ? route('tenant.admin.vendas.create', [$slug, 'cliente_id' => $cliente->id])
    : route('admin.vendas.create', ['cliente_id' => $cliente->id]);
$clienteEditUrl = $slug
    ? route('tenant.admin.clientes.edit', [$slug, $cliente])
    : route('admin.clientes.edit', $cliente);
$vendaStatusMap = ['pendente' => 'amber', 'finalizada' => 'green', 'cancelada' => 'red'];
$vendaStatusLabel = ['pendente' => 'Pendente', 'finalizada' => 'Finalizada', 'cancelada' => 'Cancelada'];
@endphp

@extends('layouts.app')

@section('title', $cliente->nome)

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-people"/></svg>
    <span class="sep">/</span>
    <span>Clientes</span>
    <span class="sep">/</span>
    <span class="current">{{ $cliente->nome }}</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>{{ $cliente->agendamentos->count() }} agendamentos</span>
    <span class="pipe">·</span>
    <span>@if($comandaAberta > 0) comanda aberta de <strong style="color:var(--accent)">R$ {{ number_format($comandaAberta, 2, ',', '.') }}</strong>@else sem comanda aberta @endif</span>
@endsection

@section('topbar-actions')
    <a href="{{ $vendaCreateUrl }}" class="btn-primary-c">
        <svg class="icon icon-sm"><use href="#i-plus"/></svg>
        Nova Venda
    </a>
    <a href="{{ $clienteEditUrl }}" class="btn-ghost-c">
        <svg class="icon icon-sm"><use href="#i-edit"/></svg>
        Editar
    </a>
@endsection

@section('content')
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-people" viewBox="0 0 24 24" fill="none"><path d="M16.67 20.17v-1.5c0-2.07-1.68-3.75-3.75-3.75H5.42c-2.07 0-3.75 1.68-3.75 3.75v1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9.17" cy="7.42" r="3.75" stroke="currentColor" stroke-width="1.6"/><path d="M22 20.17v-1.5c0-1.69-1.13-3.12-2.67-3.58M15.42 4.05a3.74 3.74 0 0 1 0 6.74" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24" fill="none"><path d="M6 12h12M12 6v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24" fill="none"><path d="M13.26 3.6l-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.98 1.87 1.8l3.22-.55c.45-.08 1.08-.41 1.39-.75L18.86 8.6c.75-.81.8-2.01-.02-2.79l-1.6-1.54c-.83-.79-2.16-.76-2.98.08z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.47 5.08l3.43 3.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-call" viewBox="0 0 24 24" fill="none"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-mail" viewBox="0 0 24 24" fill="none"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-receipt" viewBox="0 0 24 24" fill="none"><path d="M4 5c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2v15.5l-2.5-1.5L15 20.5 12.5 19 10 20.5 7.5 19 5 20.5 4 19.5V5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8 8h8M8 11.5h8M8 15h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M16.5 13.5h.01M7.5 13.5h4.49" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
    </defs>
</svg>

<div class="profile-header fade-in d1">
    <div class="cover"></div>
    <div class="header-body">
        <div class="profile-photo">{{ mb_strtoupper(mb_substr($cliente->nome, 0, 1)) }}</div>
        <div class="profile-details">
            <h2>{{ $cliente->nome }}</h2>
            <div class="role">
                @if($cliente->planos->where('ativo', true)->count())
                <span class="badge-c gold" style="margin-right:6px;"><svg class="icon icon-xs"><use href="#i-receipt"/></svg> Plano ativo</span>
                @endif
                Cliente
            </div>
            <div class="joined">
                <svg class="icon icon-sm"><use href="#i-call"/></svg>{{ $cliente->telefone }}
                @if($cliente->email)<span style="display:inline-flex;align-items:center;gap:6px;margin-left:14px;"><svg class="icon icon-sm"><use href="#i-mail"/></svg>{{ $cliente->email }}</span>@endif
                <span style="display:inline-flex;align-items:center;gap:6px;margin-left:14px;"><svg class="icon icon-sm"><use href="#i-calendar"/></svg>Desde {{ $cliente->created_at?->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>

@if($cliente->observacoes)
<div class="panel fade-in d2" style="margin-bottom:22px;">
    <div class="panel-body" style="padding:16px 24px;">
        <span class="cell-muted" style="font-size:13px;">Observações: <strong style="color:var(--text);">{{ $cliente->observacoes }}</strong></span>
    </div>
</div>
@endif

<div class="row g-4 fade-in d3" style="margin:0;">
    <div class="col-lg-6" style="padding:0 10px;">
        <div class="panel h-100">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-clock"/></svg></div>
                    <div>
                        <h2 class="panel-title">Histórico de agendamentos</h2>
                        <div class="panel-subtitle">{{ $cliente->agendamentos->count() }} atendimento(s)</div>
                    </div>
                </div>
            </div>
            <div class="panel-body" style="padding:8px 0;">
                @forelse($cliente->agendamentos as $ag)
                <div class="upcoming-item">
                    <div class="up-time">
                        <span class="h">{{ $ag->hora_inicio instanceof \Carbon\Carbon ? $ag->hora_inicio->format('H:i') : $ag->hora_inicio }}</span>
                        <span class="d">{{ $ag->data->format('d/m') }}</span>
                    </div>
                    <div class="up-divider"></div>
                    <div class="up-info">
                        <div class="n">{{ $ag->barbeiro->nome }}</div>
                        <div class="m">{{ $ag->servicos->pluck('nome')->implode(', ') }}</div>
                    </div>
                    <span class="up-status {{ $ag->status }}">{{ ucfirst($ag->status) }}</span>
                </div>
                @empty
                <div class="empty-state">
                    <div class="icon-wrap"><svg class="icon"><use href="#i-calendar"/></svg></div>
                    <h4>Nenhum agendamento</h4>
                    <p>Este cliente ainda não possui atendimentos registrados.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6" style="padding:0 10px;">
        <div class="panel h-100">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-receipt"/></svg></div>
                    <div>
                        <h2 class="panel-title">Comanda de vendas</h2>
                        <div class="panel-subtitle">produtos comprados pelo cliente</div>
                    </div>
                </div>
                @if($comandaAberta > 0)
                <span class="badge-c amber" style="font-size:13px;">Comanda aberta: R$ {{ number_format($comandaAberta, 2, ',', '.') }}</span>
                @endif
            </div>
            <div class="panel-body" style="padding:8px 0;">
                @forelse($vendas as $venda)
                <div class="upcoming-item">
                    <div class="up-time">
                        <span class="h" style="font-size:13px;">#{{ $venda->id }}</span>
                        <span class="d">{{ $venda->created_at->format('d/m') }}</span>
                    </div>
                    <div class="up-divider"></div>
                    <div class="up-info">
                        <div class="n" style="display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach($venda->produtos as $p)
                            <span class="badge-c gray" style="font-size:10.5px;">{{ $p->nome }} <small>×{{ $p->pivot->quantidade }}</small></span>
                            @endforeach
                        </div>
                        <div class="m">{{ $venda->forma_pagamento ?: 'Pagamento não informado' }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-weight:800;font-size:14px;">R$ {{ number_format($venda->total, 2, ',', '.') }}</div>
                        <span class="badge-c {{ $vendaStatusMap[$venda->status] }}" style="margin-top:4px;">{{ $vendaStatusLabel[$venda->status] }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="icon-wrap"><svg class="icon"><use href="#i-receipt"/></svg></div>
                    <h4>Nenhuma venda</h4>
                    <p>Cliente ainda não comprou produtos na barbearia.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-header { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-xl); overflow: hidden; margin-bottom: 22px; }
.cover { height: 140px; background: linear-gradient(135deg, var(--bg-elevated) 0%, #2a1a05 100%); position: relative; overflow: hidden; }
.cover::after { content: ''; position: absolute; top: -50%; right: -20%; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, var(--accent-glow), transparent 60%); pointer-events: none; }
.header-body { padding: 0 32px 24px; display: flex; align-items: flex-end; gap: 24px; flex-wrap: wrap; position: relative; border-bottom: 1px solid var(--border); }
.profile-photo { width: 84px; height: 84px; border-radius: 50%; background: linear-gradient(135deg, #f87171, #f5b544); display: grid; place-items: center; font-weight: 800; font-size: 28px; color: white; flex-shrink: 0; margin-top: -32px; border: 4px solid var(--card); }
.profile-details { flex: 1; min-width: 200px; }
.profile-details h2 { font-size: 22px; font-weight: 800; margin: 0; letter-spacing: -0.02em; }
.profile-details .role { font-size: 13px; color: var(--text-muted); margin-top: 4px; display: flex; align-items: center; gap: 6px; }
.profile-details .joined { font-size: 12.5px; color: var(--text-faint); margin-top: 8px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 16px; font-weight: 700; margin: 0; letter-spacing: -0.015em; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }
.upcoming-item { display: flex; align-items: center; gap: 14px; padding: 14px 24px; border-bottom: 1px solid var(--border); transition: background 150ms; }
.upcoming-item:last-child { border-bottom: none; }
.upcoming-item:hover { background: var(--border); }
.up-time { display: flex; flex-direction: column; align-items: center; min-width: 52px; flex-shrink: 0; }
.up-time .h { font-size: 16px; font-weight: 700; line-height: 1.2; }
.up-time .d { font-size: 10.5px; color: var(--text-faint); font-weight: 600; margin-top: 2px; }
.up-divider { width: 2px; height: 36px; border-radius: 2px; background: var(--border-strong); flex-shrink: 0; }
.up-info { flex: 1; min-width: 0; }
.up-info .n { font-size: 14px; font-weight: 600; }
.up-info .m { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
.up-status { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; padding: 4px 10px; border-radius: 999px; flex-shrink: 0; }
.up-status.pendente { background: var(--accent-glow); color: var(--accent); }
.up-status.confirmado { background: var(--info-bg); color: var(--info); }
.up-status.realizado { background: var(--success-bg); color: var(--success); }
.up-status.cancelado { background: var(--danger-bg); color: var(--danger); }
.up-status.ausente { background: var(--pink-bg); color: var(--pink); }
.empty-state { text-align: center; padding: 48px 24px; }
.empty-state .icon-wrap { width: 56px; height: 56px; border-radius: 16px; background: var(--border); display: grid; place-items: center; margin: 0 auto 14px; }
.empty-state .icon-wrap .icon { width: 26px; height: 26px; color: var(--text-faint); }
.empty-state h4 { font-size: 15px; font-weight: 700; margin: 0 0 6px; }
.empty-state p { font-size: 13px; color: var(--text-muted); margin: 0; }
.badge-c { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
.badge-c.green { background: var(--success-bg); color: var(--success); }
.badge-c.amber { background: var(--accent-glow); color: var(--accent); }
.badge-c.red { background: var(--danger-bg); color: var(--danger); }
.badge-c.gray { background: var(--border); color: var(--text-muted); }
.badge-c.gold { background: var(--accent-glow); color: var(--accent-soft); }
.fade-in { animation: fadeInUp 400ms ease both; }
.d1 { animation-delay: 50ms; } .d2 { animation-delay: 100ms; } .d3 { animation-delay: 150ms; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush