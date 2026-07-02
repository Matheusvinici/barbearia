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
        <div class="brand-mark">
            @if($__tenant?->logo)
            <img src="{{ $__tenant->logo_url }}" alt="Logo" style="width:42px;height:42px;border-radius:13px;object-fit:cover;">
            @else
            SB
            @endif
        </div>
        <div class="brand-text">
            <strong>{{ $__tenant?->nome ?? 'Barber Control' }}</strong>
            <span>{{ $__tenant ? 'Administração' : 'Pro' }}</span>
        </div>
    </div>
    <div class="sidebar-nav-scroll">
    @include('layouts.navigation')
    </div>
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
(function() {
    var state = {
        idsVistos: {},
        primeiraCarga: true,
        ultimoSomMs: 0,
        somTocando: false,
        notifPerm: false,
        modalTimeout: null,
    };

    function detalhesUrl(dados) {
        var path = window.location.pathname;
        if (path.indexOf('/barbeiro/') !== -1) {
            var match = path.match(/^\/([^\/]+)\//);
            var slug = match && match[1] !== 'barbeiro' ? match[1] : null;
            return slug ? '/' + slug + '/barbeiro/agendamentos' : '/barbeiro/agendamentos';
        }
        return dados.url || '#';
    }

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            state.notifPerm = true;
        } else if (Notification.permission === 'default') {
            Notification.requestPermission().then(function(p) { state.notifPerm = p === 'granted'; });
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') fecharModal();
    });

    function fecharModal() {
        var el = document.getElementById('notif-modal-agendamento');
        if (el) el.remove();
        if (state.modalTimeout) { clearTimeout(state.modalTimeout); state.modalTimeout = null; }
    }

    function tocarAlarme() {
        var agora = Date.now();
        if (state.somTocando || (agora - state.ultimoSomMs) < 300000) return;
        state.ultimoSomMs = agora;
        state.somTocando = true;
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var n = ctx.currentTime;
            [
                { f: 880, t: 0 },
                { f: 1100, t: 0.25 },
                { f: 880, t: 0.5 },
            ].forEach(function(t) {
                var o = ctx.createOscillator();
                var g = ctx.createGain();
                o.type = 'triangle'; o.frequency.value = t.f;
                g.gain.setValueAtTime(0, n + t.t);
                g.gain.linearRampToValueAtTime(0.25, n + t.t + 0.02);
                g.gain.exponentialRampToValueAtTime(0.001, n + t.t + 1.5);
                o.connect(g); g.connect(ctx.destination);
                o.start(n + t.t); o.stop(n + t.t + 1.6);
            });
            setTimeout(function() { ctx.close(); state.somTocando = false; }, 3000);
        } catch (e) { state.somTocando = false; }
    }

    function mostrarNotifDesktop(titulo, msg, url, dados) {
        if (!state.notifPerm) return;
        try {
            var n = new Notification(titulo, { body: msg, icon: '/images/logo.jpg', tag: 'novo-agendamento' });
            var link = detalhesUrl(dados);
            if (link) n.onclick = function() { window.focus(); window.location.href = link; };
            setTimeout(function() { n.close(); }, 8000);
        } catch (e) {}
    }

    function marcarLida(id) {
        fetch('/notificacoes/' + id + '/marcar-lida', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, credentials: 'same-origin' }).catch(function(){});
        document.querySelectorAll('#notif-count').forEach(function(el) {
            var v = parseInt(el.textContent) || 0;
            if (v > 0) el.textContent = v - 1;
        });
    }

    function mostrarModal(dados) {
        marcarLida(dados.id);
        var servicos = '—';
        if (dados.servicos) {
            try {
                var arr = typeof dados.servicos === 'string' ? JSON.parse(dados.servicos) : dados.servicos;
                servicos = Array.isArray(arr) ? arr.join(', ') : '—';
            } catch (e) {}
        }
        var total = dados.total ? 'R$ ' + parseFloat(dados.total).toFixed(2).replace('.', ',') : '—';

        var m = document.createElement('div');
        m.id = 'notif-modal-agendamento';
        m.innerHTML = [
            '<div class="notif-overlay">',
            '<div class="notif-backdrop" onclick="document.getElementById(\'notif-modal-agendamento\').remove()"></div>',
            '<div class="notif-card">',
            '<div class="notif-header">',
            '<div class="notif-bell"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>',
            '<h3>Novo Agendamento!</h3>',
            '</div>',
            '<div class="notif-body">',
            '<div class="notif-row"><span class="notif-label">Cliente</span><span class="notif-value">' + (dados.cliente_nome || '—') + '</span></div>',
            '<div class="notif-row"><span class="notif-label">Telefone</span><span class="notif-value">' + (dados.cliente_telefone || '—') + '</span></div>',
            '<div class="notif-row"><span class="notif-label">Barbeiro</span><span class="notif-value">' + (dados.barbeiro_nome || '—') + '</span></div>',
            '<div class="notif-row"><span class="notif-label">Horário</span><span class="notif-value">' + (dados.data || '') + ' às ' + (dados.hora_inicio || '') + '</span></div>',
            '<div class="notif-row"><span class="notif-label">Serviços</span><span class="notif-value">' + servicos + '</span></div>',
            '<div class="notif-row notif-total-row"><span class="notif-label">Total</span><span class="notif-value">' + total + '</span></div>',
            '</div>',
            '<div class="notif-footer">',
            '<button class="notif-btn notif-btn-secondary" onclick="this.closest(\'.notif-overlay\').remove()">Fechar</button>',
            '<a href="' + detalhesUrl(dados) + '" class="notif-btn notif-btn-primary">Ver Detalhes</a>',
            '</div>',
            '</div>',
            '</div>',
        ].join('');
        document.body.appendChild(m);

        if (state.modalTimeout) clearTimeout(state.modalTimeout);
        state.modalTimeout = setTimeout(function() {
            var el = document.getElementById('notif-modal-agendamento');
            if (el) el.remove();
        }, 15000);
    }

    function carregarNotificacoes() {
        try {
            var isAuth = {{ Auth::guard('web')->check() || Auth::guard('barbeiro')->check() ? 'true' : 'false' }};
            if (!isAuth) return;

            fetch('/notificacoes', { credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    document.querySelectorAll('#notif-count').forEach(function(el) {
                        el.textContent = data.nao_lidas || '';
                    });

                    data.notificacoes.forEach(function(n) {
                        if (!n.id || state.idsVistos[n.id]) return;
                        state.idsVistos[n.id] = true;

                        if (!n.lida && n.title && n.title.indexOf('Novo agendamento') !== -1) {
                            var isRecente = n.created_at && (Date.now() - new Date(n.created_at).getTime()) < 30000;
                            if (!state.primeiraCarga || isRecente) {
                                tocarAlarme();
                                mostrarNotifDesktop(n.title, n.message, n.url, n);
                                mostrarModal(n);
                            }
                        }
                    });

                    state.primeiraCarga = false;
                })
                .catch(function() {});
        } catch (e) {}
    }

    carregarNotificacoes();
    setInterval(carregarNotificacoes, 15000);
})();
</script>

