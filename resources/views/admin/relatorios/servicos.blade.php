@extends('layouts.app')
@section('title', 'Relatório de Serviços')
@section('breadcrumb')
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.index', optional(request()->route('barbearia'))?->slug) : route('admin.relatorios.index') }}" style="color:inherit; text-decoration:none;">Relatórios</a>
    <span class="sep">/</span>
    <span class="current">Serviços</span>
@endsection
@section('subtitle')
    <span class="live-dot"></span>
    <span>Desempenho dos serviços</span>
    <span class="pipe">·</span>
    <span>{{ \Carbon\Carbon::parse($dataInicio ?? now()->startOfMonth())->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim ?? now()->endOfMonth())->format('d/m/Y') }}</span>
@endsection

@section('content')
<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-scissors-2"/></svg></div>
        </div>
        <div class="stat-label">Total de serviços</div>
        <div class="stat-value">{{ $totalServicos ?? 0 }}</div>
        <div class="stat-sub">serviços realizados</div>
    </div>
    <div class="stat-card green fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-medal"/></svg></div>
        </div>
        <div class="stat-label">Mais vendido</div>
        <div class="stat-value" style="font-size: 22px;">{{ $maisVendido ?? '—' }}</div>
        <div class="stat-sub">serviço mais popular</div>
    </div>
    <div class="stat-card blue fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-wallet"/></svg></div>
        </div>
        <div class="stat-label">Receita total</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($receitaTotal ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">faturamento do período</div>
    </div>
    <div class="stat-card purple fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-trend-up"/></svg></div>
        </div>
        <div class="stat-label">Ticket médio</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">valor médio por serviço</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-scissors-2"/></svg></div>
            <div>
                <h2 class="panel-title">Desempenho por serviço</h2>
                <div class="panel-subtitle">Período selecionado</div>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th class="text-center">Quantidade</th>
                    <th class="right">Receita</th>
                    <th class="right">% do total</th>
                    <th class="right">Ticket médio</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $maxQtd = max(collect($servicosData ?? [])->pluck('quantidade')->toArray() ?: [1]); @endphp
                @forelse($servicosData ?? [] as $s)
                @php $pct = $receitaTotal > 0 ? ($s['receita'] / $receitaTotal) * 100 : 0; @endphp
                @php $ticket = $s['quantidade'] > 0 ? $s['receita'] / $s['quantidade'] : 0; @endphp
                @php $isMost = $loop->first; @endphp
                @php $isLeast = $loop->last && count($servicos) > 1; @endphp
                <tr>
                    <td>
                        <div class="svc-cell">
                            <div class="svc-icon" style="background:var(--accent-glow);color:var(--accent)"><svg class="icon icon-sm"><use href="#i-scissor"/></svg></div>
                            <div>
                                <div class="svc-name">{{ $s['nome'] }}</div>
                                <div class="svc-cat">{{ $s['categoria'] ?? 'Serviço' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center num-cell">{{ $s['quantidade'] }}</td>
                    <td class="val-cell">R$ {{ number_format($s['receita'], 2, ',', '.') }}</td>
                    <td class="val-cell">
                        <span class="mini-bar"><span style="width: {{ $pct * 2 }}%"></span></span>{{ number_format($pct, 1) }}%
                    </td>
                    <td class="val-cell">R$ {{ number_format($ticket, 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($isMost)
                        <span class="badge-c green"><svg class="icon icon-xs"><use href="#i-medal"/></svg>Mais vendido</span>
                        @elseif($isLeast)
                        <span class="badge-c gray">Menos vendido</span>
                        @else
                        <span class="badge-c blue">{{ number_format($pct, 1) }}%</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted);">Nenhum serviço realizado no período.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
