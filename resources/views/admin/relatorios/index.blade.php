@extends('layouts.app')

@php
    if (!function_exists('relRoute')) {
        function relRoute($name, $params = []) {
            if (request()->route('barbearia')) {
                return route('tenant.admin.' . $name, array_merge([request()->route('barbearia')->slug], $params));
            }
            return route('admin.' . $name, $params);
        }
    }
@endphp

@section('title', 'Relatório Financeiro')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-chart"/></svg>
    <span class="sep">/</span>
    <span>Barber Control</span>
    <span class="sep">/</span>
    <span class="current">Relatório Financeiro</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Entradas e saídas</span>
    <span class="pipe">·</span>
    <span>{{ \Carbon\Carbon::create($ano, $mes, 1)->translatedFormat('F \d\e Y') }}</span>
    <span class="pipe">·</span>
    <span>{{ $movimentacoes->count() }} movimentações</span>
@endsection

@section('topbar-actions')
    <div class="status-toggle" style="display:flex;gap:3px;padding:4px;background:var(--bg);border:1px solid var(--border);border-radius:12px;">
        <a href="{{ relRoute('relatorios.index') }}" class="status-toggle-btn {{ !$tipo ? 'active' : '' }}" style="text-decoration:none;">Todas</a>
        <a href="{{ relRoute('relatorios.index', array_merge(['tipo' => 'entrada'], request()->except('tipo', 'page'))) }}" class="status-toggle-btn {{ $tipo === 'entrada' ? 'active' : '' }}" style="text-decoration:none;">Entradas</a>
        <a href="{{ relRoute('relatorios.index', array_merge(['tipo' => 'saida'], request()->except('tipo', 'page'))) }}" class="status-toggle-btn {{ $tipo === 'saida' ? 'active' : '' }}" style="text-decoration:none;">Saídas</a>
    </div>
    <a href="{{ relRoute('relatorios.faturamento-pdf', request()->query()) }}" class="btn-primary-c" style="height:44px;padding:0 18px;border-radius:12px;background:var(--accent);color:#0d0d12;border:none;font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
        <svg class="icon icon-sm"><use href="#i-download"/></svg>
        Gerar PDF
    </a>
@endsection

@section('content')
@push('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2.5rem; }
.stat-card { background: var(--card-solid); border: 1px solid var(--border); border-radius: 20px; padding: 1.75rem; position: relative; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.stat-card:hover { border-color: var(--border-strong); transform: translateY(-4px); }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.4rem; }
.stat-icon { width: 52px; height: 52px; border-radius: 14px; display: grid; place-items: center; transition: transform 0.3s ease; }
.stat-card:hover .stat-icon { transform: scale(1.08) rotate(-5deg); }
.stat-icon.green { background: var(--success-bg); color: var(--success); }
.stat-icon.red { background: var(--danger-bg); color: var(--danger); }
.stat-icon.amber { background: var(--accent-glow); color: var(--accent); }
.stat-icon.blue { background: var(--info-bg); color: var(--info); }
.stat-label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
.stat-value { font-size: 2.4rem; font-weight: 800; letter-spacing: -1.5px; line-height: 1; }
.stat-value .cur { font-size: 1rem; font-weight: 700; letter-spacing: 0; }
.stat-sub { font-size: 0.78rem; color: var(--text-faint); margin-top: 10px; font-weight: 500; }
.stat-sub strong { color: var(--text); }
.stat-value.pos { color: var(--success); }
.stat-value.neg { color: var(--danger); }

.panel { background: var(--card-solid); border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }
.panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }
.panel-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.panel-body { padding: 1.5rem 2rem; }

