@extends('layouts.site')

@section('content')
    <div class="text-center">
        <div style="font-size: 6rem; animation: pulse 1.5s ease-in-out infinite;">🎨</div>
        <h1 class="title" style="font-size: 2.5rem; margin-top: 1rem;">Criando sua história...</h1>
        <p class="subtitle" style="font-size: 1.3rem;">A inteligência artificial está desenhando sua HQ!</p>
        <div style="margin-top: 2rem; width: 200px; height: 200px; border: 8px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 1s linear infinite; margin-left: auto; margin-right: auto;"></div>
    </div>

    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>

    <meta http-equiv="refresh" content="30;url={{ route('site.biblioteca') }}">
@endsection
