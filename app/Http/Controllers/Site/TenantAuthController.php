<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAuthController extends Controller
{
    public function showLoginForm(Barbearia $barbearia)
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('tenant.admin.dashboard', $barbearia->slug);
        }
        return view('admin.tenant-login', compact('barbearia'));
    }

    public function login(Request $request, Barbearia $barbearia)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $user = Auth::guard('web')->user();

            if ($user->isSuperAdmin() || $barbearia->owner_id === $user->id || $user->hasAnyRole(['proprietario', 'admin'])) {
                $request->session()->regenerate();
                return redirect()->route('tenant.admin.dashboard', $barbearia->slug);
            }

            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Você não tem permissão para acessar esta barbearia.']);
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request, Barbearia $barbearia)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenant.login', $barbearia->slug);
    }
}
