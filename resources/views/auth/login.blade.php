@extends('admin.layouts.admin')

@section('admin-content')
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <h2 style="font-weight: 800; text-align: center; margin-bottom: 1.5rem;">🔐 Login do Mediador</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label>E-mail</label>
                    <input type="email" name="email" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required autofocus>
                </div>
                <div class="mb-3">
                    <label>Senha</label>
                    <input type="password" name="password" class="input-giant" style="font-size: 1.1rem; padding: 0.7rem 1rem;" required>
                </div>
                @error('email')
                    <p style="color: #E74C3C; font-size: 0.9rem; margin-bottom: 0.5rem;">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn btn-orange" style="width: 100%; padding: 0.8rem; font-size: 1.1rem;">Entrar</button>
            </form>
        </div>
    </div>

    <style>
        .input-giant { font-family: 'Nunito', sans-serif; border: 2px solid #ddd; border-radius: 10px; width: 100%; outline: none; transition: border-color 0.3s; }
        .input-giant:focus { border-color: #FF6B35; }
        label { font-size: 0.9rem; font-weight: 700; display: block; margin-bottom: 0.3rem; color: #2D2D2D; }
    </style>
@endsection
