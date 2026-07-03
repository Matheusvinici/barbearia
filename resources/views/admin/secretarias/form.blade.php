@extends('layouts.app')

@section('title', $edit ? 'Editar Secretária' : 'Nova Secretária')

@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.secretarias.index') }}" style="color:inherit;text-decoration:none;">Secretárias</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar' : 'Nova' }} Secretária</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon"><use href="#i-menu"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
<button class="icon-btn"><svg class="icon"><use href="#i-bell"/></svg><span class="dot-notif"></span></button>
<a href="{{ route('admin.secretarias.index') }}" class="btn-ghost-c"><svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>Voltar</a>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">
        <div class="panel fade-in d1">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-user-plus"/></svg></div>
                    <div>
                        <h2 class="panel-title">{{ $edit ? 'Editar Secretária' : 'Nova Secretária' }}</h2>
                        <div class="panel-subtitle">Dados de acesso e permissões</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ $edit ? route('admin.secretarias.update', $secretaria) : route('admin.secretarias.store') }}" method="POST">
                    @csrf
                    @if($edit) @method('PUT') @endif

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nome *</label>
                            <input type="text" name="name" class="form-input" value="{{ old('name', $edit ? $secretaria->name : '') }}" placeholder="Nome completo" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail *</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $edit ? $secretaria->email : '') }}" placeholder="email@exemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Senha {{ $edit ? '(deixe vazio para manter)' : '*' }}</label>
                            <input type="password" name="password" class="form-input" placeholder="{{ $edit ? 'Nova senha' : 'Mínimo 6 caracteres' }}" {{ $edit ? '' : 'required' }}>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Unidades de Acesso *</label>
                            <select name="barbearias[]" class="form-select" multiple required style="height:100px;">
                                @foreach($barbearias as $b)
                                <option value="{{ $b->id }}"
                                    {{ $edit && $secretaria->barbearias->contains($b->id) ? 'selected' : '' }}>
                                    {{ $b->nome }}
                                </option>
                                @endforeach
                            </select>
                            <small style="font-size:11px;color:var(--text-faint);">Segure Ctrl para selecionar múltiplas</small>
                        </div>
                    </div>

                    <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                        <h3 style="font-size:15px;font-weight:700;margin:0 0 4px;">Permissões Específicas</h3>
                        <p style="font-size:12.5px;color:var(--text-muted);margin:0 0 16px;">
                            Marque permissões adicionais ou remova as existentes. Por padrão a secretária já possui permissões básicas.
                        </p>

                        @foreach($permissions as $group => $perms)
                        <div style="margin-bottom:16px;">
                            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--text-faint);margin-bottom:6px;">
                                {{ ucfirst($group) }}
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @foreach($perms as $perm)
                                <label class="filter-chip" style="cursor:pointer;user-select:none;{{ in_array($perm->id, old('permissions', $secretariaPermissions ?? [])) ? 'background:var(--accent-glow);color:var(--accent);border-color:transparent;' : '' }}">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        onchange="this.closest('.filter-chip').style.background=this.checked?'var(--accent-glow)':'';this.closest('.filter-chip').style.color=this.checked?'var(--accent)':'';this.closest('.filter-chip').style.borderColor=this.checked?'transparent':''"
                                        {{ in_array($perm->id, old('permissions', $secretariaPermissions ?? [])) ? 'checked' : '' }}
                                        style="display:none;">
                                    <span style="font-size:12px;">{{ $perm->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div style="display:flex;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                        <button type="submit" class="btn-primary-c"><svg class="icon icon-sm"><use href="#i-check"/></svg>{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
                        <a href="{{ route('admin.secretarias.index') }}" class="btn-ghost-c">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="action-card">
        <div class="panel" style="background:transparent;border:none;backdrop-filter:none;padding:0;">
            <div class="panel-body" style="padding:0;">
                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-info"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Acesso Administrativo</div>
                            <div class="d">A secretária terá acesso ao admin da barbearia com as permissões configuradas.</div>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-lock"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Permissões Granulares</div>
                            <div class="d">Você pode controlar exatamente o que cada secretária pode ver ou fazer.</div>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-user-check"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Desativação</div>
                            <div class="d">Ao desativar, a secretária perde o acesso imediatamente sem perder os dados.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
