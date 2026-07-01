@extends('layouts.app')
@section('title', 'Relatórios')
@section('breadcrumb')
    <span class="current">Relatórios</span>
@endsection
@section('subtitle')
    <span class="live-dot"></span>
    <span>Análise completa do seu negócio</span>
    <span class="pipe">·</span>
    <span>Dados atualizados em tempo real</span>
@endsection

@section('content')
<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-wallet"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>12,4%</span>
        </div>
        <div class="stat-label">Faturamento do mês</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($faturamentoMes ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">vs. mês anterior</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-calendar-check"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>8,2%</span>
        </div>
        <div class="stat-label">Agendamentos</div>
        <div class="stat-value">{{ $agendamentosMes ?? 0 }}</div>
        <div class="stat-sub">atendimentos este mês</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-trend-up"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>4%</span>
        </div>
        <div class="stat-label">Ticket médio</div>
        <div class="stat-value"><span class="cur">R$</span>{{ number_format($ticketMedio ?? 0, 2, ',', '.') }}</div>
        <div class="stat-sub">valor médio por atendimento</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-users-group"/></svg></div>
            <span class="stat-delta up"><svg class="icon icon-xs"><use href="#i-arrow-up"/></svg>6%</span>
        </div>
        <div class="stat-label">Taxa de conversão</div>
        <div class="stat-value">{{ number_format($taxaConversao ?? 0, 1) }}%</div>
        <div class="stat-sub">agendamentos concretizados</div>
    </div>
</section>

<section class="feature-grid" style="margin-top: 8px;">
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.faturamento', optional(request()->route('barbearia'))?->slug) : route('admin.relatorios.faturamento') }}" class="feature-card fade-in d3" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="feature-icon amber"><svg class="icon"><use href="#i-wallet"/></svg></div>
        <h4>Relatório de Faturamento</h4>
        <p>Receitas, despesas, lucro líquido e evolução financeira do seu negócio.</p>
    </a>
    <a href="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.relatorios.servicos', optional(request()->route('barbearia'))?->slug) : route('admin.relatorios.servicos') }}" class="feature-card fade-in d4" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="feature-icon blue"><svg class="icon"><use href="#i-scissor"/></svg></div>
        <h4>Relatório de Serviços</h4>
        <p>Desempenho individual dos serviços, mais vendidos e tendências.</p>
    </a>
    <a href="#" class="feature-card fade-in d5" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="feature-icon purple"><svg class="icon"><use href="#i-user-tag"/></svg></div>
        <h4>Relatório de Profissionais</h4>
        <p>Produtividade, faturamento por barbeiro e ranking de desempenho.</p>
    </a>
    <a href="#" class="feature-card fade-in d6" style="text-decoration: none; color: inherit; cursor: pointer;">
        <div class="feature-icon pink"><svg class="icon"><use href="#i-clock"/></svg></div>
        <h4>Relatório de Horários</h4>
        <p>Análise de ocupação, horários de pico e dias mais movimentados.</p>
    </a>
</section>
@endsection
