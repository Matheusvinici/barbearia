<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Agende seu horário')</title>
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#f5b544">
<meta name="apple-mobile-web-app-capable" content="yes">
@livewireStyles
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
@stack('styles')
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
html { scroll-behavior: smooth; }
body {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    letter-spacing: -0.01em;
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
body::before {
    content: '';
    position: fixed; inset: 0;
    background:
      radial-gradient(900px 600px at 85% -10%, var(--accent-glow), transparent 60%),
      radial-gradient(700px 500px at 0% 110%, rgba(96, 165, 250, 0.05), transparent 60%);
    pointer-events: none; z-index: 0;
}
[data-bs-theme="light"] body::before {
    background:
      radial-gradient(900px 600px at 85% -10%, var(--accent-glow), transparent 60%),
      radial-gradient(700px 500px at 0% 110%, rgba(96, 165, 250, 0.05), transparent 60%);
}

.icon { width: 22px; height: 22px; display: inline-flex; flex-shrink: 0; }
.icon-sm { width: 18px; height: 18px; }
.icon-xs { width: 15px; height: 15px; }

.navbar-public {
    position: sticky; top: 0; z-index: 1000;
    background: rgba(13, 13, 18, 0.8);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border);
    padding: 16px 0;
}
[data-bs-theme="light"] .navbar-public { background: rgba(255, 255, 255, 0.85); }
.nav-container {
    max-width: 1100px; margin: 0 auto;
    padding: 0 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.brand-logo {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; color: var(--text);
    font-weight: 800; font-size: 18px; letter-spacing: -0.02em;
}
.brand-logo:hover { color: var(--text); }
.brand-mark {
    width: 38px; height: 38px; border-radius: 11px;
    background: linear-gradient(135deg, var(--accent), var(--accent-soft));
    display: grid; place-items: center; color: #0d0d12;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
}
.brand-logo span { color: var(--text-muted); font-weight: 500; }
.nav-actions { display: flex; align-items: center; gap: 12px; }
.nav-link-public {
    color: var(--text-muted); text-decoration: none;
    font-size: 14px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 6px;
}
.nav-link-public:hover { color: var(--accent); }
.theme-toggle-public {
    width: 38px; height: 38px; border-radius: 10px;
    border: 1px solid var(--border-strong);
    background: var(--card-solid);
    color: var(--text-muted);
    display: grid; place-items: center;
    cursor: pointer;
}
.theme-toggle-public:hover {
    color: var(--accent); border-color: var(--accent);
    background: var(--accent-glow);
}

.main-content {
    flex: 1;
    max-width: 1100px;
    width: 100%;
    margin: 0 auto;
    padding: 48px 24px 140px;
    position: relative;
    z-index: 1;
}

.steps {
    display: flex; align-items: center; justify-content: center;
    gap: 8px; margin-bottom: 48px; flex-wrap: wrap;
}
.step-item {
    display: flex; align-items: center; gap: 8px;
    color: var(--text-faint); font-size: 13px; font-weight: 600;
}
.step-num {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--card-solid); border: 1px solid var(--border-strong);
    display: grid; place-items: center; font-size: 12px; font-weight: 700;
}
.step-item.active { color: var(--text); }
.step-item.active .step-num {
    background: var(--accent); color: #0d0d12; border-color: var(--accent);
    box-shadow: 0 0 0 4px var(--accent-glow);
}
.step-item.done { color: var(--text-muted); }
.step-item.done .step-num {
    background: var(--success-bg); color: var(--success); border-color: var(--success);
}
.step-divider {
    width: 30px; height: 2px; background: var(--border-strong);
    border-radius: 2px; flex-shrink: 0;
}

.page-header { text-align: center; margin-bottom: 40px; }
.page-title { font-size: 36px; font-weight: 800; letter-spacing: -0.035em; line-height: 1.1; margin-bottom: 12px; }
.page-subtitle { font-size: 16px; color: var(--text-muted); max-width: 500px; margin: 0 auto; }

