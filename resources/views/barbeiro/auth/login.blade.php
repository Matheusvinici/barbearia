<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Barbeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); height: 100vh; }
        .login-card { border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); border: none; }
        .login-header { background: #0f3460; color: white; border-radius: 15px 15px 0 0; padding: 30px; text-align: center; }
        .login-header h4 { margin: 0; font-weight: 600; }
        .login-header p { margin: 5px 0 0; opacity: 0.8; font-size: 0.9rem; }
        .btn-login { background: #e94560; border: none; border-radius: 8px; padding: 12px; font-weight: 600; }
        .btn-login:hover { background: #d63851; }
    </style>
</head>
<body>
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="col-md-4">
            <div class="card login-card">
                <div class="login-header">
                    <i class="fas fa-regular fa-user-tie mb-3" style="font-size: 3rem;"></i>
                    <h4>Área do Barbeiro</h4>
                    <p>Faça login para acessar seus agendamentos</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('barbeiro.login.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Lembrar-me</label>
                        </div>
                        <button type="submit" class="btn btn-login btn-block text-white w-100">Entrar</button>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <a href="{{ route('login') }}" class="text-muted"><i class="fas fa-arrow-left"></i> Area Administrativa</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
