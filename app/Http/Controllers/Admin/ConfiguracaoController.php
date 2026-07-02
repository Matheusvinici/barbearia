<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use App\Models\Configuracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        $barbearia = request()->route('barbearia');
        $user = auth()->user();

        if ($barbearia) {
            if ($barbearia->isMatriz()) {
                $barbearias = collect([$barbearia])->merge($barbearia->filiais);
            } else {
                $barbearias = collect([$barbearia]);
            }
        } elseif ($user && $user->isSuperAdmin()) {
            $barbearias = Barbearia::orderBy('nome')->get();
        } else {
            $barbearias = $user?->ownedBarbearias ?? collect();
        }

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
        $botAuthenticated = false;
        try {
            $response = Http::timeout(3)->get('http://localhost:3000/health');
            if ($response->successful()) {
                $botOnline = true;
                $data = $response->json();
                $botAuthenticated = $data['authenticated'] ?? false;
            }
        } catch (\Exception $e) {}

        $qrExiste = file_exists(public_path('storage/bot-qr.png'));

        return view('admin.configuracoes.index', compact('configuracoes', 'botOnline', 'botAuthenticated', 'qrExiste', 'barbearia', 'barbearias'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'horario_abertura' => 'nullable',
            'horario_fechamento' => 'nullable',
            'intervalo_minutos' => 'nullable|integer|min:15|max:120',
            'dias_funcionamento' => 'nullable|string',
            'whatsapp_bot_token' => 'nullable|string',
            'barbearias' => 'nullable|array',
            'barbearias.*.id' => 'required|exists:barbearias,id',
            'barbearias.*.horario_abertura' => 'nullable|string',
            'barbearias.*.horario_fechamento' => 'nullable|string',
            'barbearias.*.intervalo_minutos' => 'nullable|integer|min:15|max:120',
            'barbearias.*.dias_funcionamento' => 'nullable|string',
        ]);

        // Save per-barbearia hours
        if ($request->has('barbearias')) {
            foreach ($request->barbearias as $data) {
                $b = Barbearia::find($data['id']);
                if ($b) {
                    $b->update([
                        'horario_abertura' => $data['horario_abertura'] ?? $b->horario_abertura,
                        'horario_fechamento' => $data['horario_fechamento'] ?? $b->horario_fechamento,
                        'intervalo_minutos' => $data['intervalo_minutos'] ?? $b->intervalo_minutos,
                        'dias_funcionamento' => $data['dias_funcionamento'] ?? $b->dias_funcionamento,
                    ]);
                }
            }
        }

        // Save global config keys
        foreach ($request->except('_token', 'barbearias') as $chave => $valor) {
            if (in_array($chave, ['horario_abertura', 'horario_fechamento', 'intervalo_minutos', 'dias_funcionamento', 'whatsapp_bot_token', 'nome_barbearia', 'endereco', 'telefone', 'email', 'metodo_pagamento_padrao', 'taxa_servico', 'aliquota_impostos', 'emissao_nf', 'notificacoes_painel', 'lembretes_email', 'resumo_diario', 'cancelamento_notif'])) {
                Configuracao::set($chave, $valor);
            }
        }

        return redirect()->back()->with('success', 'Configurações atualizadas com sucesso!');
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
