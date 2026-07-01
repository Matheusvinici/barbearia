@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.users.index') }}" style="color:inherit;text-decoration:none;">Usuários</a>
<span class="sep">/</span>
<span class="current">{{ $user->name }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Detalhes do usuário</span>
<span class="pipe">·</span>
<span>{{ $user->email }}</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.users.edit', $user) }}" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
<a href="{{ route('admin.users.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<div class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 11v2h-8M18 9l4 4-4 4"/></svg></div>
            <div>
                <h2 class="panel-title">{{ $user->name }}</h2>
                <div class="panel-subtitle">
                    @foreach($user->roles as $r)
                    <span class="badge-c badge-info">{{ ucfirst($r->name) }}</span>
                    @endforeach
                    @if($user->roles->isEmpty())
                    <span class="badge-c gray">Sem papel</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="form-grid" style="max-width:600px;">
            <div class="form-group">
                <label class="form-label" style="color:var(--text-muted);font-size:12px;">Nome</label>
                <div style="font-size:15px;font-weight:600;">{{ $user->name }}</div>
            </div>
            <div class="form-group">
                <label class="form-label" style="color:var(--text-muted);font-size:12px;">Email</label>
                <div style="font-size:15px;">{{ $user->email }}</div>
            </div>
            <div class="form-group">
                <label class="form-label" style="color:var(--text-muted);font-size:12px;">Criado em</label>
                <div style="font-size:15px;">{{ $user->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="form-group">
                <label class="form-label" style="color:var(--text-muted);font-size:12px;">Atualizado em</label>
                <div style="font-size:15px;">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
