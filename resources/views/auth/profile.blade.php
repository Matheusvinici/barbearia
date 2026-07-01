@php
    $user = auth()->user();
    $initial = mb_substr($user->name, 0, 1, 'UTF-8');
    $joined = $user->created_at ? $user->created_at->format('M Y') : 'N/A';
@endphp

@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-home"/></svg>
    <span class="sep">/</span>
    <span class="current">Perfil</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Conta verificada</span>
    <span class="pipe">·</span>
    <span>Membro desde {{ $joined }}</span>
@endsection

@section('topbar-actions')
    <button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
    <button class="btn-primary-c" id="saveProfileBtn"><svg class="icon icon-sm"><use href="#i-check"/></svg>Salvar Alterações</button>
@endsection

@push('styles')
<style>
.profile-header { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-xl); overflow: hidden; margin-bottom: 22px; position: relative; }
.cover { height: 160px; background: linear-gradient(135deg, #14141b 0%, #2a1a05 100%); position: relative; overflow: hidden; }
.cover::after { content: ''; position: absolute; top: -50%; right: -20%; width: 500px; height: 500px; border-radius: 50%; background: radial-gradient(circle, var(--accent-glow), transparent 60%); pointer-events: none; }
.header-body { padding: 0 32px 24px; display: flex; align-items: flex-end; gap: 24px; flex-wrap: wrap; position: relative; border-bottom: 1px solid var(--border); }
.avatar-wrap { position: relative; margin-top: -52px; flex-shrink: 0; }
.avatar-lg { width: 116px; height: 116px; border-radius: 28px; background: linear-gradient(135deg, #f87171, #f5b544); display: grid; place-items: center; font-weight: 800; color: white; font-size: 42px; letter-spacing: -0.03em; border: 4px solid var(--bg-elevated); box-shadow: 0 12px 30px -8px rgba(248, 113, 113, 0.3); }
.header-info { flex: 1; min-width: 220px; padding-bottom: 12px; }
.header-name { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap; }
.header-name h2 { font-size: 26px; font-weight: 800; letter-spacing: -0.03em; margin: 0; }
.role-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; color: var(--accent); background: var(--accent-glow); padding: 4px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.05em; }
.header-meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 13.5px; color: var(--text-muted); }
.header-meta .item { display: flex; align-items: center; gap: 6px; }
.tabs-nav { display: flex; gap: 4px; padding: 0 32px; overflow-x: auto; scrollbar-width: none; }
.tabs-nav::-webkit-scrollbar { display: none; }
.tab-btn { padding: 16px 18px; background: transparent; border: none; color: var(--text-muted); font-size: 14.5px; font-weight: 600; cursor: pointer; position: relative; transition: color 150ms; font-family: inherit; white-space: nowrap; display: flex; align-items: center; gap: 8px; }
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--accent); }
.tab-btn.active::after { content: ''; position: absolute; bottom: -1px; left: 12px; right: 12px; height: 3px; background: var(--accent); border-radius: 3px 3px 0 0; }
.main-grid { display: grid; grid-template-columns: 1fr 340px; gap: 18px; margin-top: 22px; }
.col-stack { display: flex; flex-direction: column; gap: 18px; }
.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 16px; font-weight: 700; margin: 0; letter-spacing: -0.015em; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }
.panel-body { padding: 24px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-label { font-size: 12.5px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 6px; }
.form-input, .form-select { width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-strong); background: var(--bg); color: var(--text); font-family: inherit; font-size: 14px; transition: all 180ms; }
.form-input:focus, .form-select:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); background: var(--bg-elevated); }
.form-input::placeholder { color: var(--text-faint); }
.toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 16px 0; border-bottom: 1px solid var(--border); gap: 20px; }
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; }
.toggle-info .t { font-size: 14px; font-weight: 600; color: var(--text); }
.toggle-info .d { font-size: 12.5px; color: var(--text-muted); margin-top: 3px; line-height: 1.4; }
.switch { position: relative; width: 42px; height: 24px; border-radius: 999px; background: var(--border-strong); cursor: pointer; transition: background 200ms; flex-shrink: 0; border: none; padding: 0; }
.switch::after { content: ''; position: absolute; top: 2px; left: 2px; width: 20px; height: 20px; border-radius: 50%; background: white; transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
.switch.on { background: var(--accent); }
.switch.on::after { transform: translateX(18px); }
.activity-list { display: flex; flex-direction: column; gap: 14px; padding: 4px 0; }
.activity-item { display: flex; gap: 14px; position: relative; }
.activity-icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; }
.activity-icon.green { background: var(--success-bg); color: var(--success); }
.activity-icon.amber { background: var(--accent-glow); color: var(--accent); }
.activity-icon.blue { background: var(--info-bg); color: var(--info); }
.activity-icon.purple { background: var(--purple-bg); color: var(--purple); }
.activity-content { flex: 1; min-width: 0; padding-top: 4px; }
.activity-content .text { font-size: 13.5px; line-height: 1.4; }
.activity-content .text strong { font-weight: 700; }
.activity-content .time { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; font-weight: 500; }
.activity-line { position: absolute; left: 17px; top: 40px; bottom: -18px; width: 1px; background: var(--border); }
.activity-item:last-child .activity-line { display: none; }
.fade-in { animation: fadeInUp 400ms ease both; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 992px) {
    .main-grid { grid-template-columns: 1fr; }
    .header-body { padding: 0 20px 20px; }
    .tabs-nav { padding: 0 20px; }
}
@media (max-width: 768px) {
    .cover { height: 120px; }
    .avatar-lg { width: 90px; height: 90px; font-size: 32px; border-radius: 22px; }
    .header-body { flex-direction: column; align-items: flex-start; gap: 16px; }
    .avatar-wrap { margin-top: -45px; }
    .header-info { padding-bottom: 0; }
    .panel-header, .panel-body { padding: 18px; }
    .form-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<section class="profile-header fade-in">
    <div class="cover"></div>
    <div class="header-body">
        <div class="avatar-wrap">
            <div class="avatar-lg">{{ $initial }}</div>
        </div>
        <div class="header-info">
            <div class="header-name">
                <h2>{{ $user->name }}</h2>
                <span class="role-badge"><svg class="icon icon-xs"><use href="#i-shield"/></svg>{{ $user->roles->first()?->name ?? 'Admin' }}</span>
            </div>
            <div class="header-meta">
                <span class="item"><svg class="icon icon-sm"><use href="#i-mail"/></svg>{{ $user->email }}</span>
                @if($user->telefone)
                <span class="item"><svg class="icon icon-sm"><use href="#i-call"/></svg>{{ $user->telefone }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="tabs-nav">
        <button class="tab-btn active" data-tab="tab-info">
            <svg class="icon icon-sm"><use href="#i-user-tag"/></svg> Dados Pessoais
        </button>
        <button class="tab-btn" data-tab="tab-security">
            <svg class="icon icon-sm"><use href="#i-shield"/></svg> Segurança
        </button>
        <button class="tab-btn" data-tab="tab-notifications">
            <svg class="icon icon-sm"><use href="#i-bell"/></svg> Notificações
        </button>
    </div>
</section>

<div class="main-grid">
    <div class="col-stack">

        <div class="tab-content" id="tab-info">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-user-check"/></svg></div>
                        <div>
                            <h2 class="panel-title">Dados Pessoais</h2>
                            <div class="panel-subtitle">Atualize suas informações de perfil</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div style="color:var(--danger);font-size:12px;">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">E-mail</label>
                                <div class="input-group" style="position:relative;">
                                    <span class="addon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-faint);pointer-events:none;"><svg class="icon icon-sm"><use href="#i-mail"/></svg></span>
                                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" style="padding-left:42px;" required>
                                </div>
                                @error('email') <div style="color:var(--danger);font-size:12px;">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Telefone</label>
                                <div class="input-group" style="position:relative;">
                                    <span class="addon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-faint);pointer-events:none;"><svg class="icon icon-sm"><use href="#i-call"/></svg></span>
                                    <input type="text" name="telefone" class="form-input" value="{{ old('telefone', $user->telefone ?? '') }}" style="padding-left:42px;">
                                </div>
                            </div>
                            @if($user->roles->count())
                            <div class="form-group">
                                <label class="form-label">Cargo</label>
                                <div class="input-group" style="position:relative;">
                                    <span class="addon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-faint);pointer-events:none;"><svg class="icon icon-sm"><use href="#i-briefcase"/></svg></span>
                                    <input type="text" class="form-input" value="{{ $user->roles->pluck('name')->implode(', ') }}" style="padding-left:42px;" disabled>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab-security" style="display: none;">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-key"/></svg></div>
                        <div>
                            <h2 class="panel-title">Alterar Senha</h2>
                            <div class="panel-subtitle">Recomendamos trocar sua senha a cada 90 dias</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('profile.password') }}" id="passwordForm">
                        @csrf
                        @method('PUT')
                        <div class="form-grid full">
                            <div class="form-group">
                                <label class="form-label">Senha atual</label>
                                <input type="password" name="current_password" class="form-input" required>
                                @error('current_password') <div style="color:var(--danger);font-size:12px;">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nova senha</label>
                                <input type="password" name="password" class="form-input" placeholder="Mínimo 8 caracteres" required>
                                @error('password') <div style="color:var(--danger);font-size:12px;">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirmar nova senha</label>
                                <input type="password" name="password_confirmation" class="form-input" placeholder="Repita a nova senha" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-shield"/></svg></div>
                        <div>
                            <h2 class="panel-title">Segurança da Conta</h2>
                            <div class="panel-subtitle">Proteção e privacidade</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Autenticação em duas etapas (2FA)</div>
                            <div class="d">Adicione uma camada extra de segurança usando um código enviado para seu celular.</div>
                        </div>
                        <button class="switch on" data-setting="2fa"></button>
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Alerta de login em novo dispositivo</div>
                            <div class="d">Receba um e-mail sempre que sua conta for acessada de um dispositivo desconhecido.</div>
                        </div>
                        <button class="switch on" data-setting="login-alert"></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab-notifications" style="display: none;">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-bell"/></svg></div>
                        <div>
                            <h2 class="panel-title">Preferências de Notificação</h2>
                            <div class="panel-subtitle">Escolha como e quando ser avisado</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Notificações no painel</div>
                            <div class="d">Receba alertas push no navegador enquanto o sistema estiver aberto.</div>
                        </div>
                        <button class="switch on" data-setting="push"></button>
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Alertas por e-mail</div>
                            <div class="d">Resumo diário de atividades e alertas importantes.</div>
                        </div>
                        <button class="switch on" data-setting="email"></button>
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Mensagens no WhatsApp</div>
                            <div class="d">Receba lembretes da agenda do dia no seu WhatsApp.</div>
                        </div>
                        <button class="switch on" data-setting="whatsapp"></button>
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Marketing e novidades</div>
                            <div class="d">Dicas de uso, novas funcionalidades e promoções parceiras.</div>
                        </div>
                        <button class="switch" data-setting="marketing"></button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-stack">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-activity"/></svg></div>
                    <div>
                        <h2 class="panel-title">Atividade Recente</h2>
                        <div class="panel-subtitle">Suas últimas ações no sistema</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon green"><svg class="icon icon-sm"><use href="#i-check"/></svg></div>
                        <div class="activity-content">
                            <div class="text"><strong>Login realizado</strong></div>
                            <div class="time">Agora</div>
                        </div>
                        <div class="activity-line"></div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon amber"><svg class="icon icon-sm"><use href="#i-edit"/></svg></div>
                        <div class="activity-content">
                            <div class="text"><strong>Acesso ao perfil</strong></div>
                            <div class="time">Hoje</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.getElementById('saveProfileBtn').addEventListener('click', function() {
    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
        const tabId = activeTab.dataset.tab;
        if (tabId === 'tab-info') {
            document.getElementById('profileForm').submit();
        } else if (tabId === 'tab-security') {
            document.getElementById('passwordForm').submit();
        } else {
            const showToast = window.showToast;
            if (showToast) showToast('Sucesso', 'Preferências salvas.');
        }
    }
});
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const targetId = btn.dataset.tab;
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById(targetId).style.display = 'block';
    });
});
document.querySelectorAll('.switch').forEach(sw => {
    sw.addEventListener('click', () => {
        sw.classList.toggle('on');
    });
});
</script>
@endpush
