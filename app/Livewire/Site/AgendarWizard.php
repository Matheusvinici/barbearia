<?php

namespace App\Livewire\Site;

use App\Models\Agendamento;
use App\Models\Avaliacao;
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

    public $nome;
    public $telefone;
    public $step1_pedir_nome = false;
    public $slug;

    public $avaliacoes;
    public $barbeariaAtual;

    public function mount()
    {
        $clienteId = session('cliente_id');
        $this->cliente = $clienteId ? Cliente::find($clienteId) : null;
        if ($this->cliente) {
            $this->nome = $this->cliente->nome;
            $this->telefone = $this->cliente->telefone;
        } else {
            $this->telefone = session('telefone', '');
        }

        $this->step = 0;

        $route = request()->route();
        $barbearia = $route->parameter('barbearia');

        if ($barbearia) {
            $this->slug = $barbearia->slug;
            $this->barbeariaAtual = $barbearia;
            $filiais = $barbearia->filiais;
            if ($filiais->isNotEmpty()) {
                $this->barbearias = collect([$barbearia])->merge($filiais);
            } else {
                $this->barbearias = collect([$barbearia]);
            }
            $this->avaliacoes = Avaliacao::where('barbearia_id', $barbearia->id)
                ->latest()
                ->take(10)
                ->get();
        } else {
            $this->barbearias = Barbearia::whereHas('barbeiros', function ($q) {
                $q->where('ativo', true);
            })->orWhereDoesntHave('barbeiros')->orderBy('nome')->get();
        }

        $this->servicos = Servico::where('ativo', true)->get();
    }

    public function iniciarAgendamento()
    {
        $this->step = $this->cliente ? 2 : 1;
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

        $this->step = 3;
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
        $this->step = 4;
    }

    public function selectServico($id)
    {
        $this->servico_id = $id;
        $this->servico = Servico::find($id);
        $this->data = null;
        $this->hora = null;
        $this->horarios = null;
        $this->carregarDias();
        $this->step = 5;
    }

    public function selectDia($data)
    {
        $this->data = $data;
        $this->hora = null;
        $this->carregarHorarios();
        $this->step = 5;
    }

    public function selectHora($hora)
    {
        $this->hora = $hora;
        $this->step = 5;
    }

    public function avancarStep1()
    {
        $telefone = preg_replace('/\D/', '', $this->telefone);
        if (strlen($telefone) < 10) {
            session()->flash('error', 'Digite um telefone válido com DDD.');
            return;
        }

        $cliente = Cliente::where('telefone', $telefone)->first();
        if ($cliente) {
            $this->cliente = $cliente;
            $this->nome = $cliente->nome;
            session(['cliente_id' => $cliente->id, 'cliente_nome' => $cliente->nome, 'telefone' => $telefone]);
            $this->step = 2;
            return;
        }

        if (!$this->step1_pedir_nome) {
            $this->step1_pedir_nome = true;
            return;
        }

        if (strlen(trim($this->nome ?? '')) < 3) {
            session()->flash('error', 'Digite seu nome.');
            return;
        }

        $this->cliente = Cliente::create([
            'nome' => trim($this->nome),
            'telefone' => $telefone,
            'whatsapp_id' => $telefone,
        ]);
        session(['cliente_id' => $this->cliente->id, 'cliente_nome' => $this->cliente->nome, 'telefone' => $telefone]);
        $this->step = 2;
    }

    public function corrigirTelefone()
    {
        $this->step1_pedir_nome = false;
        $this->cliente = null;
        $this->nome = null;
        $this->telefone = null;
        $this->step = 1;
    }

    public function voltar()
    {
        $this->step = max(1, $this->step - 1);
    }

    public function confirmar()
    {
        if (!$this->cliente) {
            $telefone = preg_replace('/\D/', '', $this->telefone);
            if (strlen($telefone) < 10) {
                session()->flash('error', 'Digite um telefone válido com DDD.');
                return;
            }
            if (strlen(trim($this->nome ?? '')) < 3) {
                session()->flash('error', 'Digite seu nome.');
                return;
            }
            $this->cliente = Cliente::create([
                'nome' => trim($this->nome),
                'telefone' => $telefone,
                'whatsapp_id' => $telefone,
                'barbearia_id' => $this->barbearia_id,
            ]);
            session(['cliente_id' => $this->cliente->id, 'cliente_nome' => $this->cliente->nome]);
        }

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
        $this->resetExcept(['cliente', 'barbearias', 'servicos', 'telefone', 'nome', 'avaliacoes', 'barbeariaAtual']);
        $this->step = 0;
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
                    'label' => $data->translatedFormat('l'),
                    'dia' => $data->translatedFormat('l'),
                    'mes' => $data->translatedFormat('M'),
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

        $agora = Carbon::now();
        $hoje = $agora->format('Y-m-d');

        foreach ($faixas as $faixa) {
            $inicio = Carbon::parse($this->data . ' ' . $faixa['inicio']);
            $fim = Carbon::parse($this->data . ' ' . $faixa['fim']);

            while ($inicio < $fim) {
                if ($this->data === $hoje && $inicio <= $agora) {
                    $inicio->addMinutes($intervalo);
                    continue;
                }
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
