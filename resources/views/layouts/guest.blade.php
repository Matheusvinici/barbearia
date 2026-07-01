@php
    $nome = \App\Models\Configuracao::get('nome_barbearia', 'Barber Control');
    $logo = asset('images/logo.jpg');
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Login') — {{ $nome }}</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
</head>
<body style="background:var(--bg);min-height:100vh;">
<div class="login-wrapper">
    <div class="brand-panel">
        <div>
            <div class="brand-top">
                <div class="brand-mark-lg">SB</div>
                <div class="brand-title"><span>Studio</span> Barber <span>Pro</span></div>
            </div>
            <div class="testimonial" style="margin-top:80px;">
                <blockquote>O sistema que transformou a gestão da minha barbearia. Agendamento online, controle financeiro e muito mais.</blockquote>
                <cite>
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--accent-glow);display:grid;place-items:center;font-weight:700;color:var(--accent);">C</div>
                    <div>
                        <strong>Carlos Silva</strong>
                        <span>Proprietário, Santa Barba</span>
                    </div>
                </cite>
            </div>
        </div>
        <div style="font-size:12.5px;color:var(--text-muted);">
            &copy; {{ date('Y') }} Barber Control Pro. Todos os direitos reservados.
        </div>
    </div>
    <div class="auth-panel">
        <div class="auth-card">
            <div class="auth-header">
                <h1>@yield('title', 'Bem-vindo')</h1>
                <p>@yield('subtitle', 'Acesse sua conta')</p>
            </div>
            @yield('content')
            <div style="text-align:center;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                @yield('footer-links')
            </div>
        </div>
    </div>
</div>
</body>
</html>
