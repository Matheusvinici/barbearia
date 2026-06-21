<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Cliente;
use App\Models\Servico;
use App\Models\BloqueioAgenda;
use App\Models\Configuracao;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use App\Models\ClientePlanoUso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index()
    {
        $data = request('data', Carbon::today()->format('Y-m-d'));
        $barbeiroId = request('barbeiro_id');
        $barbeariaId = request('barbearia_id');

        $query = Agendamento::with(['barbeiro', 'cliente', 'servicos'])
            ->whereDate('data', $data);

        if ($barbeariaId) {
            $query->where('barbearia_id', $barbeariaId);
        }

        if ($barbeiroId) {
            $query->where('barbeiro_id', $barbeiroId);
        }

        $agendamentos = $query->orderBy('hora_inicio')->get();
        $barbeiros = Barbeiro::where('ativo', true)->get();
        $servicos = Servico::where('ativo', true)->get();
        $barbearias = Barbearia::orderBy('nome')->get();

        return view('admin.agendamentos.index', compact(
            'agendamentos', 'barbeiros', 'servicos', 'barbearias', 'data', 'barbeiroId', 'barbeariaId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'cliente_id' => 'required|exists:clientes,id',
            'servico_ids' => 'required|array',
            'servico_ids.*' => 'exists:servicos,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'usar_plano' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $servicos = Servico::whereIn('id', $data['servico_ids'])->get();
        $totalMinutos = $servicos->sum('duracao_minutos');
        $totalValor = $servicos->sum('preco');

        $horaInicio = Carbon::parse($data['data'] . ' ' . $data['hora_inicio']);
        $horaFim = $horaInicio->copy()->addMinutes($totalMinutos);

        $agendamento = Agendamento::create([
            'barbearia_id' => $data['barbearia_id'] ?? null,
            'barbeiro_id' => $data['barbeiro_id'],
            'cliente_id' => $data['cliente_id'],
            'data' => $data['data'],
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => 'pendente',
            'total' => $totalValor,
            'usar_plano' => $request->boolean('usar_plano'),
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
        $agendamento->load(['barbeiro', 'cliente', 'servicos', 'cliente.planos' => function ($q) {
            $q->where('ativo', true)->with('plano.quotas');
        }, 'planoUso']);
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
            'usar_plano' => 'boolean',
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
            'usar_plano' => $request->boolean('usar_plano'),
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
            if ($agendamento->usar_plano) {
                $this->registrarUsoPlano($agendamento);
            }
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
        $diaSemana = Carbon::parse($data)->dayOfWeek;

        $agendamentos = Agendamento::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->get(['hora_inicio', 'hora_fim']);

        $bloqueios = BloqueioAgenda::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->get(['hora_inicio', 'hora_fim']);

        $barbeiro = Barbeiro::with('horarios')->find($barbeiroId);
        $horarioBarbeiro = $barbeiro?->horarios->where('dia_semana', $diaSemana)->where('ativo', true)->first();

        if ($horarioBarbeiro) {
            $abertura = $horarioBarbeiro->hora_inicio;
            $fechamento = $horarioBarbeiro->hora_fim;
        } else {
            $abertura = Configuracao::get('horario_abertura', '08:00');
            $fechamento = Configuracao::get('horario_fechamento', '18:00');
        }

        $intervalo = (int) Configuracao::get('intervalo_minutos', '30');

        $horarios = [];
        $inicio = Carbon::parse($data . ' ' . $abertura);
        $fim = Carbon::parse($data . ' ' . $fechamento);

        while ($inicio < $fim) {
            $fimSlot = $inicio->copy()->addMinutes($intervalo);

            $disponivel = true;

            foreach ($agendamentos as $ag) {
                $hi = $ag->hora_inicio instanceof \Carbon\Carbon ? $ag->hora_inicio->format('H:i') : $ag->hora_inicio;
                $hf = $ag->hora_fim instanceof \Carbon\Carbon ? $ag->hora_fim->format('H:i') : $ag->hora_fim;
                $agInicio = Carbon::parse($data . ' ' . $hi);
                $agFim = Carbon::parse($data . ' ' . $hf);
                if ($inicio < $agFim && $fimSlot > $agInicio) {
                    $disponivel = false;
                    break;
                }
            }

            foreach ($bloqueios as $bl) {
                $hi = $bl->hora_inicio instanceof \Carbon\Carbon ? $bl->hora_inicio->format('H:i') : $bl->hora_inicio;
                $hf = $bl->hora_fim instanceof \Carbon\Carbon ? $bl->hora_fim->format('H:i') : $bl->hora_fim;
                $blInicio = Carbon::parse($data . ' ' . $hi);
                $blFim = Carbon::parse($data . ' ' . $hf);
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

    private function registrarUsoPlano(Agendamento $ag)
    {
        $ag->load('cliente.planos', 'servicos');
        $cp = $ag->cliente?->planos?->where('ativo', true)->first();
        if (!$cp) return;

        foreach ($ag->servicos as $servico) {
            ClientePlanoUso::create([
                'cliente_plano_id' => $cp->id,
                'agendamento_id' => $ag->id,
                'servico_id' => $servico->id,
                'usado_em' => now(),
            ]);
        }
    }

    private function registrarNoCaixa(Agendamento $agendamento)
    {
        $dataStr = $agendamento->data instanceof Carbon
            ? $agendamento->data->format('Y-m-d')
            : Carbon::parse($agendamento->data)->format('Y-m-d');

        $caixa = Caixa::whereDate('data', $dataStr)->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'data' => $dataStr,
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
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
                'descricao' => "Serviço realizado - {$agendamento->cliente->nome}",
                'origem_type' => Agendamento::class,
                'origem_id' => $agendamento->id,
                'user_id' => Auth::guard('web')->id(),
            ]);
        }
    }
}
