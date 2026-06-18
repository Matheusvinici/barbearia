<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Barbeiro;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\BloqueioAgenda;
use App\Models\Configuracao;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index()
    {
        $data = request('data', Carbon::today()->format('Y-m-d'));
        $barbeiroId = request('barbeiro_id');

        $query = Agendamento::with(['barbeiro', 'cliente', 'servicos'])
            ->whereDate('data', $data);

        if ($barbeiroId) {
            $query->where('barbeiro_id', $barbeiroId);
        }

        $agendamentos = $query->orderBy('hora_inicio')->get();
        $barbeiros = Barbeiro::where('ativo', true)->get();
        $servicos = Servico::where('ativo', true)->get();

        return view('admin.agendamentos.index', compact(
            'agendamentos', 'barbeiros', 'servicos', 'data', 'barbeiroId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servico_ids' => 'required|array',
            'servico_ids.*' => 'exists:servicos,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'observacoes' => 'nullable|string',
        ]);

        $servicos = Servico::whereIn('id', $data['servico_ids'])->get();
        $totalMinutos = $servicos->sum('duracao_minutos');
        $totalValor = $servicos->sum('preco');

        $horaInicio = Carbon::parse($data['data'] . ' ' . $data['hora_inicio']);
        $horaFim = $horaInicio->copy()->addMinutes($totalMinutos);

        $agendamento = Agendamento::create([
            'barbeiro_id' => $data['barbeiro_id'],
            'cliente_id' => $data['cliente_id'],
            'data' => $data['data'],
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => 'pendente',
            'total' => $totalValor,
            'observacoes' => $data['observacoes'] ?? null,
            'created_by' => Auth::guard('web')->id(),
            'origem' => 'admin',
        ]);

        foreach ($servicos as $servico) {
            $agendamento->servicos()->attach($servico->id, [
                'preco_praticado' => $servico->preco,
            ]);
        }

        return redirect()->route('admin.agendamentos.index', ['data' => $data['data']])
            ->with('success', 'Agendamento criado com sucesso!');
    }

    public function show(Agendamento $agendamento)
    {
        $agendamento->load(['barbeiro', 'cliente', 'servicos']);
        return view('admin.agendamentos.show', compact('agendamento'));
    }

    public function edit(Agendamento $agendamento)
    {
        $agendamento->load('servicos');
        $barbeiros = Barbeiro::where('ativo', true)->get();
        $servicos = Servico::where('ativo', true)->get();

        return view('admin.agendamentos.form', compact('agendamento', 'barbeiros', 'servicos'));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $data = $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'servico_ids' => 'required|array',
            'servico_ids.*' => 'exists:servicos,id',
            'status' => 'required|in:pendente,confirmado,realizado,cancelado,ausente',
            'observacoes' => 'nullable|string',
        ]);

        $servicos = Servico::whereIn('id', $data['servico_ids'])->get();
        $totalMinutos = $servicos->sum('duracao_minutos');
        $totalValor = $servicos->sum('preco');

        $horaInicio = Carbon::parse($data['data'] . ' ' . $data['hora_inicio']);
        $horaFim = $horaInicio->copy()->addMinutes($totalMinutos);

        $oldStatus = $agendamento->status;

        $agendamento->update([
            'barbeiro_id' => $data['barbeiro_id'],
            'data' => $data['data'],
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => $data['status'],
            'total' => $totalValor,
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        $agendamento->servicos()->detach();
        foreach ($servicos as $servico) {
            $agendamento->servicos()->attach($servico->id, [
                'preco_praticado' => $servico->preco,
            ]);
        }

        if ($data['status'] === 'realizado' && $oldStatus !== 'realizado') {
            $this->registrarNoCaixa($agendamento);
        }

        return redirect()->route('admin.agendamentos.index', ['data' => $agendamento->data->format('Y-m-d')])
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Agendamento $agendamento)
    {
        $agendamento->servicos()->detach();
        $agendamento->delete();
        return response()->json(['success' => true, 'message' => 'Agendamento excluído com sucesso']);
    }

    public function horariosDisponiveis(Request $request)
    {
        $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date',
        ]);

        $data = $request->data;
        $barbeiroId = $request->barbeiro_id;

        $agendamentos = Agendamento::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->get(['hora_inicio', 'hora_fim']);

        $bloqueios = BloqueioAgenda::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->get(['hora_inicio', 'hora_fim']);

        $abertura = Configuracao::get('horario_abertura', '08:00');
        $fechamento = Configuracao::get('horario_fechamento', '18:00');
        $intervalo = (int) Configuracao::get('intervalo_minutos', '30');

        $horarios = [];
        $inicio = Carbon::parse($data . ' ' . $abertura);
        $fim = Carbon::parse($data . ' ' . $fechamento);

        while ($inicio < $fim) {
            $fimSlot = $inicio->copy()->addMinutes($intervalo);

            $disponivel = true;

            foreach ($agendamentos as $ag) {
                $agInicio = Carbon::parse($data . ' ' . $ag->hora_inicio);
                $agFim = Carbon::parse($data . ' ' . $ag->hora_fim);
                if ($inicio < $agFim && $fimSlot > $agInicio) {
                    $disponivel = false;
                    break;
                }
            }

            foreach ($bloqueios as $bl) {
                $blInicio = Carbon::parse($data . ' ' . $bl->hora_inicio);
                $blFim = Carbon::parse($data . ' ' . $bl->hora_fim);
                if ($inicio < $blFim && $fimSlot > $blInicio) {
                    $disponivel = false;
                    break;
                }
            }

            if ($disponivel) {
                $horarios[] = $inicio->format('H:i');
            }

            $inicio->addMinutes($intervalo);
        }

        return response()->json($horarios);
    }

    private function registrarNoCaixa(Agendamento $agendamento)
    {
        $data = $agendamento->data instanceof Carbon ? $agendamento->data : Carbon::parse($agendamento->data);

        $caixa = Caixa::firstOrCreate(
            ['data' => $data->format('Y-m-d')],
            [
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]
        );

        if (!$caixa->fechado) {
            $caixa->increment('total_entradas', $agendamento->total);
            $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
            $caixa->save();

            CaixaMovimentacao::create([
                'caixa_id' => $caixa->id,
                'tipo' => 'entrada',
                'valor' => $agendamento->total,
                'descricao' => "Serviço realizado - {$agendamento->cliente->nome}",
                'origem_type' => Agendamento::class,
                'origem_id' => $agendamento->id,
                'user_id' => Auth::guard('web')->id(),
            ]);
        }
    }
}