.card-base { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 24px; }
.card-hover { cursor: pointer; transition: all 250ms; position: relative; overflow: hidden; }
.card-hover:hover { border-color: var(--border-strong); transform: translateY(-3px); box-shadow: 0 12px 30px -10px rgba(0,0,0,0.3); }
.card-selected { border-color: var(--accent) !important; box-shadow: 0 0 0 1px var(--accent), 0 12px 30px -10px var(--accent-glow) !important; }
.check-circle {
    position: absolute; top: 16px; right: 16px;
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--bg); border: 2px solid var(--border-strong);
    display: grid; place-items: center; z-index: 2;
}
.check-circle svg { color: #0d0d12; opacity: 0; }
.card-selected .check-circle { background: var(--accent); border-color: var(--accent); }
.card-selected .check-circle svg { opacity: 1; }

.grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

.form-group { margin-bottom: 20px; }
.form-group:last-child { margin-bottom: 0; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
.input-group { position: relative; }
.input-group .addon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-faint); pointer-events: none; display: flex; align-items: center; }
.form-input {
    width: 100%; height: 48px; padding: 0 14px 0 44px;
    border-radius: 12px; border: 1px solid var(--border-strong);
    background: var(--bg); color: var(--text);
    font-family: inherit; font-size: 15px;
}
.form-input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); }
.form-input::placeholder { color: var(--text-faint); }

.action-bar {
    position: fixed; bottom: 0; left: 0; right: 0;
    background: rgba(13, 13, 18, 0.9);
    backdrop-filter: blur(20px);
    border-top: 1px solid var(--border);
    padding: 16px 0; z-index: 100;
    transform: translateY(100%);
    transition: transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1);
}
[data-bs-theme="light"] .action-bar { background: rgba(255, 255, 255, 0.9); }
.action-bar.show { transform: translateY(0); }
.action-container {
    max-width: 1100px; margin: 0 auto; padding: 0 24px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
}
.selected-info { display: flex; flex-direction: column; gap: 2px; }
.selected-info .label { font-size: 11px; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }
.selected-info .value { font-size: 14px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.selected-info .value .pipe { color: var(--text-faint); }

.btn-primary-c {
    height: 48px; padding: 0 28px;
    border-radius: 12px;
    background: var(--accent); color: #0d0d12;
    border: none; font-weight: 700; font-size: 15px;
    display: inline-flex; align-items: center; gap: 8px;
    cursor: pointer; font-family: inherit;
    box-shadow: 0 8px 22px -8px var(--accent-glow);
}
.btn-primary-c:hover { background: var(--accent-hover); transform: translateY(-1px); }
.btn-primary-c:disabled { background: var(--border-strong); color: var(--text-faint); box-shadow: none; cursor: not-allowed; transform: none; }

.btn-ghost-c {
    height: 48px; padding: 0 28px;
    border-radius: 12px;
    background: transparent; color: var(--text);
    border: 1px solid var(--border-strong);
    font-weight: 700; font-size: 15px;
    display: inline-flex; align-items: center; gap: 8px;
    cursor: pointer; font-family: inherit;
}
.btn-ghost-c:hover { border-color: var(--accent); background: var(--accent-glow); color: var(--accent); }

.toast-c {
    position: fixed; bottom: 90px; left: 50%; transform: translateX(-50%) translateY(150%);
    background: var(--card-solid); border: 1px solid var(--border-strong); border-radius: 12px;
    padding: 14px 18px; display: flex; align-items: center; gap: 12px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.3); z-index: 1000;
    max-width: 360px;
    pointer-events: none; opacity: 0;
    transition: transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1), opacity 300ms;
}
.toast-c.show { transform: translateX(-50%) translateY(0); pointer-events: auto; opacity: 1; }
.toast-c .ic { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; }
.toast-c .ic.success { background: var(--success-bg); color: var(--success); }
.toast-c strong { display: block; font-size: 13.5px; font-weight: 700; }
.toast-c span { font-size: 12.5px; color: var(--text-muted); }

