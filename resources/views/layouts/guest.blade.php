<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') - Barbearia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 100%; max-width: 420px; }
        .login-header { background: #0f3460; color: white; border-radius: 15px 15px 0 0; padding: 30px; text-align: center; }
        .login-header h4 { margin: 0; font-weight: 600; }
        .login-header p { margin: 5px 0 0; opacity: 0.8; font-size: 0.9rem; }
        .btn-login { background: #e94560; border: none; border-radius: 8px; padding: 12px; font-weight: 600; }
        .btn-login:hover { background: #d63851; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-cut mb-3" style="font-size: 3rem;"></i>
            <h4>Barbearia</h4>
            <p>Administração</p>
        </div>
        <div class="card-body p-4 bg-white" style="border-radius: 0 0 15px 15px;">
            @yield('content')
        </div>
        <div class="card-footer text-center py-3 bg-white" style="border-radius: 0 0 15px 15px; border-top: none;">
            <a href="{{ route('barbeiro.login') }}" class="text-muted small"><i class="fas fa-user-tie"></i> Área do Barbeiro</a>
        </div>
    </div>
</body>
</html>
