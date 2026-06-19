<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Barbearia') - Agende seu horário</title>
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @stack('styles')
    <style>
        :root { --primary: #1a1a2e; --accent: #e94560; --bg: #f8f9fa; }
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
            background: #fff;
            border-top: 1px solid #eee;
            padding: .6rem .5rem;
            text-align: center;
            font-size: .8rem;
            flex-shrink: 0;
            position: sticky;
            bottom: 0;
        }
        .footer-bar a { color: #666; text-decoration: none; margin: 0 .5rem; }
        .footer-bar a:hover { color: var(--accent); }
        .step-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .step-card h5 { font-weight: 600; margin-bottom: 1rem; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: #d63850; border-color: #d63850; }
        .service-card { cursor: pointer; border: 2px solid transparent; transition: all .2s; border-radius: 12px; padding: .75rem; }
        .service-card:hover { border-color: var(--accent); }
        .service-card.selected { border-color: var(--accent); background: #fff5f5; }
        .service-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </div>

    <div class="footer-bar">
        <a href="{{ route('site.agendar') }}">Agendar</a>
        <span class="text-secondary">|</span>
        <a href="{{ route('login') }}">Administração</a>
        <span class="text-secondary">|</span>
        <a href="{{ route('barbeiro.login') }}">Barbeiros</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
