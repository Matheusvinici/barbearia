<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-building" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V5c0-1 .5-2 2-2h10c1.5 0 2 1 2 2v16M9 7h2M9 11h2M9 15h2M13 7h2M13 11h2M13 15h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-whatsapp" viewBox="0 0 24 24" fill="none"><path d="M3 21l1.9-5.7A8.5 8.5 0 1 1 12 20.5a8.4 8.4 0 0 1-4.5-1.3L3 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5.6 0 1-.5 1-1l-.2-1.2-1.8.4-.8-.8c-.5-.5-1-1.3-1.3-1.8l.4-1.8L11 8.5c0-.6-.5-1-1-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-bell-ring" viewBox="0 0 24 24" fill="none"><path d="M20 10.5c0 4.5-3 8-7 9.5M4 10.5c0 4.5 3 8 7 9.5M12 3a4 4 0 0 0-4 4v3.5c0 1-.5 2-1 2.5h10c-.5-.5-1-1.5-1-2.5V7a4 4 0 0 0-4-4z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 3v1M9.5 20.5a2.5 2.5 0 0 0 5 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-credit-card" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M2 10h20M6 15h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24" fill="none"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24" fill="none"><rect x="2" y="4.5" width="20" height="15" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-call" viewBox="0 0 24 24" fill="none"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-map-pin" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.6"/></symbol>
    <symbol id="i-plug" viewBox="0 0 24 24" fill="none"><path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 0 1-12 0V8zM12 17v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-info" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
  </defs>
</svg>

@extends('layouts.app')

@section('title', 'Configurações')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-building"/></svg>
    <span class="sep">/</span>
    <span class="current">Configurações</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>Sistema saudável</span>
    <span class="pipe">·</span>
    <span>Última alteração há 2 dias</span>
@endsection

@section('topbar-actions')
    <button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon"><use href="#i-menu"/></svg></button>
    <button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
    <button class="icon-btn" id="notifBtn"><svg class="icon"><use href="#i-bell-ring"/></svg><span class="dot-notif"></span></button>
    <button type="submit" form="settings-form" class="btn-primary-c"><svg class="icon icon-sm"><use href="#i-check"/></svg>Salvar Alterações</button>
@endsection

