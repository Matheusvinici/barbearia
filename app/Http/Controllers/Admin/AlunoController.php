<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;

class AlunoController extends Controller
{
    public function index()
    {
        $alunos = Aluno::withCount('historias')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.alunos.index', compact('alunos'));
    }

    public function show($id)
    {
        $aluno = Aluno::with('historias')->findOrFail($id);
        $historias = $aluno->historias()->orderBy('created_at', 'desc')->get();

        return view('admin.alunos.show', compact('aluno', 'historias'));
    }
}