.filter-bar { padding: 1rem 2rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: var(--bg-input); }
.filter-bar select, .filter-bar input { height: 36px; padding: 0 12px; border-radius: 10px; border: 1px solid var(--border-strong); background: var(--card-solid); color: var(--text); font-family: inherit; font-size: 13px; }
.filter-bar .lbl { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
.filter-spacer { flex: 1; }
.filter-btn { height: 36px; padding: 0 16px; border-radius: 10px; background: var(--accent); color: #0d0d12; border: none; font-weight: 700; font-size: 13px; font-family: inherit; cursor: pointer; transition: all 180ms; }
.filter-btn:hover { background: var(--accent-hover); }

.table-wrap { overflow-x: auto; padding: 0 1.5rem; }
.fin-table { width: 100%; border-collapse: separate; border-spacing: 0 8px; min-width: 860px; }
.fin-table thead th { text-align: center; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); padding: 0 22px 8px; white-space: nowrap; }
.fin-table tbody td { padding: 0.9rem 22px; font-size: 14px; vertical-align: middle; }
.fin-table tbody tr { background: var(--bg-input); transition: all 0.2s ease; }
.fin-table tbody tr:hover { background: var(--card-hover); transform: translateX(4px); }
.fin-table tbody td:first-child { border-radius: 14px 0 0 14px; }
.fin-table tbody td:last-child { border-radius: 0 14px 14px 0; }

.tipo-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
.tipo-badge.entrada { background: var(--success-bg); color: var(--success); }
.tipo-badge.saida { background: var(--danger-bg); color: var(--danger); }
.tipo-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

.val { font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }
.val.entrada { color: var(--success); }
.val.saida { color: var(--danger); }
.cell-muted { font-size: 12.5px; color: var(--text-muted); }
.orig-chip { display: inline-block; padding: 3px 9px; border-radius: 8px; font-size: 11px; font-weight: 700; background: var(--accent-glow); color: var(--accent); margin-right: 6px; }

.status-toggle-btn { padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--text-muted); background: transparent; border: none; cursor: pointer; transition: all 180ms; display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; font-family: inherit; }
.status-toggle-btn:hover { color: var(--text); }
.status-toggle-btn.active { background: var(--card-solid); color: var(--text); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }

.days-chart { display: flex; align-items: flex-end; gap: 3px; height: 130px; padding-top: 10px; }
.day-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; min-width: 0; }
.day-bars { display: flex; gap: 2px; width: 100%; align-items: flex-end; flex: 1; }
.day-bar { flex: 1; border-radius: 3px 3px 0 0; min-height: 2px; }
.day-bar.in { background: var(--success); }
.day-bar.out { background: var(--danger); }
.day-num { font-size: 9.5px; color: var(--text-faint); font-weight: 600; }

