<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-building" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V5c0-1 .5-2 2-2h10c1.5 0 2 1 2 2v16M9 7h2M9 11h2M9 15h2M13 7h2M13 11h2M13 15h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-whatsapp" viewBox="0 0 24 24" fill="none"><path d="M3 21l1.9-5.7A8.5 8.5 0 1 1 12 20.5a8.4 8.4 0 0 1-4.5-1.3L3 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5.6 0 1-.5 1-1l-.2-1.2-1.8.4-.8-.8c-.5-.5-1-1.3-1.3-1.8l.4-1.8L11 8.5c0-.6-.5-1-1-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-bell-ring" viewBox="0 0 24 24" fill="none"><path d="M20 10.5c0 4.5-3 8-7 9.5M4 10.5c0 4.5 3 8 7 9.5M12 3a4 4 0 0 0-4 4v3.5c0 1-.5 2-1 2.5h10c-.5-.5-1-1.5-1-2.5V7a4 4 0 0 0-4-4z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 3v1M9.5 20.5a2.5 2.5 0 0 0 5 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-credit-card" viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M2 10h20M6 15h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24" fill="none"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
    <symbol id="i-plug" viewBox="0 0 24 24" fill="none"><path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 0 1-12 0V8zM12 17v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-info" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-x" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24" fill="none"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7l3-7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
    <symbol id="i-shop" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7zM9 21V12h6v9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 7l2-4h14l2 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
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
<form id="settings-form" action="{{ $barbearia ? route('tenant.admin.configuracoes.update', $barbearia->slug) : route('admin.configuracoes.update') }}" method="POST">
    @csrf

    <div class="settings-grid fade-in d1">
        <nav class="settings-nav" id="settingsNav">
            <button type="button" class="settings-nav-item active" data-target="sec-horarios">
                <svg class="icon icon-sm"><use href="#i-sun"/></svg>Horários
            </button>
            <button type="button" class="settings-nav-item" data-target="sec-whatsapp">
                <svg class="icon icon-sm"><use href="#i-plug"/></svg>WhatsApp
            </button>
            <button type="button" class="settings-nav-item" data-target="sec-avaliacoes">
                <svg class="icon icon-sm"><use href="#i-star"/></svg>Avaliações
            </button>
        </nav>

        <div>
            <section class="panel" id="sec-horarios">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-sun"/></svg></div>
                        <div>
                            <h2 class="panel-title">Horários</h2>
                            <div class="panel-subtitle">Configure os horários de funcionamento de cada unidade</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    @forelse($barbearias as $b)
                    <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--r-md);padding:16px;margin-bottom:16px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                            @if($b->logo)
                            <img src="{{ $b->logo_url }}" alt="" style="width:36px;height:36px;border-radius:8px;object-fit:cover;">
                            @else
                            <div style="width:36px;height:36px;border-radius:8px;background:var(--accent-glow);color:var(--accent);display:grid;place-items:center;">
                                <svg class="icon icon-sm"><use href="#i-shop"/></svg>
                            </div>
                            @endif
                            <div>
                                <strong style="color:var(--text);font-size:14px;">{{ $b->nome }}</strong>
                                @if($b->parent_id)
                                <span style="font-size:11px;color:var(--text-muted);margin-left:6px;">Filial</span>
                                @else
                                <span style="font-size:11px;color:var(--accent);margin-left:6px;">Matriz</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-grid" style="margin-top:8px;">
                            <input type="hidden" name="barbearias[{{ $b->id }}][id]" value="{{ $b->id }}">
                            <div class="form-group">
                                <label class="form-label">Abertura</label>
                                <input type="time" name="barbearias[{{ $b->id }}][horario_abertura]" class="form-input" value="{{ $b->horario_abertura ?? '08:00' }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fechamento</label>
                                <input type="time" name="barbearias[{{ $b->id }}][horario_fechamento]" class="form-input" value="{{ $b->horario_fechamento ?? '18:00' }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Intervalo (min)</label>
                                <select name="barbearias[{{ $b->id }}][intervalo_minutos]" class="form-select">
                                    @foreach([15,20,30,40,45,60] as $int)
                                    <option value="{{ $int }}" {{ ($b->intervalo_minutos ?? 30) == $int ? 'selected' : '' }}>{{ $int }} min</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="grid-column:1/-1;">
                                <label class="form-label">Dias de funcionamento</label>
                                @php
                                    $dias = explode(',', $b->dias_funcionamento ?? '1,2,3,4,5,6');
                                    $diasSemana = [0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sab'];
                                @endphp
                                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px;">
                                    @foreach($diasSemana as $k => $v)
                                    <label style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text);cursor:pointer;">
                                        <input type="checkbox" class="dia-checkbox-{{ $b->id }}" value="{{ $k }}" {{ in_array((string)$k, $dias) ? 'checked' : '' }} style="accent-color:var(--accent);">
                                        {{ $v }}
                                    </label>
                                    @endforeach
                                </div>
                                <input type="hidden" name="barbearias[{{ $b->id }}][dias_funcionamento]" class="dias-hidden-{{ $b->id }}" value="{{ $b->dias_funcionamento ?? '1,2,3,4,5,6' }}">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p style="text-align:center;color:var(--text-muted);font-size:14px;padding:20px;">Nenhuma barbearia encontrada.</p>
                    @endforelse
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

            <section class="panel" id="sec-avaliacoes">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-star"/></svg></div>
                        <div>
                            <h2 class="panel-title">Avaliações</h2>
                            <div class="panel-subtitle">Veja e responda às avaliações dos clientes</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    @livewire('admin.avaliacoes-list', ['barbearia' => $barbearia], key('avaliacoes-list'))
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



// Salvar dias de funcionamento antes de enviar
document.getElementById('settings-form').addEventListener('submit', function() {
    var barbearias = @json($barbearias->pluck('id'));
    barbearias.forEach(function(id) {
        var dias = [];
        document.querySelectorAll('.dia-checkbox-' + id + ':checked').forEach(function(cb) {
            dias.push(cb.value);
        });
        var hidden = document.querySelector('.dias-hidden-' + id);
        if (hidden) hidden.value = dias.join(',');
    });
});

</script>
@endpush
