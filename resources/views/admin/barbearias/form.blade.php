@extends('layouts.app')

@section('title', $edit ? 'Editar Barbearia' : 'Nova Barbearia')

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.barbearias.index') }}" style="color:inherit;text-decoration:none;">Barbearias</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar Barbearia' : 'Nova Barbearia' }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $edit ? 'Editando' : 'Cadastrando' }} unidade</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.barbearias.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">

        <form action="{{ $edit ? route('admin.barbearias.update', $barbearia) : route('admin.barbearias.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($edit) @method('PUT') @endif

            {{-- Basic Info --}}
            <div class="panel fade-in d1">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg></div>
                        <div>
                            <h2 class="panel-title">{{ $edit ? 'Editar Barbearia' : 'Nova Barbearia' }}</h2>
                            <div class="panel-subtitle">Informações da unidade</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg></span>
                                <input type="text" name="nome" class="form-input @error('nome') form-error @enderror" placeholder="Ex: Barbearia Centro" value="{{ old('nome', $edit ? $barbearia->nome : '') }}" required>
                            </div>
                            @error('nome')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Barbearia Matriz</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-6 9 6v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/></svg></span>
                                <select name="parent_id" class="form-input">
                                    <option value="">É matriz</option>
                                    @foreach($matrizes as $m)
                                    <option value="{{ $m->id }}" {{ old('parent_id', $edit ? $barbearia->parent_id : '') == $m->id ? 'selected' : '' }}>{{ $m->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <small style="font-size:11px;color:var(--text-faint);margin-top:4px;display:block;">Selecione a matriz se esta for uma filial</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bairro</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                                <input type="text" name="bairro" class="form-input @error('bairro') form-error @enderror" placeholder="Centro" value="{{ old('bairro', $edit ? $barbearia->bairro : '') }}">
                            </div>
                            @error('bairro')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cidade</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                                <input type="text" name="cidade" class="form-input @error('cidade') form-error @enderror" placeholder="São Paulo" value="{{ old('cidade', $edit ? $barbearia->cidade : '') }}">
                            </div>
                            @error('cidade')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Logomarca</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>
                                <input type="file" name="logo" class="form-input @error('logo') form-error @enderror" accept="image/jpeg,image/png,image/webp">
                            </div>
                            @error('logo')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            @if($edit && $barbearia->logo)
                            <div style="margin-top:8px;display:flex;align-items:center;gap:12px;">
                                <img src="{{ $barbearia->logo_url }}" alt="Logo" style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:1px solid var(--border);">
                                <span style="font-size:12px;color:var(--text-muted);">Logo atual. Envie uma nova para substituir.</span>
                            </div>
                            @endif
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-textarea @error('descricao') form-error @enderror" rows="3" placeholder="Descrição da unidade...">{{ old('descricao', $edit ? $barbearia->descricao : '') }}</textarea>
                            @error('descricao')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Owner Section --}}
            <div class="panel fade-in d2">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 11v2h-8M18 9l4 4-4 4"/></svg></div>
                        <div>
                            <h2 class="panel-title">Proprietário</h2>
                            <div class="panel-subtitle">{{ $edit ? 'Gerenciar proprietário da barbearia' : 'Criar o administrador que será o dono desta barbearia' }}</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    @if(!$edit)
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome do Proprietário *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></span>
                                <input type="text" name="owner_name" class="form-input @error('owner_name') form-error @enderror" placeholder="Nome completo" value="{{ old('owner_name') }}" required>
                            </div>
                            @error('owner_name')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail do Proprietário *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4.5" width="20" height="15" rx="3"/><path d="M3 6l9 7 9-7"/></svg></span>
                                <input type="email" name="owner_email" class="form-input @error('owner_email') form-error @enderror" placeholder="email@exemplo.com" value="{{ old('owner_email') }}" required>
                            </div>
                            @error('owner_email')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Senha do Proprietário *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <input type="password" name="owner_password" class="form-input @error('owner_password') form-error @enderror" placeholder="Mínimo 6 caracteres" required>
                            </div>
                            @error('owner_password')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    @else
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Proprietário Atual</label>
                            <div style="padding:8px 0;font-size:14px;color:var(--text-muted);">
                                {{ $barbearia->owner?->name ?? 'Nenhum' }} ({{ $barbearia->owner?->email ?? '-' }})
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vincular Proprietário</label>
                            <select name="owner_id" class="form-input">
                                <option value="">Manter atual</option>
                                @foreach($proprietarios as $p)
                                <option value="{{ $p->id }}" {{ old('owner_id', $barbearia->owner_id) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->email }})</option>
                                @endforeach
                            </select>
                            <small style="font-size:11px;color:var(--text-faint);margin-top:4px;display:block;">Selecione para alterar o proprietário</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail do Proprietário</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4.5" width="20" height="15" rx="3"/><path d="M3 6l9 7 9-7"/></svg></span>
                                <input type="email" name="owner_email" class="form-input" placeholder="E-mail do proprietário" value="{{ old('owner_email', $barbearia->owner?->email ?? '') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Senha do Proprietário</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                                <input type="password" name="owner_password" class="form-input" placeholder="Deixe vazio para manter">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:20px;">
                <button type="submit" class="btn-primary-c">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    {{ $edit ? 'Atualizar Barbearia' : 'Salvar Barbearia' }}
                </button>
                <a href="{{ route('admin.barbearias.index') }}" class="btn-ghost-c" style="padding:0 20px;height:44px;">Cancelar</a>
            </div>
        </form>

    </div>
</div>
@endsection