<style>
.notif-overlay {
    position: fixed; inset: 0; z-index: 99999;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.notif-backdrop {
    position: absolute; inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.notif-card {
    position: relative;
    background: #fff; border-radius: 20px;
    width: 420px; max-width: 92vw;
    box-shadow: 0 32px 64px rgba(0,0,0,0.3);
    animation: notifSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}
@keyframes notifSlideIn {
    from { opacity: 0; transform: scale(0.85) translateY(30px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.notif-header {
    text-align: center; padding: 28px 24px 16px;
    background: linear-gradient(135deg, #1a1a2e, #16213e);
    color: #fff;
}
.notif-bell {
    width: 64px; height: 64px; margin: 0 auto 12px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    animation: notifBellRing 0.6s ease-in-out;
}
@keyframes notifBellRing {
    0%, 100% { transform: rotate(0); }
    25% { transform: rotate(-15deg); }
    50% { transform: rotate(15deg); }
    75% { transform: rotate(-10deg); }
}
.notif-header h3 {
    margin: 0; font-size: 20px; font-weight: 700;
}
.notif-body {
    padding: 16px 24px;
}
.notif-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px solid #f0f0f0;
}
.notif-row:last-child { border-bottom: none; }
.notif-label {
    font-size: 13px; color: #888; font-weight: 500;
}
.notif-value {
    font-size: 14px; color: #222; font-weight: 600; text-align: right;
}
.notif-total-row .notif-value {
    font-size: 18px; color: #22c55e; font-weight: 800;
}
.notif-footer {
    display: flex; gap: 10px; padding: 16px 24px 24px;
}
.notif-btn {
    flex: 1; padding: 12px; border-radius: 12px; border: none;
    font-size: 14px; font-weight: 600; cursor: pointer;
    text-decoration: none; text-align: center;
    transition: all 0.2s;
}
.notif-btn-primary {
    background: #22c55e; color: #fff;
}
.notif-btn-primary:hover {
    background: #16a34a; color: #fff;
}
.notif-btn-secondary {
    background: #f3f4f6; color: #555;
}
.notif-btn-secondary:hover {
    background: #e5e7eb; color: #333;
}
</style>

@stack('scripts')
@stack('modals')
</body>
</html>
