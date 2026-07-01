@php
    $tenantName = $barbearia->nome ?? 'Barber Control';
    $tenantLogo = $barbearia->logo_url ?? asset('images/logo.jpg');
    $tenantBg = $barbearia->background_url ?? '';
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $tenantName }} — Agendar</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
<style>
:root {
    --bg: #0d0d12;
    --bg-elevated: #14141b;
    --card: rgba(20, 20, 27, 0.65);
    --card-solid: #16161e;
    --border: rgba(255, 255, 255, 0.06);
    --border-strong: rgba(255, 255, 255, 0.11);
    --text: #f4f4f6;
    --text-muted: #8a8a94;
    --text-faint: #55555f;
    --accent: #f5b544;
    --accent-hover: #ffc554;
    --accent-soft: #e89538;
    --accent-glow: rgba(245, 181, 68, 0.16);
    --success: #4ade80;
    --success-bg: rgba(74, 222, 128, 0.12);
    --danger: #f87171;
    --danger-bg: rgba(248, 113, 113, 0.12);
    --info: #60a5fa;
    --info-bg: rgba(96, 165, 250, 0.12);
    --r-sm: 10px; --r-md: 14px; --r-lg: 18px; --r-xl: 22px;
}
[data-bs-theme="light"] {
    --bg: #f6f6f8;
    --bg-elevated: #ffffff;
    --card: rgba(255, 255, 255, 0.75);
    --card-solid: #ffffff;
    --border: rgba(0, 0, 0, 0.07);
    --border-strong: rgba(0, 0, 0, 0.12);
    --text: #14141a;
    --text-muted: #6b6b75;
    --text-faint: #a0a0aa;
    --accent: #c47a06;
    --accent-hover: #d97706;
    --accent-soft: #b45309;
    --accent-glow: rgba(217, 119, 6, 0.12);
    --success: #16a34a;
    --success-bg: rgba(22, 163, 74, 0.1);
    --danger: #dc2626;
    --danger-bg: rgba(220, 38, 38, 0.1);
    --info: #2563eb;
    --info-bg: rgba(37, 99, 235, 0.1);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    letter-spacing: -0.01em;
    overflow-x: hidden;
}
.icon { width: 22px; height: 22px; display: inline-flex; flex-shrink: 0; }
.icon-sm { width: 18px; height: 18px; }
.login-wrapper { display: grid; grid-template-columns: 1.1fr 1fr; min-height: 100vh; }
.brand-panel {
    position: relative;
    background: radial-gradient(circle at 80% 0%, var(--accent-glow), transparent 50%), radial-gradient(circle at 0% 100%, rgba(96, 165, 250, 0.05), transparent 50%), linear-gradient(135deg, var(--bg-elevated) 0%, var(--bg) 100%);
    padding: 48px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    border-right: 1px solid var(--border);
}
.brand-panel::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 700px;
    height: 700px;
    border-radius: 50%;
    background: radial-gradient(circle, var(--accent-glow), transparent 60%);
    pointer-events: none;
    opacity: 0.6;
}
.brand-panel::after {
    content: '';
    position: absolute;
    bottom: -20%;
    left: -10%;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(96, 165, 250, 0.04), transparent 60%);
    pointer-events: none;
}
.brand-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 2;
    position: relative;
}
.brand-mark {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    background: linear-gradient(135deg, var(--accent), var(--accent-soft));
    display: grid;
    place-items: center;
    color: #0d0d12;
    font-weight: 800;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
    flex-shrink: 0;
}
.brand-name { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
.brand-name span { color: var(--text-muted); font-weight: 500; }
.brand-content { z-index: 2; position: relative; max-width: 480px; }
.brand-title { font-size: 40px; font-weight: 800; letter-spacing: -0.035em; margin-bottom: 18px; line-height: 1.15; }
.brand-subtitle { font-size: 15px; color: var(--text-muted); line-height: 1.6; margin-bottom: 40px; }
.feature-list { display: flex; flex-direction: column; gap: 18px; }
.feature-item { display: flex; align-items: center; gap: 14px; font-size: 14.5px; color: var(--text); font-weight: 500; }
.feature-ic { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; flex-shrink: 0; border: 1px solid var(--border-strong); }
.brand-footer { z-index: 2; position: relative; font-size: 12.5px; color: var(--text-faint); display: flex; align-items: center; gap: 6px; }
.form-panel {
    background: var(--bg);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px;
    position: relative;
}
.theme-toggle-login {
    position: absolute;
    top: 24px;
    right: 24px;
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--border-strong);
    background: var(--card-solid);
    color: var(--text-muted);
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 180ms;
}
.theme-toggle-login:hover { color: var(--text); border-color: var(--accent); transform: translateY(-1px); }
.login-card { width: 100%; max-width: 420px; }
.login-header { margin-bottom: 32px; text-align: center; }
.login-header img { max-height: 60px; margin-bottom: 16px; object-fit: contain; }
.login-title { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 8px; }
.login-subtitle { font-size: 14.5px; color: var(--text-muted); }
.form-group { margin-bottom: 18px; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
.input-group { position: relative; }
.input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-faint); pointer-events: none; display: flex; align-items: center; }
.form-input {
    width: 100%;
    height: 48px;
    padding: 0 14px 0 44px;
    border-radius: 12px;
    border: 1px solid var(--border-strong);
    background: var(--card-solid);
    color: var(--text);
    font-family: inherit;
    font-size: 15px;
    transition: all 180ms;
}
.form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }
.form-input::placeholder { color: var(--text-faint); }
.form-options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.checkbox-wrap { display: flex; align-items: center; gap: 8px; cursor: pointer; user-select: none; }
.custom-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    border: 1.5px solid var(--border-strong);
    background: var(--card-solid);
    display: grid;
    place-items: center;
    transition: all 150ms;
}
.checkbox-wrap input { display: none; }
.checkbox-wrap input:checked + .custom-checkbox { background: var(--accent); border-color: var(--accent); }
.checkbox-wrap input:checked + .custom-checkbox svg { opacity: 1; }
.custom-checkbox svg { width: 12px; height: 12px; color: #0d0d12; opacity: 0; transition: opacity 150ms; }
.checkbox-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
.btn-login {
    width: 100%;
    height: 48px;
    border-radius: 12px;
    background: var(--accent);
    color: #0d0d12;
    border: none;
    font-weight: 700;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 180ms;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
    font-family: inherit;
}
.btn-login:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 12px 28px -8px var(--accent-glow); }
.alert-error {
    background: var(--danger-bg);
    color: var(--danger);
    border: 1px solid rgba(248, 113, 113, 0.2);
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 20px;
}
.login-footer { text-align: center; margin-top: 24px; }
.login-footer a { color: var(--text-muted); font-size: 13.5px; text-decoration: none; transition: color 150ms; }
.login-footer a:hover { color: var(--accent); }
.login-footer .sep { color: var(--text-faint); margin: 0 10px; }
@media (max-width: 992px) {
    .login-wrapper { grid-template-columns: 1fr; }
    .brand-panel { display: none; }
}
@media (max-width: 480px) {
    .form-panel { padding: 24px 20px; }
    .login-title { font-size: 24px; }
}
</style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-scissor" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-wallet" viewBox="0 0 24 24" fill="none"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-chart" viewBox="0 0 24 24" fill="none"><path d="M3 22h18M5.6 18V9M10.6 18V5M15.6 18v-7M20.6 18V8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-mail" viewBox="0 0 24 24" fill="none"><rect x="2" y="4.5" width="20" height="15" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M3 6l9 7 9-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-lock" viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="3" stroke="currentColor" stroke-width="1.6"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M12 15v2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 18l6-6-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24" fill="none"><path d="M3.27 12.31c.43 4.6 4.34 8.21 8.95 8.41 3.16.13 5.97-1.18 7.86-3.34.62-.71.27-1.32-.69-1.21-.55.06-1.11.04-1.69-.06-3.58-.6-6.32-3.45-6.65-7.06-.12-1.34.07-2.62.5-3.79.34-.92-.31-1.39-1.22-1.04-4.21 1.61-7.04 5.71-6.69 10.09z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24" fill="none"><path d="M20 21c0-3.5-3-6-7-6s-7 2.5-7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="13" cy="8" r="4" stroke="currentColor" stroke-width="1.6"/></symbol>
    <symbol id="i-user-tie" viewBox="0 0 24 24" fill="none"><path d="M13 20.5H6.5c-1.5 0-2.5-1-2.5-2.5 0-3.5 3-5.5 6-5.5.83 0 1.63.13 2.36.37" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="6.5" r="3.5" stroke="currentColor" stroke-width="1.6"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24" fill="none"><path d="M12 2L4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5l-8-3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
  </defs>
