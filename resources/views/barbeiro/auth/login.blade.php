@php
    $barbearia = $barbearia ?? null;
    $tenantLogo = ($barbearia?->logo_url ?? asset('images/logo.jpg'));
    $tenantName = ($barbearia?->nome ?? config('app.name', 'Barber Control'));
    $tenantBg = ($barbearia?->background_url ?? asset('images/frenteBarbearia.jpg'));
    $tenantSlug = ($barbearia?->slug ?? null);
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $tenantName }} — Login Barbeiro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
</head>
<body>
<div class="login-wrapper">
    <div class="brand-panel" style="background:
        radial-gradient(circle at 80% 0%, var(--accent-glow), transparent 50%),
        radial-gradient(circle at 0% 100%, rgba(96, 165, 250, 0.05), transparent 50%),
        linear-gradient(135deg, var(--bg-elevated) 0%, var(--bg) 100%),
        url('{{ $tenantBg }}') center/cover;">
        <div>
            <div class="brand-top">
                @if($barbearia && $barbearia?->logo_url)
                <img src="{{ $tenantLogo }}" alt="{{ $tenantName }}" style="height:48px;border-radius:12px;">
            @else
                <div class="brand-mark-lg">SB</div>
            @endif
                <div class="brand-title" style="font-size:20px;"><span>{{ $tenantName }}</span></div>
            </div>
            <div class="testimonial" style="margin-top:60px;">
                <blockquote>Área do barbeiro — gerencie seus agendamentos e atendimentos.</blockquote>
            </div>
        </div>
        <div style="font-size:12.5px;color:var(--text-muted);">
            &copy; {{ date('Y') }} Barber Control Pro
        </div>
    </div>
    <div class="auth-panel">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Olá, barbeiro!</h1>
                <p>Entre com suas credenciais</p>
            </div>
            <form method="POST" action="{{ $tenantSlug ? route('tenant.barbeiro.login.store', $tenantSlug) : route('barbeiro.login.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="seu@email.com" required autofocus>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-top:16px;">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn-primary-c" style="width:100%;justify-content:center;margin-top:20px;">
                    Entrar
                </button>
            </form>
            <div style="text-align:center;margin-top:24px;padding-top:20px;border-top:1px solid var(--border);">
                <a href="{{ $tenantSlug ? route('tenant.login', $tenantSlug) : route('login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Administração</a>
                <span style="color:var(--text-faint);margin:0 8px;">|</span>
                <a href="{{ $tenantSlug ? route('tenant.site.login', $tenantSlug) : route('site.login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Agendamento</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
