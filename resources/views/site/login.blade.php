@php
    $telefone = old('telefone', session('telefone', ''));
    $novo = old('novo', session('novo', false));
@endphp
@extends('layouts.guest')
@section('title', 'Agendar')
@section('subtitle', 'Faça login para agendar seu horário')

@section('content')
<form method="POST" action="/entrar">
    @csrf
    <div class="form-group">
        <label class="form-label">Telefone</label>
        <div class="input-group">
            <span class="addon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </span>
            <input type="tel" name="telefone" class="form-input" value="{{ $telefone }}" placeholder="(11) 99999-8888" required autofocus>
        </div>
        @error('telefone') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <button type="submit" class="btn-primary-c" style="width:100%;justify-content:center;margin-top:20px;">
        Entrar
    </button>
    <div style="text-align:center;margin-top:14px;">
        <a href="{{ route('site.agendar') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Continuar sem login</a>
    </div>
</form>
@endsection

@section('footer-links')
<a href="{{ route('login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Administração</a>
<span style="color:var(--text-faint);margin:0 8px;">|</span>
<a href="{{ route('barbeiro.login') }}" style="font-size:13px;color:var(--text-muted);text-decoration:none;">Barbeiros</a>
@endsection
