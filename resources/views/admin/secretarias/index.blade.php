@extends('layouts.app')

@section('title', 'Secretárias')

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
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<span class="current">Secretárias</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $secretarias->total() }} secretárias cadastradas</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon"><use href="#i-menu"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
<button class="icon-btn"><svg class="icon"><use href="#i-bell"/></svg><span class="dot-notif"></span></button>
<a href="{{ route('admin.secretarias.create') }}" class="btn-primary-c"><svg class="icon icon-sm"><use href="#i-plus"/></svg>Nova Secretária</a>
@endsection

@section('content')
<div class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-people"/></svg></div>
            <div>
                <h2 class="panel-title">Secretárias</h2>
                <div class="panel-subtitle">Usuárias com acesso administrativo limitado</div>
            </div>
        </div>
    </div>

    <div class="panel-body" style="padding:0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Unidades</th>
                    <th>Status</th>
                    <th style="width:140px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($secretarias as $s)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="av pink">{{ mb_substr($s->name, 0, 2) }}</div>
                            <div class="info">
                                <strong>{{ $s->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ $s->email }}</td>
                    <td>
                        @foreach($s->barbearias as $b)
                        <span class="badge-c outlined" style="margin:1px 2px;">{{ $b->nome }}</span>
                        @endforeach
                        @if($s->barbearias->isEmpty())
                        <span class="badge-c gray">Nenhuma</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $isAtivo = $s->barbearias->first()?->pivot->ativo ?? false;
                        @endphp
                        @if($isAtivo)
                        <span class="badge-c green">Ativa</span>
                        @else
                        <span class="badge-c red">Inativa</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <button onclick="toggleAtivo('{{ route('admin.secretarias.toggle-ativo', $s) }}')" class="action-btn {{ $isAtivo ? 'danger' : 'success' }}" title="{{ $isAtivo ? 'Desativar' : 'Ativar' }}">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                {{ $isAtivo ? 'Desat.' : 'Ativar' }}
                            </button>
                            <a href="{{ route('admin.secretarias.edit', $s) }}" class="action-btn edit" title="Editar">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar
                            </a>
                            <button onclick="confirmarExclusao('{{ route('admin.secretarias.destroy', $s) }}')" class="action-btn danger" title="Excluir">
                                <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 13c0 1 .5 2 2 2h8c1.5 0 2-1 2-2l1-13M9 7V4c0-1 .5-1 1-1h4c.5 0 1 0 1 1v3"/></svg>Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <svg class="icon" style="width:40px;height:40px;margin-bottom:12px;opacity:0.4;"><use href="#i-people"/></svg>
                        <div style="font-size:16px;font-weight:600;">Nenhuma secretária cadastrada</div>
                        <div style="font-size:13px;margin-top:4px;">Clique em "Nova Secretária" para começar</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($secretarias->hasPages())
    <div class="panel-footer" style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:center;">
        {{ $secretarias->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleAtivo(url) {
    $.ajax({
        url: url,
        method: 'PATCH',
        data: { _token: '{{ csrf_token() }}' },
        success: () => location.reload()
    });
}
function confirmarExclusao(url) {
    Swal.fire({ title: 'Excluir secretária?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Excluir' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
</script>
@endpush
