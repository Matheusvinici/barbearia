<?php

namespace App\Http\Middleware;

use App\Models\Barbearia;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $barbearia = $request->route('barbearia');

        // Resolve manually if route model binding didn't run yet
        if (is_string($barbearia)) {
            $barbearia = Barbearia::where('slug', $barbearia)->first();
            if ($barbearia) {
                $request->route()->setParameter('barbearia', $barbearia);
            }
        }

        if (!$barbearia instanceof Barbearia) {
            abort(404);
        }

        if (!$barbearia->isMatriz()) {
            abort(404);
        }

        $request->merge(['tenant' => $barbearia]);

        View::share('tenant', $barbearia);

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if (!$user->isSuperAdmin() && $barbearia->owner_id !== $user->id && !$user->hasAnyRole(['proprietario', 'admin'])) {
                abort(403, 'Você não tem acesso a esta barbearia.');
            }
        } elseif (Auth::guard('barbeiro')->check()) {
            $barbeiro = Auth::guard('barbeiro')->user();
            $tenantIds = $barbearia->tenantTreeIds();

            $pivotIds = $barbeiro->barbearias()->pluck('barbearias.id')->toArray();
            $acessibleIds = array_values(array_unique(array_merge(
                [$barbeiro->barbearia_id],
                $pivotIds
            )));

            $hasAccess = !empty(array_intersect($acessibleIds, $tenantIds));

            if (!$hasAccess) {
                abort(403, 'Você não tem acesso a esta barbearia.');
            }
        }

        return $next($request);
    }
}
