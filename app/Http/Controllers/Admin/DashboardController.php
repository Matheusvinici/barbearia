<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Historia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlunos = Aluno::count();
        $totalHistorias = Historia::count();
        $historiasHoje = Historia::whereDate('created_at', today())->count();
        $historiasConcluidas = Historia::where('status', 'concluido')->count();
        $ultimasHistorias = Historia::with('aluno')
            ->where('status', 'concluido')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAlunos',
            'totalHistorias',
            'historiasHoje',
            'historiasConcluidas',
            'ultimasHistorias'
        ));
    }
}
