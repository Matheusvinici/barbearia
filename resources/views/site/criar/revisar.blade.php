@extends('layouts.site')

@section('content')
    <a href="{{ route('site.criar.etapa', ['etapa' => 4]) }}" class="back-btn">← Voltar</a>

    <div class="card card-wide" style="max-height: 75vh; overflow-y: auto;">
        <div class="progress-bar">
            @for($i = 1; $i <= 4; $i++)
                <div class="text-center">
                    <div class="step-dot done">✓</div>
                    <div class="step-dot-label">{{ ['Quem é?', 'Onde vive?', 'Família', 'Sonhos'][$i-1] }}</div>
                </div>
            @endfor
        </div>

        <h2 class="etapa-title">📝 Revisar História</h2>
        <p style="text-align: center; font-size: 1.1rem; color: #666; margin-bottom: 1.5rem;">
            Veja como ficou sua história! Tudo certinho?
        </p>

        @foreach($respostasAgrupadas as $etapaNum => $respostas)
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.3rem; font-weight: 800; color: #FF6B35;">
                    {{ $etapas[$etapaNum] ?? "Etapa $etapaNum" }}
                </h3>
                <div style="background: #fff; border-radius: 15px; padding: 1rem;">
                    @foreach($respostas as $resposta)
                        <div style="margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #eee;">
                            <strong style="color: #555;">{{ $resposta->pergunta }}</strong>
                            <p style="font-size: 1.1rem; margin: 0.2rem 0 0 0;">{{ $resposta->resposta }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4">
            <form method="POST" action="{{ route('site.criar.gerar') }}">
                @csrf
                <button type="submit" class="btn-giant btn-orange" style="width: 100%;">
                    ✨ Criar Minha HQ!
                </button>
            </form>
            <p style="color: #888; font-size: 0.9rem; margin-top: 0.5rem;">
                A inteligência artificial vai criar uma história especial para você!
            </p>
        </div>
    </div>
@endsection