@section('content')
<form id="settings-form" action="{{ route('admin.configuracoes.update') }}" method="POST">
    @csrf

    <div class="settings-grid fade-in d1">
        <nav class="settings-nav" id="settingsNav">
            <button type="button" class="settings-nav-item active" data-target="sec-geral">
                <svg class="icon icon-sm"><use href="#i-building"/></svg>Geral
            </button>
            <button type="button" class="settings-nav-item" data-target="sec-whatsapp">
                <svg class="icon icon-sm"><use href="#i-plug"/></svg>WhatsApp
            </button>
            <button type="button" class="settings-nav-item" data-target="sec-notificacoes">
                <svg class="icon icon-sm"><use href="#i-bell-ring"/></svg>Notificações
            </button>
            <button type="button" class="settings-nav-item" data-target="sec-financeiro">
                <svg class="icon icon-sm"><use href="#i-credit-card"/></svg>Financeiro
            </button>
        </nav>

        <div>
            <section class="panel" id="sec-geral">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-building"/></svg></div>
                        <div>
                            <h2 class="panel-title">Geral</h2>
                            <div class="panel-subtitle">Informações da barbearia</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Nome da Barbearia</label>
                            <input type="text" name="nome_barbearia" class="form-input" value="{{ $configuracoes['nome_barbearia'] ?? '' }}" required>
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Endereço</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm"><use href="#i-map-pin"/></svg></span>
                                <input type="text" name="endereco" class="form-input" value="{{ $configuracoes['endereco'] ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefone</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm"><use href="#i-call"/></svg></span>
                                <input type="text" name="telefone" class="form-input" value="{{ $configuracoes['telefone'] ?? '' }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm"><use href="#i-mail"/></svg></span>
                                <input type="email" name="email" class="form-input" value="{{ $configuracoes['email'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel" id="sec-whatsapp">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-plug"/></svg></div>
                        <div>
                            <h2 class="panel-title">WhatsApp</h2>
                            <div class="panel-subtitle">Conecte o bot do WhatsApp</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="int-card" style="margin-bottom:20px;">
                        <div class="int-logo whatsapp" style="width:48px;height:48px;border-radius:12px;display:grid;place-items:center;flex-shrink:0;background:rgba(37,211,102,0.15);color:#25d366;"><svg class="icon"><use href="#i-whatsapp"/></svg></div>
                        <div class="int-info">
                            <div class="n">WhatsApp
                                @if($botAuthenticated ?? false)
                                <span class="int-status connected" style="font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;display:inline-flex;align-items:center;gap:5px;background:rgba(74,222,128,0.12);color:#4ade80;">Conectado</span>
                                @elseif($botOnline ?? false)
                                <span class="int-status" style="font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;display:inline-flex;align-items:center;gap:5px;background:rgba(251,191,36,0.14);color:#fbbf24;">Aguardando QR</span>
                                @else
                                <span class="int-status disconnected" style="font-size:11px;font-weight:700;padding:3px 8px;border-radius:999px;display:inline-flex;align-items:center;gap:5px;background:var(--border);color:var(--text-faint);">Desconectado</span>
                                @endif
                            </div>
                            <div class="d">Envio automático de lembretes e confirmações para os clientes.</div>
                        </div>
                    </div>

                    @if(!$botOnline ?? true)
                    <div class="panel" style="background:var(--bg);border:1px solid var(--border);border-radius:var(--r-md);padding:16px;margin-bottom:20px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <svg class="icon" style="color:var(--warning);flex-shrink:0;"><use href="#i-info"/></svg>
                            <div>
                                <div style="font-size:13.5px;font-weight:600;color:var(--text);">Servidor do bot offline</div>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Execute <code style="background:var(--border);padding:2px 6px;border-radius:4px;font-size:12px;">cd whatsapp-bot && node index.js</code> no servidor.</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($qrExiste ?? false)
                    <div style="text-align:center;margin-bottom:20px;">
                        <label class="form-label" style="justify-content:center;margin-bottom:12px;">Escaneie o QR Code para conectar</label>
                        <div style="display:inline-block;background:white;padding:12px;border-radius:14px;border:2px solid var(--border-strong);">
                            <img src="http://localhost:3000/qr?t={{ time() }}" style="max-width:220px;display:block;" alt="QR Code WhatsApp">
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:12px;">
                            <svg class="icon icon-sm" style="vertical-align:middle;"><use href="#i-whatsapp"/></svg>
                            Abra o WhatsApp no celular &rarr; Dispositivos Conectados &rarr; Conectar &rarr; Escaneie este QR
                        </div>
                    </div>
                    @endif

                    @if(!$botAuthenticated ?? true)
                    <div>
                        <label class="form-label" style="margin-bottom:8px;">Ou conecte por código de pareamento</label>
                        <div style="display:flex;gap:10px;">
                            <input type="text" name="phone" class="form-input" placeholder="Ex: 558799999999" style="flex:1;" form="pair-form">
                            <button type="submit" class="btn-primary-c" style="white-space:nowrap;" form="pair-form">Conectar</button>
                        </div>
                        <div class="form-hint">Digite o número do WhatsApp com DDD e código do país (ex: 55 para Brasil)</div>
                    </div>

                    @if(session('pairing_code'))
                    <div style="margin-top:16px;padding:16px;background:var(--bg);border:1px solid var(--border-strong);border-radius:12px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:8px;">Código de pareamento:</div>
                        <div style="font-size:28px;letter-spacing:6px;font-weight:800;text-align:center;padding:12px;background:var(--card-solid);border-radius:10px;font-family:monospace;color:var(--accent);">{{ session('pairing_code') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:10px;">Abra WhatsApp &gt; Dispositivos Conectados &gt; Conectar Dispositivo &gt; "Conectar com número de telefone" e digite o código acima</div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div style="margin-top:16px;padding:12px 16px;background:var(--danger-bg);border:1px solid rgba(248,113,113,0.2);border-radius:10px;color:var(--danger);font-size:13px;font-weight:500;">{{ session('error') }}</div>
                    @endif
                    @endif

                    <input type="hidden" name="whatsapp_bot_token" value="{{ $configuracoes['whatsapp_bot_token'] ?? '' }}">
                </div>
            </section>

            <section class="panel" id="sec-notificacoes">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-bell-ring"/></svg></div>
                        <div>
                            <h2 class="panel-title">Notificações</h2>
                            <div class="panel-subtitle">Configure os alertas do sistema</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Notificações no painel</div>
                            <div class="d">Receba alertas visuais no painel quando um novo agendamento for realizado.</div>
                        </div>
                        <button type="button" class="switch on" data-setting="notificacoes_painel" data-input="notificacoes_painel"></button>
                        <input type="hidden" name="notificacoes_painel" id="notificacoes_painel" value="1">
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Lembretes por e-mail</div>
                            <div class="d">Envia lembretes automáticos de agendamento para os clientes por e-mail (1h, 30min e 15min antes).</div>
                        </div>
                        <button type="button" class="switch on" data-setting="lembretes_email" data-input="lembretes_email"></button>
                        <input type="hidden" name="lembretes_email" id="lembretes_email" value="1">
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Lembrete diário da agenda</div>
                            <div class="d">Resumo da agenda do dia enviado todo dia às 8h no WhatsApp.</div>
                        </div>
                        <button type="button" class="switch" data-setting="resumo_diario" data-input="resumo_diario"></button>
                        <input type="hidden" name="resumo_diario" id="resumo_diario" value="0">
                    </div>
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="t">Notificações de cancelamento</div>
                            <div class="d">Seja notificado imediatamente quando um agendamento for cancelado.</div>
                        </div>
                        <button type="button" class="switch on" data-setting="cancelamento_notif" data-input="cancelamento_notif"></button>
                        <input type="hidden" name="cancelamento_notif" id="cancelamento_notif" value="1">
                    </div>
                </div>
            </section>

            <section class="panel" id="sec-financeiro">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-credit-card"/></svg></div>
                        <div>
                            <h2 class="panel-title">Financeiro</h2>
                            <div class="panel-subtitle">Configurações de pagamento e tributos</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Método de pagamento padrão</label>
                            <select name="metodo_pagamento_padrao" class="form-select">
                                <option value="dinheiro" {{ ($configuracoes['metodo_pagamento_padrao'] ?? '') == 'dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="pix" {{ ($configuracoes['metodo_pagamento_padrao'] ?? '') == 'pix' ? 'selected' : '' }}>Pix</option>
                                <option value="cartao_credito" {{ ($configuracoes['metodo_pagamento_padrao'] ?? '') == 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="cartao_debito" {{ ($configuracoes['metodo_pagamento_padrao'] ?? '') == 'cartao_debito' ? 'selected' : '' }}>Cartão de Débito</option>
                                <option value="debito" {{ ($configuracoes['metodo_pagamento_padrao'] ?? '') == 'debito' ? 'selected' : '' }}>Débito</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Taxa de serviço (%)</label>
                            <input type="number" name="taxa_servico" class="form-input" step="0.01" min="0" max="100" value="{{ $configuracoes['taxa_servico'] ?? '0' }}">
                            <div class="form-hint">Percentual cobrado sobre cada serviço</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alíquota de impostos (%)</label>
                            <input type="number" name="aliquota_impostos" class="form-input" step="0.01" min="0" max="100" value="{{ $configuracoes['aliquota_impostos'] ?? '0' }}">
                            <div class="form-hint">Percentual de impostos sobre faturamento</div>
                        </div>
                        <div class="form-group">
                            <div class="toggle-row" style="padding:0;border:none;">
                                <div class="toggle-info">
                                    <div class="t">Emissão de Nota Fiscal</div>
                                    <div class="d">Habilitar emissão de NF-e para cada serviço realizado.</div>
                                </div>
                                <button type="button" class="switch {{ ($configuracoes['emissao_nf'] ?? false) ? 'on' : '' }}" data-setting="emissao_nf" data-input="emissao_nf"></button>
                                <input type="hidden" name="emissao_nf" id="emissao_nf" value="{{ ($configuracoes['emissao_nf'] ?? false) ? '1' : '0' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</form>

<form id="pair-form" action="{{ optional(request()->route('barbearia'))?->slug ? route('tenant.admin.configuracoes.pair', optional(request()->route('barbearia'))?->slug) : route('admin.configuracoes.index') }}" method="POST" style="display:none;">@csrf</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.switch').forEach(function(sw) {
    sw.addEventListener('click', function() {
        this.classList.toggle('on');
        var inputId = this.dataset.input;
        if (inputId) {
            document.getElementById(inputId).value = this.classList.contains('on') ? '1' : '0';
        }
    });
});

var navItems = document.querySelectorAll('.settings-nav-item');
var sections = document.querySelectorAll('.panel');
var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
        if (entry.isIntersecting) {
            navItems.forEach(function(nav) {
                nav.classList.toggle('active', nav.dataset.target === entry.target.id);
            });
        }
    });
}, { rootMargin: '-20% 0px -70% 0px' });
sections.forEach(function(sec) { observer.observe(sec); });
navItems.forEach(function(nav) {
    nav.addEventListener('click', function() {
        var target = document.getElementById(nav.dataset.target);
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

@if(!($botAuthenticated ?? false))
setTimeout(function() { location.reload(); }, 8000);
@endif
</script>
@endpush
