<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
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

    public function showTenantLoginForm(Barbearia $barbearia)
    {
        if (session('cliente_id') && Cliente::find(session('cliente_id'))) {
            return redirect()->route('tenant.site.agendar', $barbearia->slug);
        }
        return view('site.tenant-login', compact('barbearia'));
    }

    public function login(Request $request)
    {
        $telefone = preg_replace('/\D/', '', $request->telefone);
        if (strlen($telefone) < 10) {
            return back()->withInput()->with('error', 'Digite um telefone válido com DDD.');
        }

        $barbearia = null;
        if ($request->route()->hasParameter('barbearia')) {
            $barbearia = $request->route('barbearia');
        }

        $query = Cliente::where('telefone', $telefone)
            ->orWhere('whatsapp_id', $telefone);

        if ($barbearia && $barbearia instanceof Barbearia) {
            $query->where('barbearia_id', $barbearia->id);
        }

        $cliente = $query->first();

        if ($cliente) {
            session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
            if ($barbearia && $barbearia instanceof Barbearia) {
                return redirect()->route('tenant.site.agendar', $barbearia->slug);
            }
            return redirect()->route('site.agendar');
        }

        $nome = trim($request->nome);
        if (empty($nome)) {
            return back()->withInput()->with('novo', true)->with('telefone', $telefone)->with('error', 'Digite seu nome.');
        }
        if (strlen($nome) < 3) {
            return back()->withInput()->with('novo', true)->with('telefone', $telefone)->with('error', 'Nome deve ter pelo menos 3 caracteres.');
        }

        $clienteData = [
            'nome' => $nome,
            'telefone' => $telefone,
            'whatsapp_id' => $telefone,
        ];

        if ($barbearia && $barbearia instanceof Barbearia) {
            $clienteData['barbearia_id'] = $barbearia->id;
        }

        $cliente = Cliente::create($clienteData);

        session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome]);
        if ($barbearia && $barbearia instanceof Barbearia) {
            return redirect()->route('tenant.site.agendar', $barbearia->slug);
        }
        return redirect()->route('site.agendar');
    }

    public function logout(Request $request)
    {
        $barbearia = null;
        if ($request->route()->hasParameter('barbearia')) {
            $barbearia = $request->route('barbearia');
        }

        $request->session()->forget(['cliente_id', 'cliente_nome']);

        if ($barbearia && $barbearia instanceof Barbearia) {
            return redirect()->route('tenant.site.login', $barbearia->slug);
        }
        return redirect()->route('site.login');
    }
}
