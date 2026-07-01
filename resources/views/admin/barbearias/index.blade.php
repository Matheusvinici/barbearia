@extends('layouts.app')

@section('title', 'Barbearias')

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
.action-btn.edit { color: var(--accent); border-color: var(--accent); }
.action-btn.edit:hover { background: var(--accent-glow); }
.action-btn.danger { color: var(--danger); border-color: var(--danger); }
.action-btn.danger:hover { background: var(--danger-bg); }
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<span class="current">Barbearias</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $barbearias->total() }} unidades cadastradas</span>
<span class="pipe">·</span>
<span>{{ $barbearias->filter(fn($b) => $b->isMatriz())->count() }} matriz(es)</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.barbearias.create') }}" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 12h12M12 6v12"/></svg>Nova Barbearia</a>
@endsection

@section('content')
<section class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg></div>
            <div>
                <h2 class="panel-title">Lista de Barbearias</h2>
                <div class="panel-subtitle">Gerencie as unidades do sistema</div>
            </div>
        </div>
    </div>

    <div class="panel-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Matriz</th>
                    <th>Filial</th>
                    <th>Proprietário</th>
                    <th>Bairro</th>
                    <th>Cidade</th>
                    <th style="width:120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barbearias as $b)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="av purple">{{ mb_substr($b->nome, 0, 2) }}</div>
                            <div class="info">
                                <strong>{{ $b->nome }}</strong>
                                <span>{{ $b->descricao ? Str::limit($b->descricao, 30) : 'Sem descrição' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($b->isMatriz())
                        <span class="badge-c badge-info">Matriz</span>
                        @else
                        <span class="badge-c badge-success">Filial</span>
                        @endif
                    </td>
                    <td>{{ $b->parent?->nome ?? '-' }}</td>
                    <td>{{ $b->children_count ? $b->children_count . ' filial(is)' : '-' }}</td>
                    <td>{{ $b->owner?->name ?? '-' }}</td>
                    <td>{{ $b->bairro ?? '-' }}</td>
                    <td>{{ $b->cidade ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('admin.barbearias.edit', $b) }}" class="action-btn edit" title="Editar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
                            <button onclick="confirmarExclusao('{{ route('admin.barbearias.destroy', $b) }}')" class="action-btn danger" title="Excluir"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13c0 1 .5 2 2 2h8c1.5 0 2-1 2-2l1-13M9 7V4c0-1 .5-1 1-1h4c.5 0 1 0 1 1v3"/></svg>Excluir</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <svg class="icon" style="width:40px;height:40px;margin-bottom:12px;opacity:0.3;" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
                        <div style="font-size:15px;font-weight:600;margin-bottom:4px;">Nenhuma barbearia encontrada</div>
                        <div style="font-size:13px;">Clique em "Nova Barbearia" para começar.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($barbearias->hasPages())
    <div class="panel-footer" style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:center;">
        {{ $barbearias->links() }}
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((r) => {
        if (r.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}
</script>
@endpush
