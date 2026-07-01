@extends('layouts.guest')
@section('title', 'Administração')
@section('subtitle', 'Acesse o painel administrativo')

@section('content')
<form method="POST" action="{{ route('login') }}">
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
    <div class="form-group" style="margin-top:8px;">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-muted);cursor:pointer;">
            <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:var(--accent);" {{ old('remember') ? 'checked' : '' }}>
            Lembrar-me
        </label>
    </div>
    <button type="submit" class="btn-primary-c" style="width:100%;justify-content:center;margin-top:20px;">
        Entrar
    </button>
</form>
@endsection

@section('footer-links')
<a href="{{ route('barbeiro.login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Área do Barbeiro</a>
<span style="color:var(--text-faint);margin:0 8px;">|</span>
<a href="{{ route('site.login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Agendamento Online</a>
@if(!request()->route('barbearia'))
<span style="color:var(--text-faint);margin:0 8px;">|</span>
<a href="{{ url('/') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Página Inicial</a>
@endif
@endsection
