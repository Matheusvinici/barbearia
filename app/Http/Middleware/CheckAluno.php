<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAluno
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('aluno_id')) {
            return redirect()->route('site.entrar');
        }

        return $next($request);
    }
}
