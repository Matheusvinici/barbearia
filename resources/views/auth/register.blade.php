@extends('admin.layouts.admin')

@section('admin-content')
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-weight: 800; text-align: center; margin-bottom: 1.5rem;">📝 Cadastro</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="name" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required>
                </div>
                <div class="mb-3">
                    <label>E-mail</label>
                    <input type="email" name="email" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required>
                </div>
                <div class="mb-3">
                    <label>Senha</label>
                    <input type="password" name="password" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required>
                </div>
                <div class="mb-3">
                    <label>Confirmar Senha</label>
                    <input type="password" name="password_confirmation" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required>
                </div>
                <button type="submit" class="btn btn-orange" style="width: 100%; padding: 0.8rem; font-size: 1.1rem;">Cadastrar</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('login') }}" style="color: #3498DB;">Já tem conta? Faça login</a>
            </p>
        </div>
    </div>

    <style>
        .input-giant { font-family: 'Nunito', sans-serif; border: 2px solid #ddd; border-radius: 10px; width: 100%; outline: none; }
        .input-giant:focus { border-color: #FF6B35; }
        label { font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 0.3rem; }
    </style>
@endsection
