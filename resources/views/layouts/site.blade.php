<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Santa Barba') - Agende seu horário</title>
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('styles')
    <style>
        :root { --accent: #f5b544; --bg: #ffffff; --card-bg: #ffffff; }
        html, body { height: 100%; margin: 0; }
        body {
            background: var(--bg);
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
        }
        .main-content { flex: 1; padding: 1rem; display: flex; flex-direction: column; }
        .main-content > .container { max-width: 400px; width: 100%; margin: 0 auto; flex: 1; display: flex; flex-direction: column; }
        .footer-bar {
            background: #f8f9fa;
            border-top: 1px solid #eee;
            padding: .6rem .5rem;
            text-align: center;
            font-size: .8rem;
            flex-shrink: 0;
            position: sticky;
            bottom: 0;
        }
        .footer-bar a { color: #555; text-decoration: none; margin: 0 .5rem; }
        .footer-bar a:hover { color: var(--accent); }
        .step-card {
            background: var(--card-bg);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0,0,0,.04);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .step-card h5 { font-weight: 600; margin-bottom: 1rem; color: #111; }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #111; font-weight: 600; }
        .btn-primary:hover { background: #e0a030; border-color: #e0a030; color: #111; }
        .btn-outline-dark { border-color: #d1d5db; color: #374151; }
        .btn-outline-dark:hover { background: #f3f4f6; border-color: #d1d5db; color: #111; }
        .btn-dark { background: #111; border-color: #111; color: #fff; }
        .service-card { cursor: pointer; border: 2px solid transparent; transition: all .2s; border-radius: 12px; padding: .75rem; }
        .service-card:hover { border-color: var(--accent); }
        .service-card.selected { border-color: var(--accent); background: #fef9ef; }
        .service-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; }
        .form-control { border: 1px solid #d1d5db; border-radius: 10px; padding: .6rem .75rem; font-size: .95rem; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(245,181,68,.15); }
        .form-label { font-size: .85rem; font-weight: 500; color: #374151; margin-bottom: .3rem; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    @php
        $barbearia = request()->route('barbearia');
        $slug = $barbearia ? $barbearia->slug : null;
    @endphp
    <div class="footer-bar">
        <a href="{{ $slug ? route('tenant.site.agendar', $slug) : route('site.agendar') }}">Agendar</a>
        <span class="text-secondary">|</span>
        <a href="{{ $slug ? route('tenant.login', $slug) : route('login') }}">Administração</a>
        <span class="text-secondary">|</span>
        <a href="{{ $slug ? route('tenant.barbeiro.login', $slug) : route('barbeiro.login') }}">Barbeiros</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
