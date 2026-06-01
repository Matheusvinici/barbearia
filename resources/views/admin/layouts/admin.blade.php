<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jua Literária - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Nunito', sans-serif; background: #f5f6fa; color: #2D2D2D; }
        .admin-header { background: linear-gradient(135deg, #FF6B35, #004E89); color: #fff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 1.5rem; font-weight: 800; }
        .admin-header a { color: #fff; text-decoration: none; font-weight: 600; }
        .admin-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .card { background: #fff; border-radius: 15px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 1.5rem; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .stat-card { text-align: center; padding: 1.5rem; background: #fff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .stat-card .num { font-size: 2.5rem; font-weight: 900; color: #FF6B35; }
        .stat-card .label { font-size: 0.9rem; color: #888; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.8rem; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 700; color: #555; }
        .btn { font-family: 'Nunito', sans-serif; padding: 0.5rem 1.2rem; border-radius: 10px; border: none; cursor: pointer; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: inline-block; }
        .btn-orange { background: #FF6B35; color: #fff; }
        .btn-red { background: #E74C3C; color: #fff; }
        .btn-blue { background: #3498DB; color: #fff; }
        .btn-green { background: #2ECC71; color: #fff; }
        .btn-sm { padding: 0.3rem 0.8rem; font-size: 0.8rem; }
        .btn:hover { opacity: 0.85; }
        .flash { padding: 1rem; border-radius: 10px; margin-bottom: 1rem; font-weight: 600; }
        .flash-success { background: #D5F5E3; color: #1E8449; }
        .flash-error { background: #FADBD8; color: #922B21; }
        .pagination { margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>📚 Jua Literária - Admin</h1>
        <div>
            <span>{{ Auth::user()->name ?? 'Mediador' }}</span>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="margin-left: 1rem;">Sair</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>

    <div class="admin-container">
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        @yield('admin-content')
    </div>
</body>
</html>
