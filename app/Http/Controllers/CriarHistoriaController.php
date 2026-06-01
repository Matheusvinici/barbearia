<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Historia;
use App\Models\RespostaAluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class CriarHistoriaController extends Controller
{
    private $etapas = [
        1 => 'Quem é você?',
        2 => 'Onde você vive?',
        3 => 'Quem está com você?',
        4 => 'O que te move?',
    ];

    private $perguntas = [
        1 => [
            'nome' => 'Qual é o seu nome?',
            'idade' => 'Quantos anos você tem?',
            'olhos' => 'Qual é a cor dos seus olhos?',
            'cabelo' => 'Como é seu cabelo (cor, tamanho, tipo)?',
            'pele' => 'Qual é o tom da sua pele?',
            'altura' => 'Você é alto(a), médio(a) ou baixinho(a)?',
            'fisico' => 'Você é magro(a), fortinho(a) ou mais cheinho(a)?',
        ],
        2 => [
            'bairro' => 'Em qual bairro ou comunidade você mora?',
            'ruas' => 'Quais são as ruas e lugares perto da sua casa?',
            'clima' => 'Como é o clima onde você vive? (quente, frio, chuvoso?)',
            'lugares' => 'Quais são seus lugares favoritos no seu bairro?',
            'escola' => 'Qual é o nome da sua escola?',
            'caminho' => 'Como é o caminho até a escola?',
        ],
        3 => [
            'mora_com' => 'Com quem você mora?',
            'mae' => 'Conte um pouco sobre sua mãe (ou quem cuida de você)',
            'pai' => 'Conte um pouco sobre seu pai',
            'irmaos' => 'Você tem irmãos? Quantos e como são?',
            'amigos' => 'Quem são seus melhores amigos?',
            'animal' => 'Você tem um animal de estimação?',
        ],
        4 => [
            'brincadeira' => 'Qual é sua brincadeira ou atividade favorita?',
            'sonho' => 'Qual é o seu maior sonho?',
            'superpoder' => 'Se você tivesse um superpoder, qual seria?',
            'medo' => 'Do que você tem mais medo?',
            'feliz' => 'O que te faz mais feliz?',
            'desafio' => 'Qual foi o maior desafio que você já enfrentou?',
        ],
    ];

    public function iniciar()
    {
        if (!session('aluno_id')) {
            return redirect()->route('site.entrar');
        }

        $aluno = Aluno::find(session('aluno_id'));

        $historia = Historia::create([
            'aluno_id' => $aluno->id,
            'status' => 'rascunho',
            'slug' => Str::random(12),
        ]);

        session(['historia_id' => $historia->id]);

        return redirect()->route('site.criar.etapa', ['etapa' => 1]);
    }

    public function etapa($num)
    {
        if (!session('aluno_id') || !session('historia_id')) {
            return redirect()->route('site.welcome');
        }

        $num = (int) $num;
        if ($num < 1 || $num > 4) {
            return redirect()->route('site.criar.etapa', ['etapa' => 1]);
        }

        $historia = Historia::with('respostas')->find(session('historia_id'));
        $perguntas = $this->perguntas[$num];
        $respostas = $historia->respostas->where('etapa', $num);

        return view('site.criar.etapa' . $num, [
            'etapa' => $num,
            'totalEtapas' => 4,
            'tituloEtapa' => $this->etapas[$num],
            'perguntas' => $perguntas,
            'respostas' => $respostas,
            'historia' => $historia,
        ]);
    }

    public function salvarEtapa(Request $request, $num)
    {
        if (!session('historia_id')) {
            return redirect()->route('site.welcome');
        }

        $num = (int) $num;
        $historia = Historia::find(session('historia_id'));

        $perguntas = $this->perguntas[$num];

        $rules = [];
        foreach ($perguntas as $key => $pergunta) {
            $rules[$key] = 'required|string|max:1000';
        }

        $request->validate($rules);

        RespostaAluno::where('historia_id', $historia->id)
            ->where('etapa', $num)
            ->delete();

        foreach ($perguntas as $key => $pergunta) {
            RespostaAluno::create([
                'historia_id' => $historia->id,
                'etapa' => $num,
                'pergunta' => $pergunta,
                'resposta' => $request->$key,
            ]);
        }

        if ($num < 4) {
            return redirect()->route('site.criar.etapa', ['etapa' => $num + 1]);
        }

        return redirect()->route('site.criar.revisar');
    }

    public function revisar()
    {
        if (!session('historia_id')) {
            return redirect()->route('site.welcome');
        }

        $historia = Historia::with(['aluno', 'respostas'])->find(session('historia_id'));
        $respostasAgrupadas = $historia->respostas->groupBy('etapa');

        return view('site.criar.revisar', [
            'historia' => $historia,
            'respostasAgrupadas' => $respostasAgrupadas,
            'etapas' => $this->etapas,
        ]);
    }

    public function gerar()
    {
        if (!session('historia_id')) {
            return redirect()->route('site.welcome');
        }

        $historia = Historia::with(['aluno', 'respostas'])->find(session('historia_id'));

        $prompt = $this->montarPrompt($historia);
        $historia->update(['prompt_gerado' => $prompt]);

        $respostaGemini = $this->chamarGemini($prompt);

        $slug = $historia->slug;
        $storagePath = "hqs/{$slug}";
        Storage::disk('public')->makeDirectory($storagePath);

        $panelImages = [];
        $panelTexts = [];

        if ($respostaGemini && isset($respostaGemini['candidates'][0]['content']['parts'])) {
            $parts = $respostaGemini['candidates'][0]['content']['parts'];
            $imageIndex = 0;

            foreach ($parts as $part) {
                if (isset($part['text'])) {
                    $panelTexts[] = $part['text'];
                } elseif (isset($part['inlineData']) && $part['inlineData']['mimeType'] === 'image/png') {
                    $imageData = base64_decode($part['inlineData']['data']);
                    $imagePath = "{$storagePath}/painel_{$imageIndex}.png";
                    Storage::disk('public')->put($imagePath, $imageData);
                    $panelImages[] = Storage::url($imagePath);
                    $imageIndex++;
                }
            }
        }

        $historia->update([
            'status' => 'concluido',
            'resposta_gemini' => $respostaGemini,
        ]);

        $pdfPath = $this->gerarPdf($historia, $panelImages, $panelTexts);
        $qrCodePath = $this->gerarQRCode($historia);

        $historia->update([
            'pdf_path' => $pdfPath,
            'qr_code_path' => $qrCodePath,
        ]);

        session()->forget('historia_id');

        return redirect()->route('site.criar.resultado', ['slug' => $slug]);
    }

    public function resultado($slug)
    {
        $historia = Historia::with('aluno')->where('slug', $slug)->firstOrFail();

        return view('site.resultado', compact('historia'));
    }

    public function downloadPdf($slug)
    {
        $historia = Historia::where('slug', $slug)->firstOrFail();

        if (!$historia->pdf_path || !Storage::disk('public')->exists($historia->pdf_path)) {
            abort(404);
        }

        $alunoNome = Str::slug($historia->aluno->nome);
        return Storage::disk('public')->download($historia->pdf_path, "HQ_{$alunoNome}.pdf");
    }

    private function montarPrompt($historia)
    {
        $respostas = $historia->respostas->groupBy('etapa');
        $etapa1 = $respostas->get(1, collect());
        $etapa2 = $respostas->get(2, collect());
        $etapa3 = $respostas->get(3, collect());
        $etapa4 = $respostas->get(4, collect());

        $formatar = function ($items) {
            return $items->pluck('resposta', 'pergunta')
                ->map(fn($v, $k) => "- {$k}: {$v}")
                ->implode("\n");
        };

        return <<<PROMPT
Seja um profissional de histórias em quadrinhos. Crie uma história em quadrinhos infantil e colorida sobre um aluno.

## Dados do Aluno:
{$formatar($etapa1)}

## Onde vive:
{$formatar($etapa2)}

## Família e Amigos:
{$formatar($etapa3)}

## Sonhos e Desafios:
{$formatar($etapa4)}

## Instruções:
- A história deve ter EXATAMENTE 4 a 8 quadros (painéis).
- Cada quadro deve ter UMA ilustração colorida em estilo de desenho infantil, traço escolar.
- Cada quadro deve ter falas simples e curtas dos personagens, em português brasileiro.
- A linguagem deve ser apropriada para crianças.
- O protagonista da história é o próprio aluno.
- A história deve ser positiva, lúdica e inspiradora.
- Inclua os lugares, pessoas e elementos da vida real do aluno na narrativa.
- Formato: Gere cada quadro alternando entre descrição em texto da cena e a ilustração correspondente.
PROMPT;
    }

    private function chamarGemini($prompt)
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return null;
        }

        $models = [
            'gemini-2.0-flash-exp',
            'gemini-2.0-flash',
        ];

        foreach ($models as $model) {
            try {
                $response = Http::timeout(120)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                        'generationConfig' => [
                            'responseModalities' => ['TEXT', 'IMAGE'],
                            'temperature' => 0.9,
                            'maxOutputTokens' => 8192,
                        ],
                    ]
                );

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data && isset($data['candidates'])) {
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Fallback: text-only with gemini-2.0-flash
        try {
            $response = Http::timeout(120)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.9,
                        'maxOutputTokens' => 8192,
                    ],
                ]
            );

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    private function gerarPdf($historia, $panelImages, $panelTexts)
    {
        $slug = $historia->slug;
        $aluno = $historia->aluno;

        $pdf = Pdf::loadView('hq.pdf', compact('historia', 'aluno', 'panelImages', 'panelTexts'));
        $pdf->setPaper('a4');

        $path = "hqs/{$slug}/historia.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    private function gerarQRCode($historia)
    {
        $url = route('site.criar.download-pdf', ['slug' => $historia->slug]);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 10,
            'imageBase64' => false,
        ]);

        $qrcode = new QRCode($options);
        $imageData = $qrcode->render($url);

        $path = "hqs/{$historia->slug}/qrcode.png";
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}
