@extends('layouts.app')

@php
    $__tenantSlug = request()->route('barbearia')?->slug;
@endphp

@section('title', 'Caixa')

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
.action-btn.view { color: var(--info); border-color: var(--info); }
.action-btn.view:hover { background: var(--info-bg); }
.action-btn.edit { color: var(--accent); border-color: var(--accent); }
.action-btn.edit:hover { background: var(--accent-glow); }
.action-btn.warning { color: var(--accent); border-color: var(--accent); }
.action-btn.warning:hover { background: var(--accent-glow); }
.action-btn.danger { color: var(--danger); border-color: var(--danger); }
.action-btn.danger:hover { background: var(--danger-bg); }
.form-caixa { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width:640px) { .form-caixa { grid-template-columns: 1fr; } }
.filter-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.filter-bar label { font-size: 13px; font-weight: 600; color: var(--text-muted); }
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<span class="current">Caixa</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Gestão de caixa diário</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<button id="btnAbrirCaixa" class="btn-primary-c" wire:click="toggleAbrir" onclick="event.preventDefault(); Livewire.dispatch('toggle-abrir')"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 12h12M12 6v12"/></svg>Abrir Caixa</button>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">
        @livewire('admin.caixa-table', ['tenantSlug' => $__tenantSlug], key('caixa-table'))
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('livewire:init', function () {
    Livewire.on('notify', function (message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type || 'success',
                title: message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            alert(message);
        }
    });

    Livewire.on('toggle-abrir', function () {
        Livewire.dispatch('toggle-abrir');
    });
});
</script>
@endpush
