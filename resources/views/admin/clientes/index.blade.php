@extends('layouts.app')

@section('title', 'Clientes')

@push('styles')
<style>
.action-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 8px;
    border: 1.5px solid;
    background: transparent;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 150ms;
    font-size: 12.5px;
    font-weight: 600;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.action-btn.edit {
    color: var(--accent);
    border-color: var(--accent);
}
.action-btn.edit:hover {
    background: var(--accent-glow);
}
.action-btn.danger {
    color: var(--danger);
    border-color: var(--danger);
}
.action-btn.danger:hover {
    background: var(--danger-bg);
}
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 17.5v-3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
<span class="sep">/</span>
<span class="current">Clientes</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $clientes->total() }} cadastrados</span>
<span class="pipe">·</span>
<span>{{ $clientes->where('created_at', '>=', now()->startOfMonth())->count() ?? 0 }} novos este mês</span>
@endsection

@section('topbar-actions')
<div class="search-box">
    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11.5" cy="11.5" r="8.5"/><path d="M21 21l-4.35-4.35"/></svg>
    <input type="text" placeholder="Buscar cliente…" id="searchInput">
    <span class="kbd">⌘K</span>
</div>
<a href="{{ route('admin.clientes.create') }}" class="btn-primary-c">
    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 7.5c0 2.07-1.68 3.75-3.75 3.75S6.5 9.57 6.5 7.5 8.18 3.75 10.25 3.75 14 5.43 14 7.5z"/><path d="M2.5 20c0-3.5 3-5.5 6-5.5M19 8v6M22 11h-6"/></svg>
    Novo Cliente
</a>
@endsection

@section('content')
@php
    $avatarClasses = ['av-amber', 'av-blue', 'av-green', 'av-red', 'av-purple', 'av-pink', 'av-teal', 'av-indigo'];
    $total = $clientes->total();
    $ativos = $clientes->filter(fn($c) => $c->agendamentos_count > 0)->count();
    $comPlanos = $clientes->filter(fn($c) => $c->planos->where('ativo', true)->count() > 0)->count();
    $novosMes = $clientes->filter(fn($c) => $c->created_at->isCurrentMonth())->count();
    function initials($name) {
        $parts = explode(' ', trim($name));
        return count($parts) >= 2 ? mb_substr($parts[0], 0, 1, 'UTF-8') . mb_substr($parts[1], 0, 1, 'UTF-8') : mb_substr($name, 0, 2, 'UTF-8');
    }
    function statusInfo($cliente) {
        if ($cliente->agendamentos_count > 0) return ['Ativo', 'green'];
        if ($cliente->created_at->diffInDays(now()) <= 30) return ['Novo', 'blue'];
        return ['Inativo', 'gray'];
    }
@endphp

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5M16 8.5a3 3 0 0 1 0 6M21 19c0-2.5-1.5-4.5-3.5-5"/></svg>
            </div>
        </div>
        <div class="stat-label">Total de clientes</div>
        <div class="stat-value">{{ $total }}</div>
        <div class="stat-sub">{{ $novosMes }} novos este mês</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5M16 8.5a3 3 0 0 1 0 6M21 19c0-2.5-1.5-4.5-3.5-5"/></svg>
            </div>
        </div>
        <div class="stat-label">Clientes ativos</div>
        <div class="stat-value">{{ $ativos }}</div>
        <div class="stat-sub">{{ $total > 0 ? round(($ativos / $total) * 100) : 0 }}% do total</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
            </div>
        </div>
        <div class="stat-label">Com planos</div>
        <div class="stat-value">{{ $comPlanos }}</div>
        <div class="stat-sub">{{ $total > 0 ? round(($comPlanos / $total) * 100) : 0 }}% do total</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="9" cy="6" rx="6" ry="3"/><path d="M3 6v5c0 1.66 2.69 3 6 3s6-1.34 6-3V6M3 11v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5M15 9c2.76 0 6 .84 6 3v5c0 1.66-2.69 3-6 3"/></svg>
            </div>
        </div>
        <div class="stat-label">Novos este mês</div>
        <div class="stat-value">{{ $novosMes }}</div>
        <div class="stat-sub">cadastrados em {{ now()->format('F') }}</div>
    </div>