.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; flex-shrink: 0; }
.panel-title { font-size: 16px; font-weight: 700; margin: 0; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }
.panel-body { padding: 24px; }

.summary-item { display: flex; align-items: center; gap: 14px; padding: 14px 0; border-bottom: 1px solid var(--border); }
.summary-item:last-of-type { border-bottom: none; }
.sum-ic { width: 38px; height: 38px; border-radius: 10px; background: var(--bg); color: var(--accent); display: grid; place-items: center; flex-shrink: 0; border: 1px solid var(--border); }
.sum-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.sum-info .lbl { font-size: 11px; color: var(--text-faint); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; }
.sum-info .val { font-size: 14.5px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.summary-total { margin-top: 20px; padding-top: 20px; border-top: 2px dashed var(--border-strong); display: flex; justify-content: space-between; align-items: center; }
.summary-total .lbl { font-size: 15px; font-weight: 600; color: var(--text); }
.summary-total .val { font-size: 26px; font-weight: 800; color: var(--accent); }
.summary-total .val .cur { font-size: 16px; font-weight: 600; margin-right: 2px; }

.welcome-header { text-align: center; padding: 20px 0 10px; }
.welcome-logo { max-width: 120px; max-height: 120px; object-fit: contain; margin-bottom: 16px; border-radius: 16px; }
.welcome-logo-placeholder { width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, var(--accent), var(--accent-soft)); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 32px; color: #0d0d12; box-shadow: 0 8px 22px -8px var(--accent-glow); }
.welcome-title { font-size: 28px; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 8px; }
.welcome-address { font-size: 14px; color: var(--text-muted); margin: 0; }

@keyframes pulse-dot { 0% { box-shadow: 0 0 0 0 currentColor; } 70% { box-shadow: 0 0 0 5px transparent; } 100% { box-shadow: 0 0 0 0 transparent; } }

@media (max-width: 768px) {
    .main-content { padding: 32px 16px 120px; }
    .page-title { font-size: 28px; }
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
    .steps { gap: 4px; }
    .step-divider { width: 20px; }
    .action-container { flex-direction: column; align-items: stretch; gap: 12px; }
    .btn-primary-c, .btn-ghost-c { width: 100%; justify-content: center; }
    .selected-info { text-align: center; }
}
@media (max-width: 992px) and (min-width: 769px) {
    .grid-3 { grid-template-columns: repeat(2, 1fr); }
}
</style>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="i-scissor" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M8.12 7.88L20 18M8.12 16.12L20 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24" fill="none"><path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-left" viewBox="0 0 24 24" fill="none"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M13 18l6-6-6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-moon" viewBox="0 0 24 24" fill="none"><path d="M3.27 12.31c.43 4.6 4.34 8.21 8.95 8.41 3.16.13 5.97-1.18 7.86-3.34.62-.71.27-1.32-.69-1.21-.55.06-1.11.04-1.69-.06-3.58-.6-6.32-3.45-6.65-7.06-.12-1.34.07-2.62.5-3.79.34-.92-.31-1.39-1.22-1.04-4.21 1.61-7.04 5.71-6.69 10.09z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-whatsapp" viewBox="0 0 24 24" fill="none"><path d="M3 21l1.9-5.7A8.5 8.5 0 1 1 12 20.5a8.4 8.4 0 0 1-4.5-1.3L3 21z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5.6 0 1-.5 1-1l-.2-1.2-1.8.4-.8-.8c-.5-.5-1-1.3-1.3-1.8l.4-1.8L11 8.5c0-.6-.5-1-1-1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24" fill="none"><circle cx="11.5" cy="11.5" r="8.5" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
    <symbol id="i-map-pin" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.6"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7.5V12l3 1.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-star" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7l3-7z"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24" fill="none"><path d="M8 2v3M16 2v3M3.5 9.09h17M22 19c0 .75-.21 1.46-.58 2.06a3.42 3.42 0 0 1-2.91 1.64H5.49C3.26 22.7 1.7 21.07 1.7 19V8.06c0-2.13 1.56-3.79 3.79-3.79h13.02c2.13 0 3.79 1.66 3.79 3.79V16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-building" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V5c0-1 .5-2 2-2h10c1.5 0 2 1 2 2v16M9 7h2M9 11h2M9 15h2M13 7h2M13 11h2M13 15h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-user-tag" viewBox="0 0 24 24" fill="none"><path d="M13 20.5H6.5c-1.5 0-2.5-1-2.5-2.5 0-3.5 3-5.5 6-5.5.83 0 1.63.13 2.36.37" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="10" cy="6.5" r="3.5" stroke="currentColor" stroke-width="1.6"/><path d="M17.13 17.92l2.32 2.32c.21.21.55.21.76 0l1.55-1.55c.21-.21.21-.55 0-.76l-2.32-2.32a.54.54 0 0 1-.16-.38v-2.18c0-.29-.24-.53-.53-.53h-2.18a.54.54 0 0 1-.38-.16L14 9.95c-.18-.18-.49-.18-.67 0l-1.55 1.55c-.18.18-.18.49 0 .67l1.65 1.65c.1.1.16.24.16.38v2.18c0 .29.24.53.53.53h2.18c.14 0 .28.06.38.16z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-scissors-2" viewBox="0 0 24 24" fill="none"><circle cx="6" cy="6" r="3" stroke="currentColor" stroke-width="1.6"/><circle cx="6" cy="18" r="3" stroke="currentColor" stroke-width="1.6"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24" fill="none"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
  </defs>
</svg>

<nav class="navbar-public">
  <div class="nav-container">
    @php
        $__barbearia = request()->route('barbearia');
        $__navSlug = $__barbearia ? $__barbearia->slug : null;
        $__navNome = $__barbearia?->nome ?? \App\Models\Configuracao::get('nome_barbearia', 'Studio Barber');
        $__navLogo = $__barbearia?->logo_url ?? null;
    @endphp
    <a href="{{ $__navSlug ? route('tenant.site.agendar', $__navSlug) : route('site.agendar') }}" class="brand-logo">
      <div class="brand-mark"><svg class="icon" style="width:20px;height:20px"><use href="#i-scissor"/></svg></div>
      {{ $__navNome }} <span>Pro</span>
    </a>
    <div class="nav-actions">
      <a href="{{ $__navSlug ? route('tenant.site.meus-agendamentos', $__navSlug) : route('site.meus-agendamentos') }}" class="nav-link-public">
        <svg class="icon icon-sm"><use href="#i-user"/></svg>
        Meus Agendamentos
      </a>
      <button class="theme-toggle-public" id="themeToggle" title="Alternar tema">
        <svg class="icon"><use href="#i-sun"/></svg>
      </button>
    </div>
  </div>
</nav>

<main class="main-content">
    {{ $slot ?? '' }}
    @yield('content')
</main>

<div class="toast-c" id="toast">
  <div class="ic success"><svg class="icon"><use href="#i-check"/></svg></div>
  <div>
    <strong id="toastTitle">Tudo certo</strong>
    <span id="toastMsg">Ação executada com sucesso.</span>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
<script>
/* ============ Theme Toggle ============ */
(function(){
    var themeToggle = document.getElementById('themeToggle');
    var html = document.documentElement;
    if (!themeToggle) return;
    themeToggle.addEventListener('click', function(){
        var isDark = html.getAttribute('data-bs-theme') === 'dark';
        html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
        themeToggle.innerHTML = isDark
            ? '<svg class="icon"><use href="#i-moon"/></svg>'
            : '<svg class="icon"><use href="#i-sun"/></svg>';
    });
})();

/* ============ Toast ============ */
var toast = document.getElementById('toast');
var toastTimer;
function showToast(title, msg) {
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){ toast.classList.remove('show'); }, 3000);
}

/* ============ Notification Permission ============ */
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(function(e) {
        console.warn('SW registration failed', e);
    });
}
</script>
@stack('scripts')
</body>
</html>