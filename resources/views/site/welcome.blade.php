@extends('layouts.site')

@section('content')
    <div class="text-center mb-5">
        <div style="font-size: 5rem; margin-bottom: 1rem;">📚✨</div>
        <h1 class="title title-lg mb-3">Jua Literária<br>Juazeiro</h1>
        <p class="subtitle mb-5">Crie sua própria história em quadrinhos!</p>
    </div>

    <div class="d-flex flex-column align-items-center gap-4">
        <a href="{{ route('site.entrar') }}" class="btn-giant btn-orange">
            🚀 Nova História
        </a>
        @if(session('aluno_id'))
            <a href="{{ route('site.biblioteca') }}" class="btn-giant btn-white">
                📖 Minhas Histórias
            </a>
        @endif
    </div>

    <div style="position: fixed; bottom: 2rem; text-align: center; color: rgba(255,255,255,0.4); font-size: 0.9rem;">
        Toque na tela para começar
    </div>
@endsection
