<?php

namespace App\Livewire\Site;

use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\BloqueioAgenda;
use App\Models\Cliente;
use App\Models\Configuracao;
use App\Models\Servico;
use App\Notifications\NovoAgendamentoBot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class AgendarWizard extends Component
{
    public $step = 1;
    public $cliente;

    public $barbearia_id;
    public $barbeiro_id;
    public $servico_id;
    public $servico;
    public $data;
    public $hora;

    public $barbearias;
    public $barbeiros;
    public $servicos;
    public $dias;
    public $horarios;

    public $success = false;
    public $agendamento;

    public function mount()
    {
        $clienteId = session('cliente_id');
        $this->cliente = $clienteId ? Cliente::find($clienteId) : null;

        if (!$this->cliente) {
            $this->redirect(route('site.login'));
        }

        $this->barbearias = Barbearia::whereHas('barbeiros', function ($q) {
            $q->where('ativo', true);
        })->orWhereDoesntHave('barbeiros')->orderBy('nome')->get();

        $this->servicos = Servico::where('ativo', true)->get();
    }

    public function selectBarbearia($id)
    {
        $this->barbearia_id = $id;
        $this->barbeiro_id = null;
        $this->servico_id = null;
        $this->servico = null;
        $this->data = null;
        $this->hora = null;
        $this->horarios = null;
        $this->dias = null;

        $this->barbeiros = Barbeiro::where('ativo', true)
            ->where('barbearia_id', $id)
            ->get();

        $this->step = 2;
    }

    public function selectBarbeiro($id)
    {
        $this->barbeiro_id = $id;
        $this->servico_id = null;
        $this->servico = null;
        $this->data = null;
        $this->hora = null;
        $this->horarios = null;
        $this->carregarDias();
        $this->step = 3;
    }

    public function selectServico($id)
    {
        $this->servico_id = $id;
        $this->servico = Servico::find($id);
        $this->data = null;
        $this->hora = null;
        $this->horarios = null;
        $this->carregarDias();
        $this->step = 4;
    }

    public function selectDia($data)
    {
        $this->data = $data;
        $this->hora = null;
        $this->carregarHorarios();
        $this->step = 4;
    }

    public function selectHora($hora)
    {
        $this->hora = $hora;
        $this->step = 5;
    }

    public function voltar()
    {
        $this->step = max(1, $this->step - 1);
    }

    public function confirmar()
    {
        $barbeiro = Barbeiro::find($this->barbeiro_id);
        $horaInicio = Carbon::parse($this->data . ' ' . $this->hora);
        $horaFim = $horaInicio->copy()->addMinutes($this->servico->duracao_minutos);

        $ag = Agendamento::create([
            'barbearia_id' => $this->barbearia_id,
            'barbeiro_id' => $this->barbeiro_id,
            'cliente_id' => $this->cliente->id,
            'data' => $this->data,
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => 'pendente',
            'total' => $this->servico->preco,
            'origem' => 'site',
        ]);

        $ag->servicos()->attach($this->servico->id, [
            'preco_praticado' => $this->servico->preco,
        ]);

        try {
            $adminUsers = \App\Models\User::all();
            Notification::send($adminUsers, new NovoAgendamentoBot($ag));
            if ($ag->barbeiro) {
                $ag->barbeiro->notify(new NovoAgendamentoBot($ag));
            }
        } catch (\Exception $e) {}

        $this->agendamento = $ag;
        $this->success = true;
    }

    public function novoAgendamento()
    {
        $this->resetExcept(['cliente', 'barbearias', 'servicos']);
        $this->step = 1;
    }

    private function carregarDias()
    {
        $barbeiro = Barbeiro::with('horarios')->find($this->barbeiro_id);
        $horariosBarbeiro = $barbeiro?->horarios->where('ativo', true);

        if ($horariosBarbeiro && $horariosBarbeiro->isNotEmpty()) {
            $diasArray = $horariosBarbeiro->pluck('dia_semana')->unique()->values()->toArray();
        } else {
            $diasFuncionamento = Configuracao::get('dias_funcionamento', '1,2,3,4,5,6');
            $diasArray = array_map('intval', explode(',', $diasFuncionamento));
        }

        $dias = [];
        $hoje = Carbon::today();
        Carbon::setLocale('pt_BR');

        for ($i = 0; $i < 14; $i++) {
            $data = $hoje->copy()->addDays($i);
            $diaSemana = $data->dayOfWeek;
            if (!in_array($diaSemana, $diasArray)) continue;

            if ($this->temHorariosDisponiveis($this->barbeiro_id, $data->format('Y-m-d'))) {
                $dias[] = [
                    'data' => $data->format('Y-m-d'),
                    'label' => $data->isoFormat('dddd'),
                    'dia' => $data->isoFormat('dddd'),
                    'mes' => $data->isoFormat('MMM'),
                ];
            }
        }

        $this->dias = $dias;
    }

    private function carregarHorarios()
    {
        $agendamentos = Agendamento::where('barbeiro_id', $this->barbeiro_id)
            ->whereDate('data', $this->data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->get(['hora_inicio', 'hora_fim']);

        $bloqueios = BloqueioAgenda::where('barbeiro_id', $this->barbeiro_id)
            ->whereDate('data', $this->data)
            ->get(['hora_inicio', 'hora_fim']);

        $diaSemana = Carbon::parse($this->data)->dayOfWeek;
        $barbeiro = Barbeiro::with('horarios')->find($this->barbeiro_id);
        $horariosBarbeiro = $barbeiro?->horarios->where('ativo', true);
        $periodos = $horariosBarbeiro?->where('dia_semana', $diaSemana);

        $faixas = [];
        if ($periodos && $periodos->isNotEmpty()) {
            foreach ($periodos as $p) {
                $faixas[] = ['inicio' => $p->hora_inicio, 'fim' => $p->hora_fim];
            }
        } elseif ($horariosBarbeiro && $horariosBarbeiro->isNotEmpty()) {
            $this->horarios = [];
            return;
        } else {
            $abertura = Configuracao::get('horario_abertura', '08:00');
            $fechamento = Configuracao::get('horario_fechamento', '18:00');
            $faixas[] = ['inicio' => $abertura, 'fim' => $fechamento];
        }

        $intervalo = (int) Configuracao::get('intervalo_minutos', '30');
        $horarios = [];

        foreach ($faixas as $faixa) {
            $inicio = Carbon::parse($this->data . ' ' . $faixa['inicio']);
            $fim = Carbon::parse($this->data . ' ' . $faixa['fim']);

            while ($inicio < $fim) {
                $fimSlot = $inicio->copy()->addMinutes($intervalo);
                $disponivel = true;

                foreach ($agendamentos as $ag) {
                    $hi = $ag->hora_inicio instanceof \Carbon\Carbon ? $ag->hora_inicio->format('H:i') : $ag->hora_inicio;
                    $hf = $ag->hora_fim instanceof \Carbon\Carbon ? $ag->hora_fim->format('H:i') : $ag->hora_fim;
                    $agInicio = Carbon::parse($this->data . ' ' . $hi);
                    $agFim = Carbon::parse($this->data . ' ' . $hf);
                    if ($inicio < $agFim && $fimSlot > $agInicio) {
                        $disponivel = false;
                        break;
                    }
                }
                foreach ($bloqueios as $bl) {
                    $hi = $bl->hora_inicio instanceof \Carbon\Carbon ? $bl->hora_inicio->format('H:i') : $bl->hora_inicio;
                    $hf = $bl->hora_fim instanceof \Carbon\Carbon ? $bl->hora_fim->format('H:i') : $bl->hora_fim;
                    $blInicio = Carbon::parse($this->data . ' ' . $hi);
                    $blFim = Carbon::parse($this->data . ' ' . $hf);
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
        }

        $this->horarios = $horarios;
    }

    private function temHorariosDisponiveis($barbeiroId, $data)
    {
        $agendamentos = Agendamento::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->count();

        $bloqueios = BloqueioAgenda::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->count();

        $diaSemana = Carbon::parse($data)->dayOfWeek;
        $barbeiro = Barbeiro::with('horarios')->find($barbeiroId);
        $horariosBarbeiro = $barbeiro?->horarios->where('ativo', true);
        $periodos = $horariosBarbeiro?->where('dia_semana', $diaSemana);

        $faixas = [];
        if ($periodos && $periodos->isNotEmpty()) {
            foreach ($periodos as $p) {
                $faixas[] = ['inicio' => $p->hora_inicio, 'fim' => $p->hora_fim];
            }
        } elseif ($horariosBarbeiro && $horariosBarbeiro->isNotEmpty()) {
            return false;
        } else {
            $abertura = Configuracao::get('horario_abertura', '08:00');
            $fechamento = Configuracao::get('horario_fechamento', '18:00');
            $faixas[] = ['inicio' => $abertura, 'fim' => $fechamento];
        }

        $intervalo = (int) Configuracao::get('intervalo_minutos', '30');

        $totalSlots = 0;
        foreach ($faixas as $faixa) {
            $inicio = Carbon::parse($data . ' ' . $faixa['inicio']);
            $fim = Carbon::parse($data . ' ' . $faixa['fim']);
            while ($inicio < $fim) {
                $totalSlots++;
                $inicio->addMinutes($intervalo);
            }
        }

        return ($agendamentos + $bloqueios) < $totalSlots;
    }

    public function render()
    {
        return view('livewire.site.agendar-wizard')
            ->layout('layouts.site');
    }
}
