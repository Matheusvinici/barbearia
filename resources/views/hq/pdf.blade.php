<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>HQ - {{ $aluno->nome }}</title>
    <style>
        @page { margin: 15px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Comic Sans MS', 'Chalkboard SE', 'Nunito', cursive, sans-serif;
            background: #FFF8E7;
        }
        .cover-page {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #FF6B35, #F7C59F, #004E89);
            color: #fff;
            text-align: center;
            padding: 20px;
            page-break-after: always;
        }
        .cover-title {
            font-size: 48px;
            font-weight: 900;
            text-shadow: 4px 4px 0 rgba(0,0,0,0.2);
            margin-bottom: 10px;
        }
        .cover-subtitle {
            font-size: 24px;
            opacity: 0.9;
        }
        .cover-student {
            font-size: 36px;
            font-weight: 700;
            margin: 20px 0;
            padding: 15px 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
        }
        .comic-page {
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 8px;
            padding: 8px;
            background: #FFF8E7;
            page-break-after: always;
        }
        .panel {
            position: relative;
            border: 3px solid #2D2D2D;
            border-radius: 5px;
            overflow: hidden;
            background: #fff;
            box-shadow: inset 0 0 0 2px #fff;
        }
        .panel-image {
            width: 100%;
            height: 65%;
            object-fit: cover;
            display: block;
            background: #f0f0f0;
        }
        .panel-text {
            padding: 8px 10px;
            height: 35%;
            overflow: hidden;
            font-size: 12px;
            line-height: 1.3;
            background: #fff;
        }
        .panel-text strong {
            color: #FF6B35;
        }
        .speech-bubble {
            position: absolute;
            background: #fff;
            border: 2px solid #2D2D2D;
            border-radius: 15px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 700;
            max-width: 80%;
            box-shadow: 2px 2px 0 rgba(0,0,0,0.1);
        }
        .speech-bubble::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 20px;
            border-width: 10px 10px 0;
            border-style: solid;
            border-color: #fff transparent transparent;
        }
        .onomatopoeia {
            position: absolute;
            font-size: 28px;
            font-weight: 900;
            color: #FF6B35;
            text-shadow: 2px 2px 0 #2D2D2D;
            transform: rotate(-10deg);
            opacity: 0.8;
        }
        .panel-number {
            position: absolute;
            top: 5px;
            right: 8px;
            background: #FF6B35;
            color: #fff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            border: 2px solid #fff;
        }
        .no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 65%;
            background: linear-gradient(135deg, #FFEAA7, #FAB1A0);
            font-size: 32px;
            color: #666;
        }
        .footer-page {
            text-align: center;
            font-size: 10px;
            color: #888;
            padding: 5px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .last-page {
            page-break-after: avoid;
        }
    </style>
</head>
<body>
    <div class="cover-page">
        <div class="cover-title">🌟 Jua Literária Juazeiro 🌟</div>
        <div class="cover-subtitle">História em Quadrinhos</div>
        <div class="cover-student">{{ $aluno->nome }}</div>
        <div style="font-size: 18px; opacity: 0.8;">Turma: {{ $aluno->serie }}</div>
        <div style="font-size: 16px; opacity: 0.6; margin-top: 30px;">Uma história criada especialmente para você!</div>
    </div>

    @php
        $totalPanels = count($panelTexts);
        $chunks = array_chunk(range(0, $totalPanels - 1), 4);
    @endphp

    @foreach($chunks as $pageIndex => $panelIndices)
        <div class="comic-page {{ $loop->last ? 'last-page' : '' }}">
            @foreach($panelIndices as $i => $panelIdx)
                <div class="panel">
                    <div class="panel-number">{{ $panelIdx + 1 }}</div>

                    @if(isset($panelImages[$panelIdx]))
                        <img src="{{ $panelImages[$panelIdx] }}" class="panel-image" alt="Painel {{ $panelIdx + 1 }}">
                    @else
                        <div class="no-image">🎨</div>
                    @endif

                    <div class="panel-text">
                        {!! nl2br(e(Str::limit($panelTexts[$panelIdx] ?? '', 200))) !!}
                    </div>
                </div>
            @endforeach

            @for($i = count($panelIndices); $i < 4; $i++)
                <div class="panel" style="background: #f9f9f9; display: flex; align-items: center; justify-content: center;">
                    <div style="color: #ccc; font-size: 20px;">✨</div>
                </div>
            @endfor
        </div>
    @endforeach
</body>
</html>
