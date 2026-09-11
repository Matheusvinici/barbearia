<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
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
use Illuminate\Support\Facades\Notification;
use App\Notifications\NovoAgendamentoBot;

class AgendamentoController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $data = request('data', Carbon::today()->format('Y-m-d'));
        $barbeiroId = request('barbeiro_id');
        $barbeariaId = request('barbearia_id');

        $userBarbeiro = Auth::guard('web')->user()?->barbeiro;
        if (!$barbeiroId && $userBarbeiro) {
            $barbeiroId = $userBarbeiro->id;
        }

        $query = Agendamento::with(['barbeiro', 'cliente', 'cliente.planos', 'servicos'])
            ->whereDate('data', $data);

        if ($this->isTenantContext()) {
            $query = $this->applyTenantScope($query);
        } elseif ($barbeariaId) {
            $query->where('barbearia_id', $barbeariaId);
        }

        if ($barbeiroId) {
            $query->where('barbeiro_id', $barbeiroId);
        }

        $agendamentos = $query
            ->orderByRaw("CASE status WHEN 'pendente' THEN 0 WHEN 'confirmado' THEN 1 WHEN 'cancelado' THEN 2 WHEN 'ausente' THEN 2 ELSE 3 END")
            ->orderBy('hora_inicio')
            ->get();

        $barbeirosQuery = Barbeiro::where('ativo', true);
        $barbeariasQuery = Barbearia::orderBy('nome');

        if ($this->isTenantContext()) {
            $treeIds = $this->tenantIds();
            $barbeirosQuery->whereIn('barbearia_id', $treeIds);
            $barbeariasQuery->whereIn('id', $treeIds);
        } elseif (!Auth::guard('web')->user()?->isSuperAdmin()) {
            $ownedIds = Auth::guard('web')->user()?->ownedBarbearias()->get()
                ->flatMap(fn($b) => $b->tenantTreeIds())
                ->unique()->values()->toArray() ?? [];
            if (!empty($ownedIds)) {
                $barbeirosQuery->whereIn('barbearia_id', $ownedIds);
                $barbeariasQuery->whereIn('id', $ownedIds);
            }
        }

        $barbeiros = $barbeirosQuery->get();
        $servicos = Servico::where('ativo', true)->get();
        $barbearias = $barbeariasQuery->get();

        return view('admin.agendamentos.index', compact(
            'agendamentos', 'barbeiros', 'servicos', 'barbearias', 'data', 'barbeiroId', 'barbeariaId'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_nome_manual' => 'nullable|string|max:255',
            'cliente_telefone_manual' => 'nullable|string|max:20',
            'nome_cliente' => 'nullable|string|max:255',
            'telefone_cliente' => 'nullable|string|max:20',
            'servico_ids' => 'required|array',
            'servico_ids.*' => 'exists:servicos,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'forma_pagamento' => 'nullable|string|max:50',
            'usar_plano' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        // Resolver cliente: aceita cliente_id existente OU nome/telefone manual (telefone opcional)
        $clienteId = $data['cliente_id'] ?? null;
        if (!$clienteId) {
            $nomeManual = trim($data['cliente_nome_manual'] ?? $data['nome_cliente'] ?? $request->input('cliente_nome_manual') ?? $request->input('nome_cliente') ?? '');
            $telManualRaw = $data['cliente_telefone_manual'] ?? $data['telefone_cliente'] ?? $request->input('cliente_telefone_manual') ?? $request->input('telefone_cliente') ?? '';
            $telManual = preg_replace('/\D/', '', (string) $telManualRaw);

            if ($nomeManual === '') {
                return back()->withErrors(['cliente_id' => 'Informe o cliente: selecione um existente ou digite o nome.'])->withInput();
            }

            // Se telefone foi informado, tenta reaproveitar cliente existente pelo telefone
            if ($telManual !== '' && strlen($telManual) >= 10) {
                $clienteExistente = Cliente::where('telefone', $telManual)->first();
                if ($clienteExistente) {
                    $clienteId = $clienteExistente->id;
                    // Opcional: atualiza nome se diferente
                    if (strtolower(trim($clienteExistente->nome)) !== strtolower($nomeManual)) {
                        // Mantém nome existente para não sobrescrever sem consentimento; apenas loga
                    }
                } else {
                    $cliente = Cliente::create([
                        'nome' => $nomeManual,
                        'telefone' => $telManual,
                        'barbearia_id' => $data['barbearia_id'] ?? $this->tenantId(),
                    ]);
                    $clienteId = $cliente->id;
                }
            } else {
                // Telefone opcional/ausente: gera telefone único fictício para satisfazer constraint unique
                $telDummy = $telManual !== '' ? $telManual : '999' . time() . rand(100, 999);
                // Garante unicidade
                $attempts = 0;
                while (Cliente::where('telefone', $telDummy)->exists() && $attempts < 5) {
                    $telDummy = '999' . time() . rand(100, 999) . $attempts;
                    $attempts++;
                }
                // Se ainda existir, adiciona microtime
                if (Cliente::where('telefone', $telDummy)->exists()) {
                    $telDummy = '999' . microtime(true) * 10000 . rand(10, 99);
                    $telDummy = substr(preg_replace('/\D/', '', $telDummy), 0, 20);
                }
                $cliente = Cliente::create([
                    'nome' => $nomeManual,
                    'telefone' => $telDummy,
                    'barbearia_id' => $data['barbearia_id'] ?? $this->tenantId(),
                ]);
                $clienteId = $cliente->id;
            }
        }

        if ($this->isTenantContext() && empty($data['barbearia_id'])) {
            $data['barbearia_id'] = $this->tenantId();
        }

        $servicos = Servico::whereIn('id', $data['servico_ids'])->get();
        $totalMinutos = (int) $servicos->sum('duracao_minutos');
        if ($totalMinutos <= 0) $totalMinutos = 30;
        $totalValor = $servicos->sum('preco');

        $horaInicio = Carbon::parse($data['data'] . ' ' . $data['hora_inicio']);
        $horaFim = $horaInicio->copy()->addMinutes((int) $totalMinutos);

        // Validação de conflito: evita sobreposição com agendamentos/bloqueios (mesma lógica de horariosDisponiveis)
        $diaSemanaStore = Carbon::parse($data['data'])->dayOfWeek;
        $agendamentosDia = Agendamento::where('barbeiro_id', $data['barbeiro_id'])
            ->whereDate('data', $data['data'])
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->get(['hora_inicio', 'hora_fim']);
        foreach ($agendamentosDia as $ag) {
            $hi = $ag->hora_inicio instanceof Carbon ? $ag->hora_inicio->format('H:i') : substr((string) $ag->hora_inicio, 0, 5);
            $hf = $ag->hora_fim instanceof Carbon ? $ag->hora_fim->format('H:i') : substr((string) $ag->hora_fim, 0, 5);
            $agIni = Carbon::parse($data['data'] . ' ' . $hi);
            $agFim = Carbon::parse($data['data'] . ' ' . $hf);
            if ($horaInicio < $agFim && $horaFim > $agIni) {
                return back()->withErrors(['hora_inicio' => 'Horário conflita com outro agendamento (' . $hi . '-' . $hf . '). Escolha outro.'])->withInput();
            }
        }
        $bloqueiosDia = BloqueioAgenda::where('barbeiro_id', $data['barbeiro_id'])
            ->where(function ($q) use ($data) {
                $q->whereDate('data', $data['data'])
                  ->orWhere('recorrente', true);
            })->get(['data', 'hora_inicio', 'hora_fim', 'recorrente', 'motivo']);
        foreach ($bloqueiosDia as $bl) {
            if ($bl->recorrente) {
                $blDia = Carbon::parse($bl->data)->dayOfWeek;
                if ($bl->data->format('Y-m-d') !== $data['data'] && $blDia !== $diaSemanaStore) {
                    continue;
                }
            } else {
                if ($bl->data->format('Y-m-d') !== $data['data']) continue;
            }
            $hi = $bl->hora_inicio instanceof Carbon ? $bl->hora_inicio->format('H:i') : substr((string) $bl->hora_inicio, 0, 5);
            $hf = $bl->hora_fim instanceof Carbon ? $bl->hora_fim->format('H:i') : substr((string) $bl->hora_fim, 0, 5);
            $blIni = Carbon::parse($data['data'] . ' ' . $hi);
            $blFim = Carbon::parse($data['data'] . ' ' . $hf);
            if ($horaInicio < $blFim && $horaFim > $blIni) {
                return back()->withErrors(['hora_inicio' => 'Horário bloqueado (' . $hi . '-' . $hf . ($bl->motivo ? ' - ' . $bl->motivo : '') . '). Escolha outro.'])->withInput();
            }
        }

        // Verifica se barbeiro atende no dia (se tem horários cadastrados mas não para este dia, bloqueia)
        $barbeiroCheck = Barbeiro::with('horarios')->find($data['barbeiro_id']);
        $horariosAtivos = $barbeiroCheck?->horarios->where('ativo', true);
        if ($horariosAtivos && $horariosAtivos->isNotEmpty()) {
            $temNoDia = $horariosAtivos->where('dia_semana', $diaSemanaStore)->isNotEmpty();
            if (!$temNoDia) {
                return back()->withErrors(['data' => 'Barbeiro não atende neste dia da semana. Escolha outro dia ou verifique a escala.'])->withInput();
            }
        }

        $agendamento = Agendamento::create([
            'barbearia_id' => $data['barbearia_id'] ?? null,
            'barbeiro_id' => $data['barbeiro_id'],
            'cliente_id' => $clienteId,
            'data' => $data['data'],
            'forma_pagamento' => $data['forma_pagamento'] ?? null,
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

        try {
            $adminUsers = \App\Models\User::all();
            Notification::send($adminUsers, new NovoAgendamentoBot($agendamento));
            if ($agendamento->barbeiro) {
                $agendamento->barbeiro->notify(new NovoAgendamentoBot($agendamento));
            }
        } catch (\Exception $e) {}

        $route = $this->isTenantContext()
            ? route('tenant.admin.agendamentos.index', [$this->getTenant()->slug, 'data' => $data['data']])
            : route('admin.agendamentos.index', ['data' => $data['data']]);

        return redirect()->to($route)->with('success', 'Agendamento criado com sucesso!');
    }

    private function getAgendamentoFromRoute(Request $request): Agendamento
    {
        $param = $request->route('agendamento');
        return $param instanceof Agendamento ? $param : Agendamento::findOrFail((int) $param);
    }

    public function show(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        $agendamento->load(['barbeiro', 'cliente', 'servicos', 'cliente.planos' => function ($q) {
            $q->where('ativo', true)->with('plano.quotas');
        }, 'planoUso']);
        return view('admin.agendamentos.show', compact('agendamento'));
    }

    public function edit(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        $agendamento->load('servicos');
        $barbeirosQuery = Barbeiro::where('ativo', true);
        $servicos = Servico::where('ativo', true)->get();

        if ($this->isTenantContext()) {
            $barbeirosQuery->whereIn('barbearia_id', $this->tenantIds());
        } elseif (!Auth::guard('web')->user()?->isSuperAdmin()) {
            $ownedIds = Auth::guard('web')->user()?->ownedBarbearias()->get()
                ->flatMap(fn($b) => $b->tenantTreeIds())
                ->unique()->values()->toArray() ?? [];
            if (!empty($ownedIds)) {
                $barbeirosQuery->whereIn('barbearia_id', $ownedIds);
            }
        }

        $barbeiros = $barbeirosQuery->get();

        return view('admin.agendamentos.form', compact('agendamento', 'barbeiros', 'servicos'));
    }

    public function update(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        $data = $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'servico_ids' => 'required|array',
            'servico_ids.*' => 'exists:servicos,id',
            'status' => 'required|in:pendente,confirmado,realizado,cancelado,ausente',
            'forma_pagamento' => 'nullable|string|max:50',
            'usar_plano' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $servicos = Servico::whereIn('id', $data['servico_ids'])->get();
        $totalMinutos = (int) $servicos->sum('duracao_minutos');
        if ($totalMinutos <= 0) $totalMinutos = 30;
        $totalValor = $servicos->sum('preco');

        $horaInicio = Carbon::parse($data['data'] . ' ' . $data['hora_inicio']);
        $horaFim = $horaInicio->copy()->addMinutes((int) $totalMinutos);

        $oldStatus = $agendamento->status;

        $agendamento->update([
            'barbeiro_id' => $data['barbeiro_id'],
            'data' => $data['data'],
            'hora_inicio' => $horaInicio->format('H:i'),
            'hora_fim' => $horaFim->format('H:i'),
            'status' => $data['status'],
            'total' => $totalValor,
            'forma_pagamento' => $data['forma_pagamento'] ?? null,
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

        $route = $this->isTenantContext()
            ? route('tenant.admin.agendamentos.index', [$this->getTenant()->slug, 'data' => $agendamento->data->format('Y-m-d')])
            : route('admin.agendamentos.index', ['data' => $agendamento->data->format('Y-m-d')]);

        return redirect()->to($route)->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function confirmar(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        if ($agendamento->status !== 'pendente') {
            return redirect()->back()->with('error', 'Agendamento não está pendente.');
        }
        $data = $request->validate(['forma_pagamento' => 'required|string|max:50']);
        $agendamento->update(['status' => 'confirmado', 'forma_pagamento' => $data['forma_pagamento']]);
        return redirect()->back()->with('success', 'Agendamento confirmado com sucesso!');
    }

    public function realizar(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        if (!in_array($agendamento->status, ['pendente', 'confirmado'])) {
            return redirect()->back()->with('error', 'Agendamento não pode ser realizado.');
        }
        $data = $request->validate(['forma_pagamento' => 'required|string|max:50']);
        $agendamento->update(['status' => 'realizado', 'forma_pagamento' => $data['forma_pagamento']]);
        $this->registrarNoCaixa($agendamento);
        if ($agendamento->usar_plano) {
            $this->registrarUsoPlano($agendamento);
        }
        return redirect()->back()->with('success', 'Serviço realizado com sucesso!');
    }

    public function destroy(Request $request)
    {
        $agendamento = $this->getAgendamentoFromRoute($request);
        $agendamento->servicos()->detach();
        $agendamento->delete();
        return response()->json(['success' => true, 'message' => 'Agendamento excluído com sucesso']);
    }

    public function horariosDisponiveis(Request $request)
    {
        $request->validate([
            'barbeiro_id' => 'required|exists:barbeiros,id',
            'data' => 'required|date',
            'servico_ids' => 'nullable|array',
            'servico_ids.*' => 'exists:servicos,id',
        ]);

        $data = $request->data;
        $barbeiroId = $request->barbeiro_id;
        $diaSemana = Carbon::parse($data)->dayOfWeek;

        $agendamentos = Agendamento::where('barbeiro_id', $barbeiroId)
            ->whereDate('data', $data)
            ->whereNotIn('status', ['cancelado', 'ausente'])
            ->get(['hora_inicio', 'hora_fim']);

        // Bloqueios: considera data exata + recorrentes semanais (mesmo dia da semana)
        $bloqueiosQuery = BloqueioAgenda::where('barbeiro_id', $barbeiroId)
            ->where(function ($q) use ($data) {
                $q->whereDate('data', $data)
                  ->orWhere('recorrente', true);
            })
            ->get(['data', 'hora_inicio', 'hora_fim', 'recorrente']);

        $bloqueios = $bloqueiosQuery->filter(function ($bl) use ($data, $diaSemana) {
            if ($bl->recorrente) {
                // Se é recorrente, verifica se o dia da semana bate
                $blDia = Carbon::parse($bl->data)->dayOfWeek;
                // Se a data do bloqueio é a mesma que a consultada, sempre considera
                if ($bl->data->format('Y-m-d') === $data) {
                    return true;
                }
                return $blDia === $diaSemana;
            }
            return $bl->data->format('Y-m-d') === $data;
        })->values();

        $barbeiro = Barbeiro::with('horarios')->find($barbeiroId);
        $horariosBarbeiro = $barbeiro?->horarios->where('ativo', true);
        $periodos = $horariosBarbeiro?->where('dia_semana', $diaSemana);

        $faixas = [];
        if ($periodos && $periodos->isNotEmpty()) {
            foreach ($periodos as $p) {
                $hi = $p->hora_inicio instanceof Carbon ? $p->hora_inicio->format('H:i') : (is_string($p->hora_inicio) ? substr($p->hora_inicio, 0, 5) : Carbon::parse($p->hora_inicio)->format('H:i'));
                $hf = $p->hora_fim instanceof Carbon ? $p->hora_fim->format('H:i') : (is_string($p->hora_fim) ? substr($p->hora_fim, 0, 5) : Carbon::parse($p->hora_fim)->format('H:i'));
                $faixas[] = ['inicio' => $hi, 'fim' => $hf];
            }
        } elseif ($horariosBarbeiro && $horariosBarbeiro->isNotEmpty()) {
            // Barbeiro tem horário cadastrado mas não atende neste dia
            return response()->json([]);
        } else {
            // Fallback: tenta usar horários da barbearia do barbeiro ou global
            $barbearia = $barbeiro?->barbearia;
            // Tenta buscar faixas agregadas de BarbeiroHorario da barbearia (como no wizard)
            $horariosDaBarbearia = collect();
            if ($barbearia) {
                $horariosDaBarbearia = \App\Models\BarbeiroHorario::where('ativo', true)
                    ->whereHas('barbeiro', function ($q) use ($barbearia) {
                        $q->where('ativo', true)
                          ->where(function ($q2) use ($barbearia) {
                              $q2->where('barbearia_id', $barbearia->id)->orWhereNull('barbearia_id');
                          });
                    })
                    ->where('dia_semana', $diaSemana)
                    ->get(['hora_inicio', 'hora_fim']);
            }

            if ($horariosDaBarbearia->isNotEmpty()) {
                $ab = $horariosDaBarbearia->min('hora_inicio');
                $fe = $horariosDaBarbearia->max('hora_fim');
                $abStr = $ab instanceof Carbon ? $ab->format('H:i') : substr((string) $ab, 0, 5);
                $feStr = $fe instanceof Carbon ? $fe->format('H:i') : substr((string) $fe, 0, 5);
                $faixas[] = ['inicio' => $abStr, 'fim' => $feStr];
            } else {
                // Verifica dias de funcionamento da barbearia / global
                if ($barbearia) {
                    $diasFunc = array_map('intval', explode(',', $barbearia->dias_funcionamento ?? Configuracao::get('dias_funcionamento', '1,2,3,4,5,6')));
                    if (!in_array($diaSemana, $diasFunc)) {
                        return response()->json([]);
                    }
                    $faixas[] = [
                        'inicio' => $barbearia->horario_abertura ?? Configuracao::get('horario_abertura', '08:00'),
                        'fim' => $barbearia->horario_fechamento ?? Configuracao::get('horario_fechamento', '18:00'),
                    ];
                } else {
                    $diasFunc = array_map('intval', explode(',', Configuracao::get('dias_funcionamento', '1,2,3,4,5,6')));
                    if (!in_array($diaSemana, $diasFunc)) {
                        return response()->json([]);
                    }
                    $faixas[] = [
                        'inicio' => Configuracao::get('horario_abertura', '08:00'),
                        'fim' => Configuracao::get('horario_fechamento', '18:00'),
                    ];
                }
            }
        }

        // Merge faixas contíguas (ex: 14:00-18:00 + 18:00-22:00 => 14:00-22:00) para serviços que atravessam períodos
        usort($faixas, fn($a,$b) => strcmp($a['inicio'], $b['inicio']));
        $merged = [];
        foreach ($faixas as $f) {
            if (empty($merged)) {
                $merged[] = $f;
            } else {
                $lastIdx = count($merged)-1;
                $lastFim = $merged[$lastIdx]['fim'];
                // Se a próxima faixa começa <= fim da anterior (contígua ou sobreposta), mescla
                if ($f['inicio'] <= $lastFim) {
                    if ($f['fim'] > $lastFim) $merged[$lastIdx]['fim'] = $f['fim'];
                } else {
                    $merged[] = $f;
                }
            }
        }
        $faixas = $merged;

        // Intervalo e duração do serviço (para checar se cabe no slot)
        $barbeariaIntervalo = $barbeiro?->barbearia?->intervalo_minutos ?? null;
        $intervalo = (int) ($barbeariaIntervalo ?? Configuracao::get('intervalo_minutos', '30'));

        $duracaoServico = null;
        if ($request->filled('servico_ids')) {
            $duracaoServico = (int) Servico::whereIn('id', $request->servico_ids)->sum('duracao_minutos');
            if ($duracaoServico <= 0) {
                $duracaoServico = $intervalo;
            }
        }
        $slotDuracao = (int) ($duracaoServico ?: $intervalo);

        $agora = Carbon::now();
        $hoje = $agora->format('Y-m-d');

        $horarios = [];
        foreach ($faixas as $faixa) {
            $inicio = Carbon::parse($data . ' ' . $faixa['inicio']);
            $fim = Carbon::parse($data . ' ' . $faixa['fim']);

            while ($inicio->copy()->addMinutes($slotDuracao) <= $fim || ($slotDuracao === $intervalo && $inicio < $fim)) {
                // Se for hoje, ignora horários já passados (igual wizard/bot)
                if ($data === $hoje && $inicio <= $agora) {
                    $inicio->addMinutes($intervalo);
                    continue;
                }

                // Para slot com duração custom, garante que o fim do serviço não ultrapasse o fim da faixa
                $fimSlot = $inicio->copy()->addMinutes($slotDuracao);
                if ($fimSlot > $fim) {
                    $inicio->addMinutes($intervalo);
                    continue;
                }

                $disponivel = true;

                foreach ($agendamentos as $ag) {
                    $hi = $ag->hora_inicio instanceof Carbon ? $ag->hora_inicio->format('H:i') : (string) $ag->hora_inicio;
                    $hf = $ag->hora_fim instanceof Carbon ? $ag->hora_fim->format('H:i') : (string) $ag->hora_fim;
                    $agInicio = Carbon::parse($data . ' ' . substr($hi, 0, 5));
                    $agFim = Carbon::parse($data . ' ' . substr($hf, 0, 5));
                    if ($inicio < $agFim && $fimSlot > $agInicio) {
                        $disponivel = false;
                        break;
                    }
                }

                if ($disponivel) {
                    foreach ($bloqueios as $bl) {
                        $hi = $bl->hora_inicio instanceof Carbon ? $bl->hora_inicio->format('H:i') : (string) $bl->hora_inicio;
                        $hf = $bl->hora_fim instanceof Carbon ? $bl->hora_fim->format('H:i') : (string) $bl->hora_fim;
                        $blInicio = Carbon::parse($data . ' ' . substr($hi, 0, 5));
                        $blFim = Carbon::parse($data . ' ' . substr($hf, 0, 5));
                        if ($inicio < $blFim && $fimSlot > $blInicio) {
                            $disponivel = false;
                            break;
                        }
                    }
                }

                if ($disponivel) {
                    $horarios[] = $inicio->format('H:i');
                }

                $inicio->addMinutes($intervalo);
            }
        }

        sort($horarios);

        return response()->json(array_values(array_unique($horarios)));
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

        $caixaQuery = Caixa::whereDate('data', $dataStr);

        if ($agendamento->barbearia_id) {
            $caixaQuery->where('barbearia_id', $agendamento->barbearia_id);
        } elseif ($this->isTenantContext()) {
            $caixaQuery->where('barbearia_id', $this->tenantId());
        }

        $caixa = $caixaQuery->first();

        if (!$caixa) {
            $caixa = Caixa::create([
                'barbearia_id' => $agendamento->barbearia_id ?? $this->tenantId(),
                'data' => $dataStr,
                'saldo_inicial' => 0,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]);
        }

    if (!$caixa->fechado) {
        $caixa->increment('total_entradas', $agendamento->total);
        $caixa->saldo_final = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
        $caixa->save();
    }

    CaixaMovimentacao::create([
        'barbearia_id' => $caixa->barbearia_id,
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
