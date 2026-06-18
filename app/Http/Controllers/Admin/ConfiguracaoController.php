<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        $configuracoes = [
            'horario_abertura' => Configuracao::get('horario_abertura', '08:00'),
            'horario_fechamento' => Configuracao::get('horario_fechamento', '18:00'),
            'intervalo_minutos' => Configuracao::get('intervalo_minutos', '30'),
            'dias_funcionamento' => Configuracao::get('dias_funcionamento', '1,2,3,4,5,6'),
            'whatsapp_bot_token' => Configuracao::get('whatsapp_bot_token', ''),
            'nome_barbearia' => Configuracao::get('nome_barbearia', 'Minha Barbearia'),
            'endereco' => Configuracao::get('endereco', ''),
            'telefone' => Configuracao::get('telefone', ''),
        ];

        $botOnline = false;
        try {
            $response = Http::timeout(3)->get('http://localhost:3000/health');
            $botOnline = $response->successful();
        } catch (\Exception $e) {}

        $qrExiste = file_exists(public_path('storage/bot-qr.png'));

        return view('admin.configuracoes.index', compact('configuracoes', 'botOnline', 'qrExiste'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'horario_abertura' => 'required',
            'horario_fechamento' => 'required|after:horario_abertura',
            'intervalo_minutos' => 'required|integer|min:15|max:120',
            'dias_funcionamento' => 'required|string',
            'whatsapp_bot_token' => 'nullable|string',
            'nome_barbearia' => 'required|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);

        foreach ($request->except('_token') as $chave => $valor) {
            Configuracao::set($chave, $valor);
        }

        return redirect()->route('admin.configuracoes.index')->with('success', 'Configurações atualizadas com sucesso!');
    }

    public function qrCode()
    {
        $path = public_path('storage/bot-qr.png');
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->file($path, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function pairBot(Request $request)
    {
        $phone = preg_replace('/\D/', '', $request->phone);
        if (!$phone) {
            return back()->with('error', 'Telefone inválido');
        }

        try {
            $response = Http::timeout(10)->post('http://localhost:3000/pair', [
                'phone' => $phone,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return back()->with('pairing_code', $data['pairing_code']);
            }

            return back()->with('error', 'Erro ao conectar: ' . ($response->json()['error'] ?? 'desconhecido'));
        } catch (\Exception $e) {
            return back()->with('error', 'Bot offline. Verifique se o servidor está rodando.');
        }
    }
}
