@extends('layouts.app')

@section('title', 'Profissionais')

@php
    $__tenantSlug = request()->route('barbearia')?->slug;
    function barbeiroRoute($name, $params = []) {
        $slug = request()->route('barbearia')?->slug;
        if (!$slug) return route('admin.' . $name, $params);
        $params = is_array($params) ? $params : [$params];
        return route('tenant.admin.' . $name, array_merge([$slug], $params));
    }
@endphp

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
.action-btn.view {
    color: var(--info);
    border-color: var(--info);
}
.action-btn.view:hover {
    background: var(--info-bg);
}
.action-btn.edit {
    color: var(--accent);
    border-color: var(--accent);
}
.action-btn.edit:hover {
    background: var(--accent-glow);
}
.action-btn.delete {
    color: var(--danger);
    border-color: var(--danger);
}
.action-btn.delete:hover {
    background: var(--danger-bg);
}
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<span class="current">Profissionais</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
Total de {{ $barbeiros->total() }} profissionais
@endsection

@section('topbar-actions')
<a href="{{ barbeiroRoute('barbeiros.create') }}" class="btn-primary-c">
    <svg class="icon icon-sm"><use href="#i-plus"/></svg>
    Novo Profissional
</a>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top">
            <div class="stat-icon amber"><svg class="icon"><use href="#i-user-tag"/></svg></div>
        </div>
        <div class="stat-label">Total de Profissionais</div>
        <div class="stat-value">{{ $stats['total'] ?? $barbeiros->total() }}</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top">
            <div class="stat-icon green"><svg class="icon"><use href="#i-check"/></svg></div>
        </div>
        <div class="stat-label">Ativos</div>
        <div class="stat-value">{{ $stats['ativos'] ?? 0 }}</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top">
            <div class="stat-icon blue"><svg class="icon"><use href="#i-calendar"/></svg></div>
        </div>
        <div class="stat-label">Agendamentos Hoje</div>
        <div class="stat-value">{{ $stats['agendamentos_hoje'] ?? 0 }}</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top">
            <div class="stat-icon purple"><svg class="icon"><use href="#i-wallet"/></svg></div>
        </div>
        <div class="stat-label">Comissão Média</div>
        <div class="stat-value">{{ number_format($stats['comissao_media'] ?? $barbeiros->avg('comissao_percentual') ?? 0, 1) }}%</div>
    </div>
</div>

<div class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-user-tag"/></svg></div>
            <div>
                <h2 class="panel-title">Lista de Profissionais</h2>
                <div class="panel-subtitle">Gerencie sua equipe</div>
            </div>
        </div>
    </div>
    <div class="panel-body p-0">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Profissional</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Unidades</th>
                    <th>Comissão</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barbeiros as $b)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="avatar-circle">{{ mb_substr($b->nome, 0, 1) }}</div>
                            <div>
                                <div class="avatar-name">{{ $b->nome }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $b->email }}</td>
                    <td>{{ $b->telefone ?? '-' }}</td>
                    <td>
                        @if(($b->barbearias ?? null) && $b->barbearias->count())
                            @foreach($b->barbearias as $unidade)
                            <span class="badge-c">{{ $unidade->nome }}</span>
                            @endforeach
                        @else
                        —
                        @endif
                    </td>
                    <td>{{ $b->comissao_percentual }}%</td>
                    <td>
                        @if($b->ativo)
                        <span class="badge-c success">Ativo</span>
                        @else
                        <span class="badge-c danger">Inativo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ barbeiroRoute('barbeiros.show', $b) }}" class="action-btn view" title="Visualizar">
                            Visualizar
                        </a>
                        <a href="{{ barbeiroRoute('barbeiros.edit', $b) }}" class="action-btn edit" title="Editar">
                            Editar
                        </a>
                        <button type="button" onclick="confirmarExclusao('{{ barbeiroRoute('barbeiros.destroy', $b) }}')" class="action-btn delete" title="Excluir">
                            Excluir
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px; color: var(--text-muted);">
                        Nenhum profissional cadastrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($barbeiros, 'hasPages') && $barbeiros->hasPages())
    <div class="panel-footer">{{ $barbeiros->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f87171',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}
</script>
@endpush
