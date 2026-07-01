@extends('layouts.app')

@section('title', $edit ? 'Editar Usuário' : 'Novo Usuário')

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.users.index') }}" style="color:inherit;text-decoration:none;">Usuários</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar Usuário' : 'Novo Usuário' }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $edit ? 'Editando' : 'Cadastrando' }} usuário</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.users.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<form action="{{ $edit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
    @csrf
    @if($edit) @method('PUT') @endif

    <div class="main-grid">
        <div class="col-stack">

            <div class="panel fade-in d1">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 11v2h-8M18 9l4 4-4 4"/></svg></div>
                        <div>
                            <h2 class="panel-title">{{ $edit ? 'Editar Usuário' : 'Novo Usuário' }}</h2>
                            <div class="panel-subtitle">Informações de acesso ao sistema</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
                                <input type="text" name="name" class="form-input @error('name') form-error @enderror" placeholder="Nome completo" value="{{ old('name', $edit ? $user->name : '') }}" required>
                            </div>
                            @error('name')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4.5" width="20" height="15" rx="3"/><path d="M3 6l9 7 9-7"/></svg></span>
                                <input type="email" name="email" class="form-input @error('email') form-error @enderror" placeholder="email@exemplo.com" value="{{ old('email', $edit ? $user->email : '') }}" required>
                            </div>
                            @error('email')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Senha {{ $edit ? '(deixe em branco para manter)' : '' }} *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <input type="password" name="password" class="form-input @error('password') form-error @enderror" placeholder="••••••••" {{ $edit ? '' : 'required' }}>
                            </div>
                            @error('password')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Papéis</label>
                            <div class="team-grid">
                                @forelse($roles as $r)
                                <label class="team-check {{ $edit && $user->hasRole($r->name) ? 'active' : '' }}" style="cursor:pointer;">
                                    <div class="team-avatar av-amber">{{ mb_substr($r->name, 0, 2) }}</div>
                                    <div class="team-info"><div class="n">{{ ucfirst($r->name) }}</div></div>
                                    <div class="check-circle"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></div>
                                    <input type="checkbox" name="roles[]" value="{{ $r->id }}" {{ $edit && $user->hasRole($r->name) ? 'checked' : '' }} style="display:none;">
                                </label>
                                @empty
                                <div style="grid-column:1/-1;padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">
                                    Nenhum papel disponível. Execute: php artisan db:seed --class=PermissionSeeder
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="action-card">
            <div class="action-buttons fade-in d2">
                <button type="submit" class="btn-primary-c">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    {{ $edit ? 'Atualizar Usuário' : 'Salvar Usuário' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost-c" style="width:100%;justify-content:center;height:48px;">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                    Cancelar
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
