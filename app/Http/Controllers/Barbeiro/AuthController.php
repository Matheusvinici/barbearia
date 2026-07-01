<?php

namespace App\Http\Controllers\Barbeiro;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('barbeiro.auth.login');
    }

    public function showTenantLoginForm(Barbearia $barbearia)
    {
        return view('barbeiro.auth.login', compact('barbearia'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('barbeiro')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $barbeiro = Auth::guard('barbeiro')->user();

            if ($request->route()->hasParameter('barbearia')) {
                $barbearia = $request->route('barbearia');
                if ($barbearia instanceof Barbearia) {
                    return redirect()->route('tenant.barbeiro.dashboard', $barbearia->slug);
                }
            }

            return redirect()->to(route('barbeiro.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $barbearia = null;
        if ($request->route()->hasParameter('barbearia')) {
            $barbearia = $request->route('barbearia');
        }

        Auth::guard('barbeiro')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($barbearia && $barbearia instanceof Barbearia) {
            return redirect()->route('tenant.barbeiro.login', $barbearia->slug);
        }

        return redirect('/barbeiro/login');
    }
}
