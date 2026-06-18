@extends('layouts.guest')
@section('title', 'Entrar')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Lembrar-me</label>
        </div>
        <button type="submit" class="btn btn-login text-white w-100">Entrar</button>
    </form>
@endsection
