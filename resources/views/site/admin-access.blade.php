<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acesso Administrativo</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/studio-barber.css') }}">
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
    --accent-glow: rgba(245, 181, 68, 0.16);
    --r-xl: 16px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px 20px; }
.container { width: 100%; max-width: 480px; }
h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
p { color: var(--text-muted); font-size: 14px; margin-bottom: 28px; }
.back { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 13px; text-decoration: none; margin-bottom: 24px; }
.back:hover { color: var(--accent); }
.barbearia-list { display: flex; flex-direction: column; gap: 8px; }
.barbearia-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--card); border: 1px solid var(--border); border-radius: var(--r-xl); text-decoration: none; color: var(--text); transition: all 180ms; }
.barbearia-item:hover { border-color: var(--accent); background: var(--accent-glow); }
.barbearia-item .nome { font-weight: 600; font-size: 15px; }
.barbearia-item .seta { color: var(--text-faint); font-size: 18px; }
.empty { text-align: center; color: var(--text-faint); padding: 40px; font-size: 14px; }
.btn-admin { display: block; width: 100%; padding: 16px 20px; background: var(--accent); color: #0d0d12; text-align: center; border-radius: var(--r-xl); text-decoration: none; font-weight: 700; font-size: 15px; transition: all 180ms; }
.btn-admin:hover { background: #ffc554; }
</style>
</head>
<body>
<div class="container">
  <a href="{{ route('landing') }}" class="back">&larr; Voltar</a>
  <h1>Acesso Administrativo</h1>
  <p>Faça login na dashboard geral ou acesse diretamente uma barbearia.</p>
  <a href="{{ route('login') }}" class="btn-admin">Dashboard Geral</a>
  <div style="margin:24px 0; text-align:center; color:var(--text-faint); font-size:13px; position:relative;">
    <span style="background:var(--bg); padding:0 12px;">ou acesse uma barbearia</span>
  </div>
  @if($barbearias->count())
  <div class="barbearia-list">
    @foreach($barbearias as $b)
    <a href="{{ route('tenant.login', $b->slug) }}" class="barbearia-item">
      <span class="nome">{{ $b->nome }}</span>
      <span class="seta">&rarr;</span>
    </a>
    @endforeach
  </div>
  @else
  <div class="empty">Nenhuma barbearia cadastrada.</div>
  @endif
</div>
</body>
</html>
