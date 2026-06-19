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
        .site-header {
            background: var(--primary);
            color: #fff;
            padding: .75rem 1rem;
            text-align: center;
            flex-shrink: 0;
        }
        .site-header h1 { font-size: 1.2rem; margin: 0; font-weight: 700; }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .main-content > .container { max-width: 400px; width: 100%; }
        .step-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,.08);
            padding: 1.5rem;
            margin-bottom: 1rem;
            position: relative;
        }
        .step-card h5 { font-weight: 600; margin-bottom: 1rem; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: #d63850; border-color: #d63850; }
        .service-card { cursor: pointer; border: 2px solid transparent; transition: all .2s; border-radius: 12px; padding: .75rem; }
        .service-card:hover { border-color: var(--accent); }
        .service-card.selected { border-color: var(--accent); background: #fff5f5; }
        .service-card img { width: 100%; height: 120px; object-fit: cover; border-radius: 8px; }
        .footer {
            background: var(--primary);
            color: rgba(255,255,255,.7);
            padding: .75rem 1rem;
            text-align: center;
            font-size: .8rem;
            flex-shrink: 0;
        }
        .footer a { color: rgba(255,255,255,.5); text-decoration: none; }
        .footer a:hover { color: #fff; }
        .step-indicator { display: flex; justify-content: center; gap: .5rem; margin-bottom: 1.5rem; }
        .step-dot { width: 12px; height: 12px; border-radius: 50%; background: #dee2e6; }
        .step-dot.active { background: var(--accent); }
        .step-dot.done { background: #28a745; }
        .phone-input { font-size: 1.2rem; text-align: center; letter-spacing: 2px; }
    </style>
</head>
<body>
    <header class="site-header">
        <h1><i class="bi bi-scissors"></i> Barbearia</h1>
    </header>

    <div class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <footer class="footer">
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('site.login') }}">Agendar</a>
            <span class="text-secondary">|</span>
            <a href="{{ route('login') }}">Administração</a>
            <span class="text-secondary">|</span>
            <a href="{{ route('barbeiro.login') }}">Barbeiros</a>
        </div>
        <div class="mt-1">&copy; {{ date('Y') }} Barbearia</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
