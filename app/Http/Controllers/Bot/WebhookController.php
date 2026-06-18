<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Barbeiro;
use App\Models\Servico;
use App\Models\Cliente;
use App\Models\Agendamento;
use App\Models\BloqueioAgenda;
use App\Models\Configuracao;
use App\Notifications\NovoAgendamentoBot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LembreteAgendamento;

class WebhookController extends Controller
{
    public function barbeiros()
    {
        $barbeiros = Barbeiro::where('ativo', true)->get(['id', 'nome']);
        return response()->json($barbeiros);
    }

    public function servicos()
    {
        $servicos = Servico::where('ativo', true)->get(['id', 'nome', 'preco', 'duracao_minutos']);
        return response()->json($servicos);
    }

    public function horarios(Request $request)
    {
        $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date_format:Y-m-d',
        ]);

        $barbeiroId = $request->barbeiro_id;
        $data = $request->data;

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
                $agInicio = Carbon::parse($data . ' ' . $ag->hora_inicio->format('H:i'));
                $agFim = Carbon::parse($data . ' ' . $ag->hora_fim->format('H:i'));
                if ($inicio < $agFim && $fimSlot > $agInicio) {
                    $disponivel = false;
                    break;
                }
            }

            foreach ($bloqueios as $bl) {
                $blInicio = Carbon::parse($data . ' ' . $bl->hora_inicio->format('H:i'));
                $blFim = Carbon::parse($data . ' ' . $bl->hora_fim->format('H:i'));
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

    public function diasDisponiveis(Request $request)
    {
        $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
        ]);

        $barbeiroId = $request->barbeiro_id;
        $diasFuncionamento = Configuracao::get('dias_funcionamento', '1,2,3,4,5,6');
        $diasArray = array_map('intval', explode(',', $diasFuncionamento));

        $dias = [];
        $hoje = Carbon::today();
        Carbon::setLocale('pt_BR');

        for ($i = 0; $i < 14; $i++) {
            $data = $hoje->copy()->addDays($i);
            $diaSemana = $data->dayOfWeek;

            if (!in_array($diaSemana, $diasArray)) {
                continue;
            }

            $temVaga = $this->temHorariosDisponiveis($barbeiroId, $data->format('Y-m-d'));

            if ($temVaga) {
                $dias[] = [
                    'data' => $data->format('Y-m-d'),
                    'label' => $data->isoFormat('dddd, D [de] MMMM'),
                ];
            }
        }

        return response()->json($dias);
    }

    public function agendar(Request $request)
    {
        $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'servico_id' => 'required|exists:servicos,id',
            'data' => 'required|date_format:Y-m-d',
            'hora' => 'required|date_format:H:i',
            'cliente_nome' => 'required|string|max:255',
            'cliente_telefone' => 'required|string|max:20',
        ]);

        $servico = Servico::findOrFail($request->servico_id);
        $horaInicio = Carbon::parse($request->data . ' ' . $request->hora);
        $horaFim = $horaInicio->copy()->addMinutes($servico->duracao_minutos);

        $cliente = Cliente::firstOrCreate(
            ['telefone' => $request->cliente_telefone],
            ['nome' => $request->cliente_nome]
        );

        if ($cliente->nome === 'Cliente WhatsApp' || $cliente->nome !== $request->cliente_nome) {
            $cliente->update(['nome' => $request->cliente_nome]);
        }

        if ($request->has('whatsapp_id')) {
            $cliente->update(['whatsapp_id' => $request->whatsapp_id]);
        }

        $agendamento = Agendamento::create([
            'barbeiro_id' => $request->barbeiro_id,
            'cliente_id' => $cliente->id,
            'data' => $request->data,
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => 'pendente',
            'total' => $servico->preco,
            'origem' => 'bot',
        ]);

        $agendamento->servicos()->attach($servico->id, [
            'preco_praticado' => $servico->preco,
        ]);

        try {
            $adminUsers = \App\Models\User::all();
            Notification::send($adminUsers, new NovoAgendamentoBot($agendamento));
        } catch (\Exception $e) {
        }

        return response()->json([
            'success' => true,
            'agendamento_id' => $agendamento->id,
            'message' => 'Agendamento confirmado!',
            'data' => $request->data,
            'hora' => $request->hora,
            'barbeiro' => $agendamento->barbeiro->nome,
            'servico' => $servico->nome,
            'preco' => $servico->preco,
        ]);
    }

    public function lembretes(Request $request)
    {
        $agora = Carbon::now();
        $daqui1h = $agora->copy()->addHour();

        $agendamentos = Agendamento::whereDate('data', $agora->format('Y-m-d'))
            ->whereIn('status', ['pendente', 'confirmado'])
            ->whereTime('hora_inicio', '>=', $agora->format('H:i:s'))
            ->whereTime('hora_inicio', '<=', $daqui1h->format('H:i:s'))
            ->with('cliente', 'barbeiro', 'servicos')
            ->get();

        return response()->json($agendamentos->map(function ($ag) {
            return [
                'id' => $ag->id,
                'cliente_nome' => $ag->cliente->nome,
                'cliente_telefone' => $ag->cliente->telefone,
                'barbeiro_nome' => $ag->barbeiro->nome,
                'data' => $ag->data->format('Y-m-d'),
                'hora' => $ag->hora_inicio,
                'servicos' => $ag->servicos->pluck('nome')->implode(', '),
            ];
        }));
    }

    public function verificarCliente($whatsappId)
    {
        $cliente = Cliente::where('whatsapp_id', $whatsappId)
            ->orWhere('telefone', $whatsappId)
            ->first(['nome']);

        if ($cliente && $cliente->nome !== 'Cliente WhatsApp') {
            return response()->json(['exists' => true, 'nome' => $cliente->nome]);
        }

        return response()->json(['exists' => false]);
    }

    private function temHorariosDisponiveis(int $barbeiroId, string $data): bool
    {
        $agendamentos = Agendamento::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->count();

        $bloqueios = BloqueioAgenda::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->count();

        $abertura = Configuracao::get('horario_abertura', '08:00');
        $fechamento = Configuracao::get('horario_fechamento', '18:00');
        $intervalo = (int) Configuracao::get('intervalo_minutos', '30');

        $totalSlots = 0;
        $inicio = Carbon::parse($data . ' ' . $abertura);
        $fim = Carbon::parse($data . ' ' . $fechamento);

        while ($inicio < $fim) {
            $totalSlots++;
            $inicio->addMinutes($intervalo);
        }

        $slotsOcupados = $agendamentos + $bloqueios;

        return $slotsOcupados < $totalSlots;
    }
}
