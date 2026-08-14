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
.stat-icon.purple { background: var(--purple-bg); color: var(--purple); }
.stat-label { font-size: 0.875rem; color: var(--text-muted); font-weight: 600; margin-bottom: 6px; }
.stat-value { font-size: 2.4rem; font-weight: 800; letter-spacing: -1.5px; line-height: 1; }

.panel { background: var(--card-solid); border: 1px solid var(--border); border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; }
.panel-header { padding: 1.5rem 2rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title { font-size: 1.1rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }
.panel-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 2px; }
.panel-footer { padding: 0.5rem 2rem 1.5rem; display: flex; justify-content: center; }

.data-table th { text-align: center; padding: 4px 16px 10px; }
.data-table td { text-align: center; }
.data-table td:first-child { text-align: left; }
.data-table td:last-child { text-align: center; }

.action-btn { height: 34px; min-width: 100px; padding: 0 12px; border-radius: 9px; border: 1.5px solid var(--border-strong); background: transparent; color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: all 150ms; font-size: 12px; font-weight: 600; font-family: inherit; white-space: nowrap; text-decoration: none; }
.action-btn:hover { color: var(--text); border-color: var(--text-faint); background: var(--bg-input); }
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
                        <a href="{{ barbeiroRoute('barbeiros.edit', $b) }}" class="action-btn" title="Editar">
                            Editar
                        </a>
                        <button type="button" onclick="alternarStatus('{{ barbeiroRoute('barbeiros.toggle', $b) }}')" class="action-btn" title="{{ $b->ativo ? 'Desativar profissional' : 'Ativar profissional' }}">
                            {{ $b->ativo ? 'Desativar' : 'Ativar' }}
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
function alternarStatus(url) {
    $.ajax({
        url,
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(r) {
            Swal.fire({
                icon: 'success',
                title: r.message || 'Status atualizado!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => location.reload());
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Erro ao atualizar status', confirmButtonText: 'OK' });
        }
    });
}
</script>
@endpush
