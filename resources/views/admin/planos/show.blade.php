@extends('layouts.app')

@section('title', $plano->nome)

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.planos.index') }}" style="color:inherit;text-decoration:none;">Planos</a>
<span class="sep">/</span>
<span class="current">{{ $plano->nome }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Detalhes do plano</span>
<span class="pipe">·</span>
<span>R$ {{ number_format($plano->valor, 2, ',', '.') }}</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.planos.edit', $plano) }}" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
<a href="{{ route('admin.planos.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<div class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></div>
            <div>
                <h2 class="panel-title">{{ $plano->nome }}</h2>
                <div class="panel-subtitle">
                    @if($plano->ativo)
                    <span class="badge-c badge-success">Ativo</span>
                    @else
                    <span class="badge-c badge-danger">Inativo</span>
                    @endif
                    <span class="pipe">·</span>
                    R$ {{ number_format($plano->valor, 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        @if($plano->descricao)
        <p style="margin-bottom:24px;color:var(--text-muted);">{{ $plano->descricao }}</p>
        @endif

        {{-- Quotas --}}
        <h3 style="font-size:14px;font-weight:600;margin-bottom:12px;">Cotas Inclusas</h3>
        <table class="data-table" style="border:0;">
            <thead>
                <tr><th>Serviço</th><th style="width:100px;">Quantidade</th></tr>
            </thead>
            <tbody>
                @forelse($plano->quotas as $q)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="av amber">{{ mb_substr($q->servico->nome, 0, 2) }}</div>
                            <div class="info">
                                <strong>{{ $q->servico->nome }}</strong>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-c badge-info">{{ $q->quantidade }}x</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" style="text-align:center;padding:24px;color:var(--text-muted);">Nenhum serviço vinculado</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Clients --}}
        <h3 style="font-size:14px;font-weight:600;margin:24px 0 12px;">Clientes Vinculados</h3>
        <table class="data-table" style="border:0;">
            <thead>
                <tr><th>Cliente</th><th>Telefone</th><th>Início</th><th>Fim</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($plano->clientes as $cp)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="av green">{{ mb_substr($cp->cliente->nome, 0, 2) }}</div>
                            <div class="info">
                                <strong>{{ $cp->cliente->nome }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ $cp->cliente->telefone }}</td>
                    <td>{{ $cp->data_inicio->format('d/m/Y') }}</td>
                    <td>{{ $cp->data_fim ? $cp->data_fim->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($cp->ativo)
                        <span class="badge-c badge-success">Ativo</span>
                        @else
                        <span class="badge-c badge-danger">Inativo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted);">Nenhum cliente vinculado</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
