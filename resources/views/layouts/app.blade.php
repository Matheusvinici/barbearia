<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ Configuracao::get('nome_barbearia', 'Barbearia') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @livewireStyles
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <style>
        .sidebar-custom { background-color: #1a1a2e !important; }
        .sidebar-custom .nav-link { color: #e0e0e0 !important; border-radius: 8px; margin: 2px 8px; transition: all 0.3s; }
        .sidebar-custom .nav-link i { color: #e0e0e0 !important; font-size: 1.2rem; }
        .sidebar-custom .nav-link p { font-size: 0.9rem; font-weight: 400; }
        .sidebar-custom .nav-item:hover > .nav-link { background-color: #16213e; transform: translateX(3px); }
        .sidebar-custom .nav-link.active { background-color: #0f3460 !important; }
        .brand-area { background-color: #16213e; padding: 15px 0; border-bottom: 1px solid #0f3460; }
        .brand-text { font-size: 1.3rem; font-weight: 700; color: #e94560 !important; text-align: center; margin-top: 5px; }
        .brand-link { padding: 0; background: transparent !important; }
        .main-header { background-color: #ffffff !important; border-bottom: 1px solid #e5e7eb; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .notification-badge { position: absolute; top: 0; right: 0; font-size: 0.6rem; }
        .notifications-dropdown { width: 350px; max-height: 400px; overflow-y: auto; }
        .notifications-dropdown .dropdown-item { white-space: normal; border-bottom: 1px solid #f0f0f0; padding: 10px 15px; }
        .notifications-dropdown .dropdown-item:hover { background-color: #f8f9fa; }
        .notifications-dropdown .dropdown-item.unread { background-color: #e8f4fd; }
        .content-wrapper { background-color: #f4f6f9; }
        .card { border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .card-header { background-color: #fff; border-bottom: 1px solid #f0f0f0; }
        .btn-action { border-radius: 8px; padding: 0.25rem 0.75rem; font-size: 0.8rem; }
        .status-pendente { background-color: #fff3cd; color: #856404; }
        .status-confirmado { background-color: #cce5ff; color: #004085; }
        .status-realizado { background-color: #d4edda; color: #155724; }
        .status-cancelado { background-color: #f8d7da; color: #721c24; }
        .status-ausente { background-color: #e2e3e5; color: #383d41; }
        .badge-status { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link position-relative" data-bs-toggle="dropdown" href="#" role="button">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger rounded-pill notification-badge" id="notif-count">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notifications-dropdown" id="notif-dropdown">
                    <div class="text-center py-2 text-muted small">Nenhuma notificação</div>
                </div>
            </li>
            @endauth

            <li class="nav-item">
                <span class="nav-link user-name">
                    <i class="fas fa-user-circle me-2"></i>
                    @if (Auth::guard('web')->check())
                        {{ Auth::guard('web')->user()->name }}
                    @elseif (Auth::guard('barbeiro')->check())
                        {{ Auth::guard('barbeiro')->user()->nome }}
                    @endif
                </span>
            </li>

            <li class="nav-item">
                <form method="POST" action="{{ Auth::guard('web')->check() ? route('logout') : route('barbeiro.logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="nav-link btn btn-logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Sair
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar elevation-4 sidebar-custom">
        <div class="brand-area">
            <a href="{{ Auth::guard('web')->check() ? route('admin.dashboard') : route('barbeiro.dashboard') }}" class="brand-link text-center">
                <span class="brand-text">{{ Configuracao::get('nome_barbearia', 'Barbearia') }}</span>
            </a>
        </div>
        <div class="sidebar">
            @if (Auth::guard('web')->check())
                @include('layouts.navigation-admin')
            @elseif (Auth::guard('barbeiro')->check())
                @include('layouts.navigation-barbeiro')
            @endif
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('breadcrumb', '')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @include('layouts.partials.messages')
                @yield('content')
                {{ $slot ?? '' }}
                @livewireScripts
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <div class="float-end d-none d-sm-inline">{{ Configuracao::get('nome_barbearia', 'Barbearia') }}</div>
        <strong>&copy; {{ date('Y') }} Todos os direitos reservados.</strong>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    function carregarNotificacoes() {
        @auth
        $.get('/notificacoes', function(data) {
            $('#notif-count').text(data.nao_lidas);
            let html = '';
            if (data.notificacoes.length === 0) {
                html = '<div class="text-center py-2 text-muted small">Nenhuma notificação</div>';
            } else {
                data.notificacoes.forEach(function(n) {
                    const unreadClass = n.lida ? '' : 'unread';
                    html += `<a href="${n.url || '#'}" class="dropdown-item ${unreadClass}">
                        <div class="d-flex">
                            <div class="me-3"><i class="${n.icon || 'fas fa-info-circle'}" style="color: ${n.color || '#6c757d'}"></i></div>
                            <div>
                                <strong>${n.title}</strong><br>
                                <small>${n.message}</small><br>
                                <small class="text-muted">${n.ago}</small>
                            </div>
                        </div>
                    </a>`;
                });
                html += '<div class="text-center py-2"><a href="/notificacoes/marcar-todas" class="small text-mark-all">Marcar todas como lidas</a></div>';
            }
            $('#notif-dropdown').html(html);
        });
        @endauth
    }

    carregarNotificacoes();
    setInterval(carregarNotificacoes, 30000);

    $(document).on('click', '.text-mar-all', function(e) {
        e.preventDefault();
        $.post('/notificacoes/marcar-todas', { _token: '{{ csrf_token() }}' }, function() {
            carregarNotificacoes();
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
