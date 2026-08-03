<div>
    @php
        $stepLabels = ['', 'Login', 'Unidade', 'Profissional', 'Serviço', 'Data/Hora'];
        $stepTitles = ['', 'Identificação', 'Escolha a Unidade', 'Escolha o Profissional', 'Escolha o Serviço', 'Escolha Data e Hora'];
        $stepSubtitles = ['', '', 'Selecione a barbearia onde deseja ser atendido.', 'Selecione seu barbeiro favorito.', 'Selecione o serviço que deseja realizar.', 'Escolha o melhor dia e horário disponível.'];
        $currentStep = $step <= 5 ? $step : 0;
    @endphp

    {{-- ============================================================ --}}
    {{-- SUCCESS SCREEN --}}
    {{-- ============================================================ --}}
    @if($success)
    <div class="steps">
        @foreach(['Unidade', 'Serviço', 'Profissional', 'Data/Hora', 'Confirmação'] as $si)
        <div class="step-item done">
            <div class="step-num"><svg class="icon icon-xs"><use href="#i-check"/></svg></div>
            <span>{{ $si }}</span>
        </div>
        @if(!$loop->last)<div class="step-divider"></div>@endif
        @endforeach
    </div>

    <div style="display:flex;flex-direction:column;align-items:center;max-width:600px;margin:0 auto;">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--success-bg);display:grid;place-items:center;margin-bottom:24px;position:relative;animation:pop-in 0.6s cubic-bezier(0.34,1.56,0.64,1);">
            <div style="position:absolute;inset:-8px;border-radius:50%;border:2px solid var(--success);opacity:0.3;"></div>
            <svg style="width:40px;height:40px;color:var(--success);"><use href="#i-check"/></svg>
        </div>

        <h1 style="font-size:32px;font-weight:800;letter-spacing:-0.035em;text-align:center;margin-bottom:8px;">Agendamento Confirmado!</h1>
        <p style="font-size:16px;color:var(--text-muted);text-align:center;margin-bottom:32px;">Seu horário foi reservado com sucesso.</p>

        <div style="width:100%;background:var(--card);backdrop-filter:blur(20px);border:1px solid var(--border-strong);border-radius:var(--r-lg);overflow:hidden;position:relative;margin-bottom:24px;">
            <div style="position:absolute;width:24px;height:24px;background:var(--bg);border-radius:50%;top:50%;transform:translateY(-50%);left:-12px;z-index:2;"></div>
            <div style="position:absolute;width:24px;height:24px;background:var(--bg);border-radius:50%;top:50%;transform:translateY(-50%);right:-12px;z-index:2;"></div>
            <div style="background:var(--accent-glow);padding:20px 24px;border-bottom:2px dashed var(--border-strong);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:12px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.1em;">Protocolo #AG-{{ str_pad($agendamento->id, 4, '0', STR_PAD_LEFT) }}</span>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;padding:4px 10px;border-radius:999px;background:var(--success-bg);color:var(--success);">
                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor;animation:pulse-dot 2s infinite;"></span>
                    Confirmado
                </span>
            </div>
            <div style="padding:24px;">
                <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-building"/></svg></div><div class="sum-info"><span class="lbl">Barbearia</span><span class="val">{{ $agendamento->barbearia?->nome ?? '-' }}</span></div></div>
                <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-scissors-2"/></svg></div><div class="sum-info"><span class="lbl">Serviço</span><span class="val">{{ $agendamento->servicos->first()->nome ?? '-' }}</span></div></div>
                <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-user-tag"/></svg></div><div class="sum-info"><span class="lbl">Profissional</span><span class="val">{{ $agendamento->barbeiro->nome }}</span></div></div>
                <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-calendar"/></svg></div><div class="sum-info"><span class="lbl">Data</span><span class="val">{{ \Carbon\Carbon::parse($agendamento->data)->format('d/m/Y') }}</span></div></div>
                <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-clock"/></svg></div><div class="sum-info"><span class="lbl">Horário</span><span class="val">{{ $agendamento->hora_inicio->format('H:i') }}</span></div></div>
            </div>
        </div>

        <div style="width:100%;background:var(--info-bg);border:1px solid var(--info);border-radius:var(--r-md);padding:16px;display:flex;align-items:center;gap:14px;margin-bottom:32px;">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(96,165,250,0.2);color:var(--info);display:grid;place-items:center;flex-shrink:0;">
                <svg class="icon"><use href="#i-bell"/></svg>
            </div>
            <div style="font-size:14px;color:var(--text);line-height:1.4;">
                Você receberá uma <strong>notificação 1 hora antes</strong> pelo sistema. Mantenha-se logado no Meus Agendamentos.
            </div>
        </div>

        <div style="width:100%;display:flex;flex-direction:column;gap:12px;">
            <button class="btn-primary-c" style="width:100%;justify-content:center;height:52px;" wire:click="novoAgendamento">
                <svg class="icon icon-sm"><use href="#i-check"/></svg>
                Novo Agendamento
            </button>
            <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="btn-ghost-c" style="width:100%;justify-content:center;height:52px;text-decoration:none;">
                Ver Meus Agendamentos
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- WELCOME SCREEN (step === 0) --}}
    {{-- ============================================================ --}}
    @elseif($step == 0)
    <div class="welcome-header">
        @if($barbeariaAtual && $barbeariaAtual->logo)
        <img src="{{ $barbeariaAtual->logo_url }}" alt="{{ $barbeariaAtual->nome }}" class="welcome-logo">
        @elseif($barbeariaAtual)
        <div class="welcome-logo-placeholder"><svg style="width:32px;height:32px;"><use href="#i-scissor"/></svg></div>
        @else
        <div class="welcome-logo-placeholder"><svg style="width:32px;height:32px;"><use href="#i-scissor"/></svg></div>
        @endif
        <h1 class="welcome-title">{{ $barbeariaAtual?->nome ?? \App\Models\Configuracao::get('nome_barbearia', 'Studio Barber') }}</h1>
        @php $endereco = $barbeariaAtual ? trim(($barbeariaAtual->bairro ?? '') . ($barbeariaAtual->bairro && $barbeariaAtual->cidade ? ' - ' : '') . ($barbeariaAtual->cidade ?? '')) : ''; @endphp
        @if($endereco)
        <p class="welcome-address"><svg class="icon icon-sm" style="margin-right:4px;"><use href="#i-map-pin"/></svg> {{ $endereco }}</p>
        @else
        <p class="welcome-address" style="color:var(--text-muted);">Agende seu horário com praticidade</p>
        @endif
    </div>

    <button style="width:100%;padding:16px;font-size:16px;border-radius:14px;" class="btn-primary-c" wire:click="iniciarAgendamento">
        <svg class="icon"><use href="#i-calendar"/></svg>
        Agendar Horário
    </button>
    <div style="text-align:center;margin:8px 0 16px;">
        <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" style="color:var(--text-muted);font-size:13px;text-decoration:none;">
            Meus Agendamentos
        </a>
    </div>

    @if(isset($servicos) && $servicos->count())
    <div class="card-base" style="margin-bottom:20px;">
        <h3 style="font-size:15px;font-weight:700;color:var(--accent);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg class="icon icon-sm"><use href="#i-scissors-2"/></svg> Serviços
        </h3>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($servicos as $s)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                    @if($s->foto)
                    <img src="{{ $s->foto_url }}" alt="{{ $s->nome }}" style="width:80px;height:80px;border-radius:12px;object-fit:cover;flex-shrink:0;border:1px solid var(--border-strong);">
                    @endif
                    <div style="min-width:0;"><strong style="font-size:14px;">{{ $s->nome }}</strong>@if($s->descricao)<br><small style="font-size:12px;color:var(--text-muted);">{{ $s->descricao }}</small>@endif</div>
                </div>
                <div style="font-size:15px;font-weight:700;color:var(--accent);white-space:nowrap;">R$ {{ number_format($s->preco, 2, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @php
        $hrAbertura = $barbeariaAtual?->horario_abertura ?? \App\Models\Configuracao::get('horario_abertura', '08:00');
        $hrFechamento = $barbeariaAtual?->horario_fechamento ?? \App\Models\Configuracao::get('horario_fechamento', '18:00');
        $hrDias = $barbeariaAtual ? explode(',', $barbeariaAtual->dias_funcionamento ?? '1,2,3,4,5,6') : explode(',', \App\Models\Configuracao::get('dias_funcionamento', '1,2,3,4,5,6'));
        $diasSemana = [0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sab'];
        $diasNomes = [];
        foreach ($hrDias as $d) { if (isset($diasSemana[(int)$d])) $diasNomes[] = $diasSemana[(int)$d]; }
    @endphp
    @if($diasNomes)
    <div class="card-base" style="margin-bottom:20px;">
        <h3 style="font-size:15px;font-weight:700;color:var(--accent);margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg class="icon icon-sm"><use href="#i-clock"/></svg> Horários
        </h3>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;">
            <span style="color:var(--text-muted);font-size:13px;">{{ implode(', ', $diasNomes) }}</span>
            <span style="color:var(--text);font-weight:600;">{{ $hrAbertura }} às {{ $hrFechamento }}</span>
        </div>
    </div>
    @endif

    <div class="card-base" style="margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <h3 style="font-size:15px;font-weight:700;color:var(--accent);display:flex;align-items:center;gap:8px;margin:0;">
                <svg class="icon icon-sm"><use href="#i-star"/></svg> Avaliações
            </h3>
            @if(!empty($avaliacoes) && $avaliacoes->count())
            @php $mediaGlobal = round($avaliacoes->avg('rating'), 1); @endphp
            <div style="display:flex;align-items:center;gap:4px;">
                <span style="font-size:20px;font-weight:800;color:var(--text);">{{ $mediaGlobal }}</span>
                <div style="display:flex;gap:2px;">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="icon icon-xs" style="color:{{ $i <= round($mediaGlobal) ? 'var(--accent)' : 'var(--text-faint)' }};"><use href="#i-star"/></svg>
                    @endfor
                </div>
                <span style="font-size:12px;color:var(--text-muted);margin-left:4px;">({{ $avaliacoes->count() }})</span>
            </div>
            @endif
        </div>
        @if(!empty($avaliacoes) && $avaliacoes->count())
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($avaliacoes as $av)
            <div style="padding:12px;background:var(--card-solid);border:1px solid var(--border);border-radius:var(--r-sm);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <strong style="font-size:13px;color:var(--text);">{{ $av->cliente_nome }}</strong>
                    <div style="color:var(--accent);font-size:13px;">@for($i=1;$i<=5;$i++)<svg class="icon icon-xs" style="margin-right:1px;color:{{ $i <= ($av->rating ?? 0) ? 'var(--accent)' : 'var(--text-faint)' }};"><use href="#i-star"/></svg>@endfor</div>
                </div>
                @if($av->comentario)<p style="font-size:13px;color:var(--text-muted);margin:4px 0 0;">{{ $av->comentario }}</p>@endif
                @if($av->resposta)<div style="margin-top:8px;padding:8px 10px;background:var(--bg);border-radius:var(--r-sm);border-left:3px solid var(--accent);"><small style="font-size:12px;color:var(--text-muted);"><strong style="color:var(--accent);">Resposta:</strong> {{ $av->resposta }}</small></div>@endif
            </div>
            @endforeach
        </div>
        @else
        <p style="font-size:13px;color:var(--text-muted);text-align:center;padding:12px 0;">Nenhuma avaliação ainda. Seja o primeiro a avaliar após o atendimento!</p>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- WIZARD STEPS 1-5 --}}
    {{-- ============================================================ --}}
    @else
    <div class="steps">
        @foreach($stepLabels as $si => $sl)
        @if($si === 0) @continue @endif
        <div class="step-item {{ $currentStep > $si ? 'done' : ($currentStep === $si ? 'active' : '') }}">
            <div class="step-num">
                @if($currentStep > $si)<svg class="icon icon-xs"><use href="#i-check"/></svg>
                @else{{ $si }}@endif
            </div>
            <span>{{ $sl }}</span>
        </div>
        @if($si < 5)<div class="step-divider"></div>@endif
        @endforeach
    </div>

    <div class="page-header">
        <h1 class="page-title">{{ $stepTitles[$step] ?? 'Agendamento' }}</h1>
        @if(isset($stepSubtitles[$step]) && $stepSubtitles[$step])
        <p class="page-subtitle">{{ $stepSubtitles[$step] }}</p>
        @endif
    </div>

    {{-- STEP 1: Phone --}}
    @if($step == 1)
    <div class="card-base" style="max-width:500px;margin:0 auto;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <button class="btn-ghost-c" style="height:36px;padding:0 10px;" wire:click="voltar">
                <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                Voltar
            </button>
            <h5 style="margin:0;font-weight:700;"><svg class="icon" style="margin-right:6px;"><use href="#i-whatsapp"/></svg> Seu WhatsApp</h5>
        </div>
        @if(session('error'))
        <div style="padding:8px 12px;background:var(--danger-bg);color:var(--danger);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:12px;">{{ session('error') }}</div>
        @endif
        <div class="form-group">
            <label class="form-label">Digite seu WhatsApp</label>
            <div class="input-group">
                <span class="addon"><svg class="icon icon-sm"><use href="#i-whatsapp"/></svg></span>
                <input type="tel" class="form-input" wire:model="telefone" placeholder="(87) 99999-8888" required>
            </div>
            @if($step1_pedir_nome)
            <label class="form-label" style="margin-top:16px;">Seu nome</label>
            <div class="input-group">
                <span class="addon"><svg class="icon icon-sm"><use href="#i-user"/></svg></span>
                <input type="text" class="form-input" wire:model="nome" placeholder="Seu nome" required>
            </div>
            @endif
        </div>
        <button class="btn-primary-c" style="width:100%;justify-content:center;" wire:click="avancarStep1">
            {{ $step1_pedir_nome ? 'Cadastrar' : 'Continuar' }}
            <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
        </button>
    </div>
    @endif

    {{-- STEP 2: Barbearia --}}
    @if($step == 2)
    <div style="margin-bottom:16px;">
        <button class="btn-ghost-c" style="height:38px;padding:0 14px;" wire:click="voltar">
            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
            Voltar
        </button>
    </div>
    <div class="grid-2">
        @foreach($barbearias as $b)
        <div class="card-base card-hover {{ $barbearia_id == $b->id ? 'card-selected' : '' }}" wire:click="selectBarbearia({{ $b->id }})" style="padding:24px;{{ $barbearia_id == $b->id ? '' : '' }}">
            <div class="check-circle"><svg class="icon icon-sm"><use href="#i-check"/></svg></div>
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:12px;">
                <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent-soft));display:grid;place-items:center;color:#0d0d12;font-weight:700;font-size:16px;flex-shrink:0;">{{ strtoupper(substr($b->nome, 0, 2)) }}</div>
                <div style="flex:1;">
                    <div style="font-size:16px;font-weight:700;">{{ $b->nome }}</div>
                    @if($b->bairro || $b->cidade)
                    <div style="font-size:13px;color:var(--text-muted);margin-top:4px;">{{ $b->bairro }}{{ $b->bairro && $b->cidade ? ' - ' : '' }}{{ $b->cidade }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="action-bar {{ $barbearia_id ? 'show' : '' }}" id="actionBarStep2">
        <div class="action-container">
            <div class="selected-info">
                <span class="label">Unidade Selecionada</span>
                <span class="value" id="selectedBarbearia">{{ $barbearia_id ? $barbearias->firstWhere('id', $barbearia_id)?->nome : 'Nenhuma selecionada' }}</span>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-ghost-c" wire:click="voltar" style="height:48px;">
                    <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                    Voltar
                </button>
                <button class="btn-primary-c" id="continueStep2" {{ !$barbearia_id ? 'disabled' : '' }} wire:click="selectBarbearia({{ $barbearia_id }})">
                    Continuar <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 3: Barbeiro --}}
    @if($step == 3)
    <div style="margin-bottom:16px;">
        <button class="btn-ghost-c" style="height:38px;padding:0 14px;" wire:click="voltar">
            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
            Voltar
        </button>
    </div>
    <div class="grid-3">
        @foreach($barbeiros as $b)
        <div class="card-base card-hover {{ $barbeiro_id == $b->id ? 'card-selected' : '' }}" wire:click="selectBarbeiro({{ $b->id }})" style="padding:20px;display:flex;flex-direction:column;align-items:center;text-align:center;">
            <div class="check-circle"><svg class="icon icon-sm"><use href="#i-check"/></svg></div>
            <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-soft));display:grid;place-items:center;color:#0d0d12;font-weight:700;font-size:22px;margin-bottom:12px;box-shadow:0 8px 20px -8px rgba(0,0,0,0.4);">
                {{ strtoupper(substr($b->nome, 0, 2)) }}
            </div>
            <div style="font-size:16px;font-weight:700;">{{ $b->nome }}</div>
            @php $avg = $b->avg_rating ?? 0; @endphp
            @if($avg > 0)
            <div style="margin-top:6px;display:flex;align-items:center;gap:4px;">
                @for($i = 1; $i <= 5; $i++)
                <svg class="icon icon-xs" style="color:{{ $i <= round($avg) ? 'var(--accent)' : 'var(--text-faint)' }};"><use href="#i-star"/></svg>
                @endfor
                <span style="font-size:11px;color:var(--text-muted);margin-left:2px;">{{ number_format($avg, 1) }}</span>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    <div class="action-bar {{ $barbeiro_id ? 'show' : '' }}" id="actionBarStep3">
        <div class="action-container">
            <div class="selected-info">
                <span class="label">Profissional Selecionado</span>
                <span class="value">{{ $barbeiro_id ? $barbeiros->firstWhere('id', $barbeiro_id)?->nome : 'Nenhum selecionado' }}</span>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-ghost-c" wire:click="voltar" style="height:48px;">
                    <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                    Voltar
                </button>
                <button class="btn-primary-c" {{ !$barbeiro_id ? 'disabled' : '' }} wire:click="selectBarbeiro({{ $barbeiro_id }})">
                    Continuar <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 4: Serviço --}}
    @if($step == 4)
    <div style="margin-bottom:16px;">
        <button class="btn-ghost-c" style="height:38px;padding:0 14px;" wire:click="voltar">
            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
            Voltar
        </button>
    </div>
    <div class="grid-2 grid-servicos">
        @foreach($servicos as $s)
        <div class="card-base card-hover card-servico {{ $servico_id == $s->id ? 'card-selected' : '' }}" wire:click="selectServico({{ $s->id }})">
            <div class="check-circle"><svg class="icon icon-sm"><use href="#i-check"/></svg></div>
            <div class="servico-thumb">
                @if($s->foto)
                <img src="{{ $s->foto_url }}" alt="{{ $s->nome }}">
                @else
                <div class="servico-thumb-ph"><svg class="icon"><use href="#i-scissors-2"/></svg></div>
                @endif
            </div>
            <div class="servico-body">
                <div class="servico-nome">{{ $s->nome }}</div>
                @if($s->descricao)<div class="servico-desc">{{ $s->descricao }}</div>@endif
                <div class="servico-footer">
                    <div class="servico-duracao">
                        <svg class="icon icon-sm"><use href="#i-clock"/></svg> {{ $s->duracao_minutos }}min
                    </div>
                    <div class="servico-preco">R$ {{ number_format($s->preco, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="action-bar {{ $servico_id ? 'show' : '' }}" id="actionBarStep4">
        <div class="action-container">
            <div class="selected-info">
                <span class="label">Serviço Selecionado</span>
                <span class="value">{{ $servico_id ? $servicos->firstWhere('id', $servico_id)?->nome : 'Nenhum selecionado' }} <span class="pipe">·</span> {{ $servico ? 'R$ '.number_format($servico->preco, 2, ',', '.') : '' }}</span>
            </div>
            <div style="display:flex;gap:10px;">
                <button class="btn-ghost-c" wire:click="voltar" style="height:48px;">
                    <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                    Voltar
                </button>
                <button class="btn-primary-c" {{ !$servico_id ? 'disabled' : '' }} wire:click="selectServico({{ $servico_id }})">
                    Continuar <svg class="icon icon-sm"><use href="#i-arrow-right"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- STEP 5: Data/Hora + Confirm --}}
    @if($step == 5)
    @php
        $selectedBarbearia = \App\Models\Barbearia::find($barbearia_id);
    @endphp
    <div style="margin-bottom:16px;">
        <button class="btn-ghost-c" style="height:38px;padding:0 14px;" wire:click="voltar">
            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
            Voltar
        </button>
    </div>
    <div style="margin-bottom:24px;">
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <svg class="icon icon-sm"><use href="#i-calendar"/></svg> Selecione o Dia
        </div>
        <div style="display:flex;gap:12px;overflow-x:auto;padding-bottom:12px;">
            @if($dias && count($dias))
            @foreach($dias as $d)
            <div class="card-base card-hover {{ $data == $d['data'] ? 'card-selected' : '' }}" wire:click="selectDia('{{ $d['data'] }}')" style="flex-shrink:0;width:80px;padding:16px 12px;display:flex;flex-direction:column;align-items:center;gap:4px;border-radius:var(--r-md);">
                <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:{{ $data == $d['data'] ? 'rgba(13,13,18,0.7)' : 'var(--text-faint)' }};">{{ $d['dia'] }}</span>
                <span style="font-size:22px;font-weight:800;letter-spacing:-0.03em;color:{{ $data == $d['data'] ? '#0d0d12' : 'var(--text)' }};">{{ \Carbon\Carbon::parse($d['data'])->format('d') }}</span>
                <span style="font-size:11px;font-weight:600;text-transform:uppercase;color:{{ $data == $d['data'] ? 'rgba(13,13,18,0.7)' : 'var(--text-muted)' }};">{{ $d['mes'] }}</span>
            </div>
            @endforeach
            @else
            <p style="color:var(--text-muted);font-size:14px;">Nenhum dia disponível para este barbeiro.</p>
            @endif
        </div>
    </div>

    @if($data)
    <div style="margin-bottom:24px;">
        <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <svg class="icon icon-sm"><use href="#i-clock"/></svg> Horários Disponíveis
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
            @if($horarios && count($horarios))
            @foreach($horarios as $h)
            <button class="{{ $hora == $h ? 'card-selected' : '' }}" wire:click="selectHora('{{ $h }}')"
                style="height:48px;border-radius:var(--r-sm);background:var(--card);border:1px solid var(--border-strong);color:var(--text);font-family:inherit;font-size:15px;font-weight:600;cursor:pointer;{{ $hora == $h ? 'background:var(--accent);border-color:var(--accent);color:#0d0d12;box-shadow:0 4px 12px -4px var(--accent-glow);' : '' }}">
                {{ $h }}
            </button>
            @endforeach
            @else
            <p style="color:var(--text-muted);font-size:14px;grid-column:1/-1;">Nenhum horário disponível nesta data.</p>
            @endif
        </div>
    </div>
    @endif

    @if($hora)
    <div class="panel" style="margin-bottom:24px;">
        <div class="panel-header">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-check"/></svg></div>
            <div>
                <h2 class="panel-title">Resumo do Agendamento</h2>
                <div class="panel-subtitle">Confira se está tudo correto</div>
            </div>
        </div>
        <div class="panel-body">
            <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-building"/></svg></div><div class="sum-info"><span class="lbl">Barbearia</span><span class="val">{{ $selectedBarbearia?->nome ?? '-' }}</span></div></div>
            <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-user-tag"/></svg></div><div class="sum-info"><span class="lbl">Profissional</span><span class="val">{{ \App\Models\Barbeiro::find($barbeiro_id)?->nome }}</span></div></div>
            <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-scissors-2"/></svg></div><div class="sum-info"><span class="lbl">Serviço</span><span class="val">{{ $servico?->nome }} - R$ {{ number_format($servico?->preco ?? 0, 2, ',', '.') }}</span></div></div>
            <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-calendar"/></svg></div><div class="sum-info"><span class="lbl">Data</span><span class="val">{{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</span></div></div>
            <div class="summary-item"><div class="sum-ic"><svg class="icon icon-sm"><use href="#i-clock"/></svg></div><div class="sum-info"><span class="lbl">Horário</span><span class="val">{{ $hora }}</span></div></div>
            <div class="summary-total">
                <span class="lbl">Valor Total</span>
                <span class="val"><span class="cur">R$</span> {{ number_format($servico?->preco ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div style="padding:10px 14px;background:var(--danger-bg);color:var(--danger);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <div class="action-bar show">
        <div class="action-container">
            <div style="display:flex;gap:12px;width:100%;">
                <button class="btn-ghost-c" wire:click="voltar">
                    <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
                    Voltar
                </button>
                <button class="btn-primary-c" style="flex:1;justify-content:center;" wire:click="confirmar">
                    <svg class="icon icon-sm"><use href="#i-check"/></svg>
                    Confirmar Agendamento
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($step < 5 || ($step == 5 && !$hora))
    <div style="text-align:center;margin-top:16px;">
        <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" style="color:var(--text-muted);font-size:13px;text-decoration:none;">
            Meus Agendamentos
        </a>
    </div>
    @endif
    @endif
    @endif
<style>
.card-servico {
    background: var(--card-solid);
    padding: 0;
    overflow: hidden;
    border: 1px solid var(--border-strong);
    display: flex;
    flex-direction: column;
}
.card-servico.card-selected {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 1px var(--accent), 0 14px 40px -12px var(--accent-glow) !important;
}
.servico-thumb {
    height: 220px;
    background: var(--bg-elevated);
    border-bottom: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}
.servico-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 250ms ease;
}
.card-servico:hover .servico-thumb img {
    transform: scale(1.04);
}
.servico-thumb-ph {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    color: var(--accent);
    background: linear-gradient(135deg, var(--accent-glow), transparent 70%);
}
.servico-body {
    padding: 18px 20px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.servico-nome {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
}
.servico-desc {
    font-size: 13px;
    color: var(--text-muted);
    line-height: 1.45;
}
.servico-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    margin-top: auto;
    border-top: 1px solid var(--border);
    gap: 10px;
}
.servico-duracao {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-muted);
}
.servico-preco {
    font-size: 19px;
    font-weight: 800;
    color: var(--accent);
    white-space: nowrap;
}
@keyframes pop-in {
    0% { transform: scale(0); opacity: 0; }
    70% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
</div>