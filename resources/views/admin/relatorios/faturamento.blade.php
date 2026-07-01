@extends('layouts.app')
@section('title', 'Faturamento')
@section('breadcrumb')
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.index', optional(request()->route('barbearia'))?->slug) : route('admin.relatorios.index') }}" style="color:inherit; text-decoration:none;">Relatórios</a>
    <span class="sep">/</span>
    <span class="current">Faturamento</span>
@endsection
@section('subtitle')
    <span class="live-dot"></span>
    <span>Dados atualizados</span>
    <span class="pipe">·</span>
    <span>Período: {{ \Carbon\Carbon::parse($dataInicio ?? now()->startOfMonth())->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim ?? now()->endOfMonth())->format('d/m/Y') }}</span>
@endsection
@section('topbar-actions')
    <div class="period-switch">
        <button class="period-btn {{ ($periodo ?? 'mensal') === 'mensal' ? 'active' : '' }}" data-period="mensal">Mês</button>
        <button class="period-btn {{ ($periodo ?? '') === 'trimestre' ? 'active' : '' }}" data-period="trimestre">Trimestre</button>
        <button class="period-btn {{ ($periodo ?? '') === 'anual' ? 'active' : '' }}" data-period="anual">Ano</button>
    </div>
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.faturamento-pdf', array_merge([optional(request()->route('barbearia'))?->slug], request()->query())) : route('admin.relatorios.faturamento-pdf', request()->query()) }}" class="btn-primary-c">
        <svg class="icon icon-sm"><use href="#i-download"/></svg>Exportar PDF
    </a>
@endsection

