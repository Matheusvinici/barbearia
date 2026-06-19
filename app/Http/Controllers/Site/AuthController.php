<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (session('cliente_id') && Cliente::find(session('cliente_id'))) {
            return redirect()->route('site.agendar');
        }
        return view('site.login');
    }

    public function login(Request $request)
    {
        $telefone = preg_replace('/\D/', '', $request->telefone);
        if (strlen($telefone) < 10) {
            return back()->withInput()->with('error', 'Digite um telefone válido com DDD.');
        }

        $cliente = Cliente::where('telefone', $telefone)
            ->orWhere('whatsapp_id', $telefone)
            ->first();

        if ($cliente) {
            session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
            return redirect()->route('site.agendar');
        }

        $nome = trim($request->nome);
        if (empty($nome)) {
            return back()->withInput()->with('novo', true)->with('telefone', $telefone)->with('error', 'Digite seu nome.');
        }
        if (strlen($nome) < 3) {
            return back()->withInput()->with('novo', true)->with('telefone', $telefone)->with('error', 'Nome deve ter pelo menos 3 caracteres.');
        }

        $cliente = Cliente::create([
            'nome' => $nome,
            'telefone' => $telefone,
            'whatsapp_id' => $telefone,
        ]);

        session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
        return redirect()->route('site.agendar');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['cliente_id', 'cliente_nome']);
        return redirect()->route('site.login');
    }
}
