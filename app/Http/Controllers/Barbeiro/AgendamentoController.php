<?php

namespace App\Http\Controllers\Barbeiro;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index()
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        $agendamentos = Agendamento::where('barbeiro_id', $barbeiro->id)
            ->whereDate('data', '>=', Carbon::today()->subDay())
            ->with('cliente', 'servicos')
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->paginate(20);

        return view('barbeiro.agendamentos.index', compact('agendamentos'));
    }

    public function confirmar(Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if ($agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        if ($agendamento->status !== 'pendente') {
            return redirect()->back()->with('error', 'Agendamento não está pendente.');
        }

        $agendamento->update(['status' => 'confirmado']);

        return redirect()->back()->with('success', 'Presença confirmada!');
    }

    public function realizar(Request $request, Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if ($agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        if (!in_array($agendamento->status, ['confirmado', 'pendente'])) {
            return redirect()->back()->with('error', 'Agendamento não pode ser marcado como realizado.');
        }

        $agendamento->update(['status' => 'realizado']);

        $this->registrarNoCaixa($agendamento);

        return redirect()->back()->with('success', 'Serviço marcado como realizado!');
    }

    public function cancelar(Agendamento $agendamento)
    {
        $barbeiro = Auth::guard('barbeiro')->user();

        if ($agendamento->barbeiro_id !== $barbeiro->id) {
            return redirect()->back()->with('error', 'Este agendamento não pertence a você.');
        }

        $agendamento->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Agendamento cancelado.');
    }

    private function registrarNoCaixa(Agendamento $agendamento)
    {
        $dataStr = Carbon::parse($agendamento->data)->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr)->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'data' => $dataStr,
                'saldo_inicial' => 0,
            ]);
        }

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $agendamento->total);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();

            CaixaMovimentacao::create([
                'caixa_id' => $caixa->id,
                'tipo' => 'entrada',
                'valor' => $agendamento->total,
                'descricao' => "Serviço realizado por {$agendamento->barbeiro->nome} - {$agendamento->cliente->nome}",
                'origem_type' => Agendamento::class,
                'origem_id' => $agendamento->id,
            ]);
        }
    }
}