</svg>

<div class="login-wrapper">
  <aside class="brand-panel">
    <div class="brand-logo">
      @if($tenantLogo)
        <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}" style="max-height:44px;max-width:200px;object-fit:contain;">
      @else
        <div class="brand-mark"><svg class="icon" style="width:22px;height:22px"><use href="#i-scissor"/></svg></div>
        <div class="brand-name">{{ $tenantName }}</div>
      @endif
    </div>
    <div class="brand-content">
      <h1 class="brand-title">Agende seu horário com praticidade.</h1>
      <p class="brand-subtitle">Escolha o barbeiro, o serviço e o horário ideal. Receba lembretes automáticos e nunca perca seu corte.</p>
      <div class="feature-list">
        <div class="feature-item"><div class="feature-ic"><svg class="icon icon-sm"><use href="#i-calendar"/></svg></div><span>Agende online 24 horas por dia</span></div>
        <div class="feature-item"><div class="feature-ic"><svg class="icon icon-sm"><use href="#i-wallet"/></svg></div><span>Pagamento na barbearia ou online</span></div>
        <div class="feature-item"><div class="feature-ic"><svg class="icon icon-sm"><use href="#i-chart"/></svg></div><span>Lembretes via WhatsApp</span></div>
      </div>
    </div>
    <div class="brand-footer">
      <svg class="icon icon-sm"><use href="#i-lock"/></svg>
      <span>Conexão criptografada e segura &middot; LGPD</span>
    </div>
  </aside>

  <main class="form-panel">
    <button class="theme-toggle-login" id="themeToggle" title="Alternar tema">
      <svg class="icon"><use href="#i-sun"/></svg>
    </button>
    <div class="login-card">
      <div class="login-header">
        <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}">
        <h2 class="login-title">{{ $tenantName }}</h2>
        <p class="login-subtitle">Faça login para acessar seus agendamentos.</p>
      </div>

      @if($errors->any())
      <div class="alert-error">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('tenant.site.login.store', $barbearia->slug) }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Seu WhatsApp</label>
          <div class="input-group">
            <span class="input-icon"><svg class="icon icon-sm"><use href="#i-mail"/></svg></span>
            <input type="tel" name="telefone" class="form-input" placeholder="(11) 99999-8888" value="{{ old('telefone', session('telefone', '')) }}" required autofocus>
          </div>
        </div>
        <div class="form-group" id="nome-group" style="{{ old('novo') || session('novo') ? '' : 'display:none' }}">
          <label class="form-label">Seu nome</label>
          <div class="input-group">
            <span class="input-icon"><svg class="icon icon-sm"><use href="#i-user"/></svg></span>
            <input type="text" name="nome" class="form-input" placeholder="Como prefere ser chamado" value="{{ old('nome') }}">
          </div>
        </div>
        <button type="submit" class="btn-login">
          Entrar
          <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
        </button>
      </form>

      <p style="text-align:center;font-size:13px;color:var(--text-muted);margin-top:16px;">
        Ao entrar, você concorda em receber lembretes via WhatsApp.
      </p>

      <div class="login-footer">
        <a href="{{ route('tenant.site.agendar', $barbearia->slug) }}">Agendar sem login</a>
        <span class="sep">&middot;</span>
        <a href="{{ route('tenant.login', $barbearia->slug) }}" style="font-weight:600;"><svg class="icon icon-xs"><use href="#i-shield"/></svg> Administração</a>
        <br>
        <a href="{{ route('tenant.barbeiro.login', $barbearia->slug) }}" style="font-size:12.5px;"><svg class="icon icon-xs"><use href="#i-user-tie"/></svg> Área do Barbeiro</a>
      </div>
    </div>
  </main>
</div>

<script>
// Show name field when server indicates new client
const telefoneInput = document.querySelector('input[name="telefone"]');
const nomeGroup = document.getElementById('nome-group');
if (telefoneInput) {
  telefoneInput.addEventListener('blur', function() {
    if (this.value.replace(/\D/g,'').length >= 10 && nomeGroup.style.display === 'none') {
      nomeGroup.style.display = '';
      nomeGroup.querySelector('input').focus();
    }
  });
}
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;
if (localStorage.getItem('theme') === 'light') {
    html.setAttribute('data-bs-theme', 'light');
    themeToggle.innerHTML = '<svg class="icon"><use href="#i-moon"/></svg>';
}
themeToggle.addEventListener('click', () => {
    const isDark = html.getAttribute('data-bs-theme') === 'dark';
    const newTheme = isDark ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    themeToggle.innerHTML = isDark ? '<svg class="icon"><use href="#i-moon"/></svg>' : '<svg class="icon"><use href="#i-sun"/></svg>';
});
</script>
</body>
</html>
