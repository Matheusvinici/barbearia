<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Agende seu horário')</title>
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
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
        .main-content { flex: 1; padding: 1rem; display: flex; flex-direction: column; position: relative; z-index: 1; }
        .main-content > .container { max-width: 420px; width: 100%; margin: 0 auto; flex: 1; display: flex; flex-direction: column; }

        .step-card {
            background: var(--card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        .step-card h5 { font-weight: 600; margin-bottom: 1rem; color: var(--text); }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 1.5rem;
        }
        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--border-strong);
            transition: all 0.2s;
        }
        .step-dot.active { background: var(--accent); box-shadow: 0 0 8px var(--accent-glow); }
        .step-dot.done { background: var(--accent); opacity: 0.5; }

        .btn-primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #0d0d12;
            font-weight: 700;
            border-radius: 11px;
            padding: 12px 20px;
            transition: all 180ms;
        }
        .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); transform: translateY(-1px); }
        .btn-primary i { margin-right: 6px; }

        .btn-outline-custom {
            background: var(--card-solid);
            border: 1px solid var(--border-strong);
            color: var(--text);
            border-radius: 11px;
            transition: all 180ms;
        }
        .btn-outline-custom:hover { border-color: var(--accent); background: var(--accent-glow); color: var(--text); }

        .btn-dark-selected {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #0d0d12 !important;
            border-radius: 11px;
            font-weight: 600;
        }

        .form-control {
            background: var(--card-solid);
            border: 1px solid var(--border-strong);
            border-radius: 10px;
            padding: .6rem .75rem;
            font-size: .95rem;
            color: var(--text);
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: var(--card-solid);
            color: var(--text);
        }
        .form-control::placeholder { color: var(--text-faint); }
        .form-label { font-size: .85rem; font-weight: 500; color: var(--text-muted); margin-bottom: .3rem; }
        .alert { border-radius: 10px; }

        .footer-bar {
            background: var(--card-solid);
            border-top: 1px solid var(--border);
            padding: .6rem .5rem;
            text-align: center;
            font-size: .8rem;
            flex-shrink: 0;
            position: sticky;
            bottom: 0;
            z-index: 2;
        }
        .footer-bar a { color: var(--text-muted); text-decoration: none; margin: 0 .5rem; }
        .footer-bar a:hover { color: var(--accent); }

        .table { margin-bottom: 0; }
        .table th { border: none; color: var(--text-muted); font-weight: 500; padding: 4px 8px; }
        .table td { border: none; color: var(--text); padding: 4px 8px; }

        /* Welcome screen */
        .welcome-screen {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .welcome-header {
            text-align: center;
            padding: 20px 0 10px;
        }
        .welcome-logo {
            max-width: 120px;
            max-height: 120px;
            object-fit: contain;
            margin-bottom: 16px;
            border-radius: 16px;
        }
        .welcome-logo-placeholder {
            width: 80px; height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--accent), var(--accent-soft));
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: #0d0d12;
            box-shadow: 0 8px 22px -8px var(--accent-glow);
        }
        .welcome-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        .welcome-address {
            font-size: 14px;
            color: var(--text-muted);
            margin: 0;
        }
        .welcome-address i { margin-right: 4px; }
        .welcome-section {
            background: var(--card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            padding: 16px;
        }
        .welcome-section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .services-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }
        .service-item:last-child { border-bottom: none; }
        .service-info strong {
            font-size: 14px;
            color: var(--text);
        }
        .service-info small {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .service-price {
            font-size: 15px;
            font-weight: 700;
            color: var(--accent);
            white-space: nowrap;
        }
        .reviews-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .review-card {
            padding: 12px;
            background: var(--card-solid);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }
        .review-header strong { font-size: 13px; color: var(--text); }
        .review-stars { color: var(--accent); font-size: 13px; }
        .review-stars i { margin-right: 1px; }
        .review-comment {
            font-size: 13px;
            color: var(--text-muted);
            margin: 4px 0 0;
        }
        .review-reply {
            margin-top: 8px;
            padding: 8px 10px;
            background: var(--bg);
            border-radius: var(--r-sm);
            border-left: 3px solid var(--accent);
        }
        .review-reply small { font-size: 12px; color: var(--text-muted); }
        .review-reply strong { color: var(--accent); }
        .btn-agendar {
            padding: 16px;
            font-size: 16px;
            border-radius: 14px;
        }
        .welcome-footer-links {
            text-align: center;
        }
        .welcome-footer-links a { text-decoration: none; }
        .welcome-footer-links a:hover { color: var(--accent) !important; }

        hr { margin: 12px 0; opacity: 0.3; }
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
