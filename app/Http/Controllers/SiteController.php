<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Historia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiteController extends Controller
{
    public function welcome()
    {
        return view('site.welcome');
    }

    public function entrar()
    {
        return view('site.entrar');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'serie' => 'required|string|max:100',
        ]);

        $aluno = Aluno::firstOrCreate(
            ['nome' => $request->nome, 'serie' => $request->serie]
        );

        session([
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->nome,
        ]);

        return redirect()->route('site.biblioteca');
    }

    public function biblioteca()
    {
        if (!session('aluno_id')) {
            return redirect()->route('site.entrar');
        }

        $aluno = Aluno::with('historias')->find(session('aluno_id'));
        $historias = $aluno->historias()->orderBy('created_at', 'desc')->get();
        $historiasEmAndamento = $historias->where('status', 'rascunho');
        $historiasConcluidas = $historias->where('status', 'concluido');

        return view('site.biblioteca', compact('aluno', 'historiasEmAndamento', 'historiasConcluidas'));
    }

    public function sair()
    {
        session()->forget(['aluno_id', 'aluno_nome', 'historia_id']);
        return redirect()->route('site.welcome');
    }
}