</section>

<section class="main-grid">
    <div class="col-stack">
        <div class="panel fade-in d5">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5M16 8.5a3 3 0 0 1 0 6M21 19c0-2.5-1.5-4.5-3.5-5"/></svg>
                    </div>
                    <div>
                        <h2 class="panel-title">Todos os clientes</h2>
                        <div class="panel-subtitle">{{ $total }} registros</div>
                    </div>
                </div>
            </div>

            @if($clientes->count())
            <div class="table-wrap">
                <table class="data-table clients-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th class="text-center">Agendamentos</th>
                            <th>Gastos</th>
                            <th>Status</th>
                            <th class="right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $i => $c)
                        @php
                            $init = initials($c->nome);
                            $avClass = $avatarClasses[$i % count($avatarClasses)];
                            [$statusLabel, $statusClass] = statusInfo($c);
                            $totalGasto = $c->agendamentos->sum('total');
                        @endphp
                        <tr>
                            <td data-label="Nome">
                                <div class="avatar-row">
                                    <div class="av {{ $avClass }}">{{ $init }}</div>
                                    <div class="info">
                                        <strong>{{ $c->nome }}</strong>
                                        <span>{{ $c->email ?? 'sem e-mail' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Telefone">
                                <div class="contact-cell">
                                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z"/></svg>
                                    {{ $c->telefone }}
                                </div>
                            </td>
                            <td data-label="E-mail">
                                <div class="contact-cell">
                                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4.5" width="20" height="15" rx="3"/><path d="M3 6l9 7 9-7"/></svg>
                                    {{ $c->email ?? '-' }}
                                </div>
                            </td>
                            <td data-label="Agendamentos" class="text-center">
                                <div class="visits-cell">
                                    <div class="n">{{ $c->agendamentos_count }}</div>
                                    <div class="s">agendamentos</div>
                                </div>
                            </td>
                            <td data-label="Gastos">
                                <div class="value-cell">
                                    @if($totalGasto > 0)
                                        R$ {{ number_format($totalGasto, 2, ',', '.') }}
                                        <span class="sub">{{ $c->agendamentos_count }} {{ $c->agendamentos_count === 1 ? 'visita' : 'visitas' }}</span>
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="badge-c {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td data-label="Ações">
                                <div class="actions-cell" style="display:flex;gap:4px;justify-content:flex-end;">
                                    <a href="{{ route('admin.clientes.edit', $c) }}" class="action-btn edit" title="Editar">
                                        <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar
                                    </a>
                                    <button type="button" class="action-btn danger" title="Excluir" onclick="confirmarExclusao('{{ route('admin.clientes.destroy', $c) }}')">
                                        <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13c0 1 .5 2 2 2h8c1.5 0 2-1 2-2l1-13M9 7V4c0-1 .5-1 1-1h4c.5 0 1 0 1 1v3"/></svg>Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($clientes->hasPages())
            <div class="panel-footer">
                <div class="pagination-info">
                    Mostrando <strong>{{ $clientes->firstItem() }} – {{ $clientes->lastItem() }}</strong> de <strong>{{ $clientes->total() }}</strong> clientes
                </div>
                <div class="pagination-c">
                    {{ $clientes->links() }}
                </div>
            </div>
            @endif
            @else
            <div class="panel-body">
                <div class="empty-state">
                    <div class="icon-wrap">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 19c0-3 2.5-5 6-5s6 2 6 5M16 8.5a3 3 0 0 1 0 6M21 19c0-2.5-1.5-4.5-3.5-5"/></svg>
                    </div>
                    <h4>Nenhum cliente encontrado</h4>
                    <p>Comece cadastrando seu primeiro cliente.</p>
                    <a href="{{ route('admin.clientes.create') }}" class="btn-primary-c">
                        <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 12h12M12 6v12"/></svg>
                        Novo Cliente
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((r) => {
        if (r.isConfirmed) {
            $.ajax({
                url: url,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => location.reload()
            });
        }
    });
}
</script>
@endpush