@section('content')
<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-coins"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>{{ number_format($crescimento ?? 0, 1) }}%</span>
        </div>
        <div class="stat-label">Total do período</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($totalPeriodo ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">faturamento bruto</div>
    </div>
    <div class="stat-card green fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-trend-up"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>{{ number_format($crescimento ?? 0, 1) }}%</span>
        </div>
        <div class="stat-label">Média mensal</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($mediaMensal ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">média do período</div>
    </div>
    <div class="stat-card blue fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-medal"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>melhor mês</span>
        </div>
        <div class="stat-label">Melhor mês</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($melhorMesValor ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">{{ $melhorMesNome ?? '—' }}</div>
    </div>
    <div class="stat-card purple fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-fire"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>{{ number_format($crescimento ?? 0, 1) }}%</span>
        </div>
        <div class="stat-label">Crescimento</div>
        <div class="stat-value">{{ number_format($crescimento ?? 0, 1) }}%</div>
        <div class="stat-sub">vs. período anterior</div>
    </div>
</section>

<section class="main-grid">
    <div class="col-stack">
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-trend-up"/></svg></div>
                    <div>
                        <h2 class="panel-title">Evolução da receita</h2>
                        <div class="panel-subtitle">Faturamento mensal</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="revenue-chart" id="revenueChart">
                    @if(isset($meses) && count($meses) > 0)
                        @php $maxRev = max($meses->pluck('receita')->toArray()); @endphp
                        @foreach($meses as $m)
                            @php $alt = $maxRev > 0 ? ($m['receita'] / $maxRev) * 100 : 0; @endphp
                            <div class="chart-bar" style="left: {{ ($loop->index / max(count($meses) - 1, 1)) * 100 }}%; width: {{ 100 / count($meses) - 4 }}%; height: {{ max($alt, 2) }}%;" title="{{ $m['nome'] }}: R$ {{ number_format($m['receita'], 2, ',', '.') }}"></div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="panel fade-in d6">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-chart"/></svg></div>
                    <div>
                        <h2 class="panel-title">Detalhamento mensal</h2>
                        <div class="panel-subtitle">Período selecionado</div>
                    </div>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mês</th>
                            <th class="text-center">Agendamentos</th>
                            <th class="right">Receita bruta</th>
                            <th class="right">Descontos</th>
                            <th class="right">Receita líquida</th>
                            <th class="right">Crescimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($meses ?? [] as $m)
                        <tr>
                            <td><strong>{{ $m['nome'] }}</strong></td>
                            <td class="text-center">{{ $m['agendamentos'] ?? 0 }}</td>
                            <td class="val-cell">R$ {{ number_format($m['receita_bruta'] ?? $m['receita'], 2, ',', '.') }}</td>
                            <td class="val-cell">R$ {{ number_format($m['descontos'] ?? 0, 2, ',', '.') }}</td>
                            <td class="val-cell">R$ {{ number_format(($m['receita_bruta'] ?? $m['receita']) - ($m['descontos'] ?? 0), 2, ',', '.') }}</td>
                            <td class="trend-cell">
                                @php $g = $m['crescimento'] ?? 0; @endphp
                                <span class="trend-pill {{ $g > 0 ? 'up' : ($g < 0 ? 'down' : 'flat') }}">
                                    <svg class="icon icon-xs"><use href="#i-arrow-{{ $g > 0 ? 'up' : ($g < 0 ? 'down' : 'right') }}"/></svg>
                                    {{ $g > 0 ? '+' : '' }}{{ number_format($g, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Nenhum dado disponível para o período.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-stack">
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-medal"/></svg></div>
                    <div>
                        <h2 class="panel-title">Faturamento por barbeiro</h2>
                        <div class="panel-subtitle">Ranking do período</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                @if(isset($porBarbeiro) && count($porBarbeiro) > 0)
                    @php $maxBarRev = max($porBarbeiro->pluck('total')->toArray()); @endphp
                    @php $barberColors = ['av-amber', 'av-blue', 'av-green', 'av-purple', 'av-pink']; @endphp
                    <div class="hbar-list">
                        @foreach($porBarbeiro as $nome => $dados)
                        @php $pct = $maxBarRev > 0 ? ($dados['total'] / $maxBarRev) * 100 : 0; @endphp
                        <div class="hbar-row">
                            <div class="hbar-avatar {{ $barberColors[$loop->index % count($barberColors)] }}">{{ substr($nome, 0, 2) }}</div>
                            <div class="hbar-info">
                                <div class="head">
                                    <span class="name">{{ $nome }}</span>
                                    <span class="val">R$ {{ number_format($dados['total'], 2, ',', '.') }} <span class="mut">{{ $dados['quantidade'] }} atend.</span></span>
                                </div>
                                <div class="hbar-track">
                                    <div class="hbar-fill" style="width: {{ $pct }}%; background: linear-gradient(90deg, var(--accent), var(--accent-soft));"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:20px; color:var(--text-muted);">Nenhum dado disponível.</div>
                @endif
            </div>
        </div>

        <div class="panel fade-in d6">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon" style="background:var(--success-bg);color:var(--success)"><svg class="icon"><use href="#i-check"/></svg></div>
                    <div>
                        <h2 class="panel-title">Resumo</h2>
                        <div class="panel-subtitle">Indicadores do período</div>
                    </div>
                </div>
            </div>
            <div class="panel-body" style="display:flex; flex-direction:column; gap:12px;">
                <div class="summary-box">
                    <div class="ic" style="background:var(--accent-glow);color:var(--accent)"><svg class="icon"><use href="#i-wallet"/></svg></div>
                    <div class="tx">
                        <div class="l">Receita bruta</div>
                        <div class="v"><span class="cur">R$</span>{{ number_format($totalPeriodo ?? 0, 2, ',', '.') }}</div>
                    </div>
                    <div class="dl" style="color:var(--success);background:var(--success-bg)">+{{ number_format($crescimento ?? 0, 1) }}%</div>
                </div>
                <div class="summary-box">
                    <div class="ic" style="background:var(--info-bg);color:var(--info)"><svg class="icon"><use href="#i-trend-up"/></svg></div>
                    <div class="tx">
                        <div class="l">Ticket médio</div>
                        <div class="v"><span class="cur">R$</span>{{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</div>
                    </div>
                </div>
                <div class="summary-box">
                    <div class="ic" style="background:var(--purple-bg);color:var(--purple)"><svg class="icon"><use href="#i-calendar-check"/></svg></div>
                    <div class="tx">
                        <div class="l">Total de atendimentos</div>
                        <div class="v">{{ $totalAtendimentos ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