.summary-box { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid var(--border); }
.summary-box:last-child { border-bottom: none; }
.summary-box .ic { width: 40px; height: 40px; border-radius: 11px; display: grid; place-items: center; flex-shrink: 0; }
.summary-box .l { font-size: 12px; color: var(--text-muted); font-weight: 600; }
.summary-box .v { font-size: 17px; font-weight: 800; letter-spacing: -0.02em; }
.rank-row { display: flex; align-items: center; gap: 10px; padding: 9px 0; }
.rank-row .nm { flex: 1; font-size: 13.5px; font-weight: 600; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.rank-row .vl { font-size: 13.5px; font-weight: 800; font-variant-numeric: tabular-nums; }

.empty-state { text-align: center; padding: 40px 22px; color: var(--text-muted); font-size: 14px; }

@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .table-wrap { padding: 0 1rem; } .fin-table thead { display: none; } .fin-table, .fin-table tbody, .fin-table tr, .fin-table td { display: block; width: 100%; } .fin-table tr { padding: 14px 16px; border: 1px solid var(--border); border-radius: 16px; margin-bottom: 10px; background: var(--bg-input); } .fin-table tbody td { padding: 5px 0; border: none; display: flex; justify-content: space-between; align-items: center; } .fin-table tbody td::before { content: attr(data-label); font-size: 11px; font-weight: 700; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; margin-right: 12px; flex-shrink: 0; } }
</style>
@endpush

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top"><div class="stat-icon green"><svg class="icon"><use href="#i-arrow-down"/></svg></div></div>
        <div class="stat-label">Entradas</div>
        <div class="stat-value pos"><span class="cur">R$</span> {{ number_format($totalEntradas, 2, ',', '.') }}</div>
        <div class="stat-sub"><strong>{{ $totalAtendimentos }}</strong> atendimentos · <strong>{{ $totalVendas }}</strong> vendas</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top"><div class="stat-icon red"><svg class="icon"><use href="#i-arrow-up"/></svg></div></div>
        <div class="stat-label">Saídas</div>
        <div class="stat-value neg"><span class="cur">R$</span> {{ number_format($totalSaidas, 2, ',', '.') }}</div>
        <div class="stat-sub"><strong>{{ $saidas->count() }}</strong> despesas pagas</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top"><div class="stat-icon amber"><svg class="icon"><use href="#i-wallet"/></svg></div></div>
        <div class="stat-label">Saldo do mês</div>
        <div class="stat-value {{ $saldo >= 0 ? 'pos' : 'neg' }}"><span class="cur">R$</span> {{ number_format($saldo, 2, ',', '.') }}</div>
        <div class="stat-sub">entradas − saídas</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top"><div class="stat-icon blue"><svg class="icon"><use href="#i-chart"/></svg></div></div>
        <div class="stat-label">Ticket médio</div>
        <div class="stat-value"><span class="cur">R$</span> {{ number_format($ticketMedio, 2, ',', '.') }}</div>
        <div class="stat-sub">por atendimento realizado</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-chart"/></svg></div>
            <div>
                <h2 class="panel-title">Movimentações de {{ \Carbon\Carbon::create($ano, $mes, 1)->translatedFormat('F \d\e Y') }}</h2>
                <div class="panel-subtitle">Entradas e saídas registradas no caixa</div>
            </div>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <span class="lbl">Mês</span>
        <select name="mes" onchange="this.form.submit()">
            @foreach($mesesList as $m)
            <option value="{{ $m }}" {{ $mes === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
            @endforeach
        </select>
        <select name="ano" onchange="this.form.submit()">
            @foreach($anosList as $a)
            <option value="{{ $a }}" {{ $ano === $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        @if(!request()->route('barbearia'))
        <span class="lbl" style="margin-left:10px;">Unidade</span>
        <select name="barbearia_id" onchange="this.form.submit()">
            <option value="">Todas</option>
            @foreach($barbearias as $b)
            <option value="{{ $b->id }}" {{ $barbeariaId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
            @endforeach
        </select>
        @endif
        <input type="hidden" name="tipo" value="{{ $tipo }}">
        <div class="filter-spacer"></div>
        <button type="submit" class="filter-btn">Filtrar</button>
    </form>

    <div class="table-wrap">
        <table class="fin-table">
            <thead>
                <tr>
                    <th style="width:110px">Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Origem</th>
                    <th style="width:130px">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimentacoes as $m)
                @php
                    $origem = $m->origem;
                    $origemNome = '';
                    if ($m->origem_type === App\Models\Agendamento::class) {
                        $origemNome = 'Serviço';
                    } elseif ($m->origem_type === App\Models\Venda::class) {
                        $origemNome = 'Venda';
                    } elseif ($m->origem_type === App\Models\Despesa::class) {
                        $origemNome = 'Despesa';
                    }
                    $detalhe = $m->descricao;
                    if ($origem) {
                        if ($m->origem_type === App\Models\Agendamento::class) {
                            $detalhe = ($origem->barbeiro?->nome ?? '—') . ' · ' . ($origem->cliente?->nome ?? '—');
                        } elseif ($m->origem_type === App\Models\Venda::class) {
                            $detalhe = ($origem->cliente?->nome ?? '—') . ' · ' . $origem->produtos->count() . ' produto(s)';
                        } elseif ($m->origem_type === App\Models\Despesa::class) {
                            $detalhe = ($origem->categoria ?? '') . ($origem->categoria ? ' · ' : '') . $origem->descricao;
                        }
                    }
                @endphp
                <tr>
                    <td data-label="Data" style="font-weight:600;white-space:nowrap;">{{ $m->created_at->format('d/m/Y') }} <span class="cell-muted">{{ $m->created_at->format('H:i') }}</span></td>
                    <td data-label="Tipo"><span class="tipo-badge {{ $m->tipo }}">{{ $m->tipo === 'entrada' ? 'Entrada' : 'Saída' }}</span></td>
                    <td data-label="Descrição">{{ $detalhe }}</td>
                    <td data-label="Origem"><span class="orig-chip">{{ $origemNome }}</span></td>
                    <td data-label="Valor" style="text-align:right;"><span class="val {{ $m->tipo }}">{{ $m->tipo === 'entrada' ? '+' : '−' }} R$ {{ number_format($m->valor, 2, ',', '.') }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">Nenhuma movimentação encontrada para este mês.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="panel-body" style="display:flex;gap:40px;flex-wrap:wrap;padding-top:1.2rem;border-top:1px solid var(--border);">
        <div style="font-size:13.5px;color:var(--text-muted);">
            Total entradas: <strong style="color:var(--success);">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</strong>
            &nbsp;·&nbsp; Total saídas: <strong style="color:var(--danger);">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</strong>
            &nbsp;·&nbsp; Saldo: <strong style="color:var(--text);">R$ {{ number_format($saldo, 2, ',', '.') }}</strong>
        </div>
    </div>
</section>

<section class="stats-grid" style="grid-template-columns:repeat(2,1fr);margin-top:1.5rem;">
    <div class="panel fade-in d6">
        <div class="panel-header">
            <div class="panel-title-wrap">
                <div class="panel-title-icon"><svg class="icon"><use href="#i-activity"/></svg></div>
                <div>
                    <h2 class="panel-title">Movimentações por dia</h2>
                    <div class="panel-subtitle">Entradas (verde) e saídas (vermelho)</div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            @if($porDia->count())
            @php
                $maxDia = max($porDia->max('entradas'), $porDia->max('saidas'), 1);
                $diasNoMes = \Carbon\Carbon::create($ano, $mes, 1)->daysInMonth;
            @endphp
            <div class="days-chart">
                @for($d = 1; $d <= $diasNoMes; $d++)
                @php $info = $porDia->get($d, ['entradas' => 0, 'saidas' => 0]); @endphp
                <div class="day-col" title="{{ $d }}/{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}: +R$ {{ number_format($info['entradas'], 2, ',', '.') }} / −R$ {{ number_format($info['saidas'], 2, ',', '.') }}">
                    <div class="day-bars">
                        <div class="day-bar in" style="height: {{ max(($info['entradas'] / $maxDia) * 100, 2) }}%"></div>
                        <div class="day-bar out" style="height: {{ max(($info['saidas'] / $maxDia) * 100, 2) }}%"></div>
                    </div>
                    <div class="day-num">{{ $d }}</div>
                </div>
                @endfor
            </div>
            @else
            <div class="empty-state">Sem dados para exibir.</div>
            @endif
        </div>
    </div>

    <div class="col-stack" style="display:flex;flex-direction:column;gap:1.5rem;">
        <div class="panel fade-in d7">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon" style="background:var(--success-bg);color:var(--success)"><svg class="icon"><use href="#i-scissor"/></svg></div>
                    <div>
                        <h2 class="panel-title">Faturamento por barbeiro</h2>
                        <div class="panel-subtitle">Ranking de atendimentos do mês</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @forelse($porBarbeiro as $nome => $dados)
                <div class="rank-row">
                    <span class="nm">{{ $nome }}</span>
                    <span class="vl" style="color:var(--text-muted);font-size:12px;">{{ $dados['quantidade'] }} atend.</span>
                    <span class="vl">R$ {{ number_format($dados['total'], 2, ',', '.') }}</span>
                </div>
                @empty
                <div class="empty-state">Sem atendimentos no mês.</div>
                @endforelse
            </div>
        </div>

        <div class="panel fade-in d8">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon" style="background:var(--danger-bg);color:var(--danger)"><svg class="icon"><use href="#i-wallet"/></svg></div>
                    <div>
                        <h2 class="panel-title">Saídas por categoria</h2>
                        <div class="panel-subtitle">Despesas do mês</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @forelse($porCategoriaSaida as $cat => $valor)
                <div class="rank-row">
                    <span class="nm">{{ $cat }}</span>
                    <span class="vl" style="color:var(--danger);">R$ {{ number_format($valor, 2, ',', '.') }}</span>
                </div>
                @empty
                <div class="empty-state">Sem despesas no mês.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection