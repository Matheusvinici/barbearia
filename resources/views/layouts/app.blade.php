@php
    use App\Models\Configuracao;
    $__tenant = request()->route('barbearia');
    $__tenantSlug = $__tenant ? $__tenant->slug : null;
    $__guard = Auth::guard('web')->check() ? 'web' : 'barbeiro';
    $__userName = Auth::guard('web')->user()?->name ?? Auth::guard('barbeiro')->user()?->nome ?? '';
    $__userInitial = mb_substr($__userName, 0, 1, 'UTF-8');
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', $__tenant?->nome ?? Configuracao::get('nome_barbearia', 'Barber Control')) — Barber Control Pro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@livewireStyles
@stack('styles')
</head>
<body>
<div class="sidebar">
    <div class="brand">
        <div class="brand-mark">SB</div>
        <div class="brand-text">
            <strong>{{ $__tenant?->nome ?? 'Barber Control' }}</strong>
            <span>{{ $__tenant ? 'Administração' : 'Pro' }}</span>
        </div>
    </div>
    @include('layouts.navigation')
    <div class="sidebar-footer">
        <div class="user-card" onclick="document.getElementById('user-dropdown').classList.toggle('show')">
            <div class="user-avatar">{{ $__userInitial }}</div>
            <div class="user-info">
                <strong>{{ $__userName }}</strong>
                <span>{{ $__guard === 'web' ? 'Admin' : 'Barbeiro' }}</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>
        <div id="user-dropdown" style="display:none; padding-top: 8px;">
            @php
                $profileRoute = $__tenantSlug
                    ? ($__guard === 'web' ? route('tenant.admin.dashboard', $__tenantSlug) : route('tenant.barbeiro.dashboard', $__tenantSlug))
                    : route('admin.dashboard');
            @endphp
            <a href="{{ $profileRoute }}" class="nav-item" style="font-size:13px;">Dashboard</a>
            <form method="POST" action="{{ $__tenantSlug ? ($__guard === 'web' ? route('tenant.logout', $__tenantSlug) : route('tenant.barbeiro.logout', $__tenantSlug)) : ($__guard === 'web' ? route('logout') : route('barbeiro.logout')) }}">
                @csrf
                <button type="submit" class="nav-item" style="font-size:13px; background:none; border:none; width:100%; text-align:left; font-family:inherit;">Sair</button>
            </form>
        </div>
    </div>
</div>

<div class="main">
    <div class="content">
        @hasSection('topbar')
            @yield('topbar')
        @else
        <div class="topbar">
            <div>
                @hasSection('breadcrumb')
                <div class="breadcrumb-trail">@yield('breadcrumb')</div>
                @endif
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                <div class="page-subtitle">@yield('subtitle')</div>
                @endif
            </div>
            <div class="topbar-actions">
                @yield('topbar-actions')
            </div>
        </div>
        @endif

        @include('layouts.partials.messages')

        @yield('content')
        {{ $slot ?? '' }}
    </div>
</div>

@livewireScripts
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let idsVistos = {};
    let primeiraCarga = true;
    let ultimoSomMs = 0;
    let somTocando = false;
    let audioCtx = null;
    let notifPerm = Notification.permission === 'granted';
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(p => { notifPerm = p === 'granted'; });
    }
    function mostrarNotificacao(title, body, url) {
        if (!notifPerm) return;
        try {
            const n = new Notification(title, { body, icon: '/images/logo.jpg', tag: 'novo-agendamento' });
            if (url) n.onclick = () => { window.focus(); window.location.href = url; };
            setTimeout(() => n.close(), 8000);
        } catch (e) {}
    }
    function tocarAlarme() {
        const agora = Date.now();
        if (somTocando || agora - ultimoSomMs < 300000) return;
        ultimoSomMs = agora; somTocando = true;
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.connect(gain); gain.connect(audioCtx.destination);
            osc.frequency.value = 800; osc.type = 'sine'; gain.gain.value = 0.3;
            osc.start();
            setTimeout(() => { osc.stop(); audioCtx.close(); somTocando = false; }, 5000);
        } catch (e) { somTocando = false; }
    }
    function carregarNotificacoes() {
        @auth
        $.get('/notificacoes', function(data) {
            $('#notif-count').text(data.nao_lidas);
            data.notificacoes.forEach(function(n) {
                if (!n.id) return;
                if (!primeiraCarga && !n.lida && n.title && n.title.includes('Novo agendamento') && !idsVistos[n.id]) {
                    idsVistos[n.id] = true; tocarAlarme(); mostrarNotificacao(n.title, n.message, n.url);
                }
                idsVistos[n.id] = true;
            });
            primeiraCarga = false;
            let html = '';
            if (data.notificacoes.length === 0) {
                html = '<div class="text-center py-2 text-muted small">Nenhuma notificação</div>';
            } else {
                data.notificacoes.forEach(function(n) {
                    html += `<a href="${n.url || '#'}" class="dropdown-item ${n.lida ? '' : 'unread'}">
                        <div class="d-flex"><div class="me-3"><i class="${n.icon || 'fas fa-info-circle'}" style="color: ${n.color || '#6c757d'}"></i></div>
                        <div><strong>${n.title}</strong><br><small>${n.message}</small><br><small class="text-muted">${n.ago}</small></div></div></a>`;
                });
            }
            $('#notif-dropdown').html(html);
        });
        @endauth
    }
    carregarNotificacoes();
    setInterval(carregarNotificacoes, 15000);
});
</script>
@stack('scripts')
@stack('modals')
</body>
</html>
