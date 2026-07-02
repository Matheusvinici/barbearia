<div>
    @if($success)
        <div class="step-card text-center">
            <div style="font-size:4rem;color:var(--success)"><i class="bi bi-check-circle-fill"></i></div>
            <h4 class="mt-2" style="color:var(--text);">Agendamento Confirmado!</h4>
            <table class="table table-borderless text-start small" style="color:var(--text-muted);">
                <tr><th>Barbearia:</th><td>{{ $agendamento->barbearia?->nome ?? '-' }}</td></tr>
                <tr><th>Barbeiro:</th><td>{{ $agendamento->barbeiro->nome }}</td></tr>
                <tr><th>Serviço:</th><td>{{ $agendamento->servicos->first()->nome ?? '-' }}</td></tr>
                <tr><th>Data:</th><td>{{ \Carbon\Carbon::parse($agendamento->data)->format('d/m/Y') }}</td></tr>
                <tr><th>Horário:</th><td>{{ $agendamento->hora_inicio->format('H:i') }}</td></tr>
                <tr><th>Valor:</th><td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td></tr>
            </table>
            <div class="alert py-2 small" style="background:var(--success-bg);color:var(--success);border:none;border-radius:10px;">Você receberá um lembrete 1h antes no WhatsApp.</div>
            <button class="btn btn-primary w-100" wire:click="novoAgendamento">
                <i class="bi bi-plus-circle"></i> Novo Agendamento
            </button>
            <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="btn w-100 mt-2" style="background:var(--card-solid);color:var(--text-muted);border:1px solid var(--border-strong);">
                <i class="bi bi-calendar-check"></i> Meus Agendamentos
            </a>
        </div>
    @elseif($step == 0)
        {{-- WELCOME SCREEN --}}
        <div class="welcome-screen">
            @if($barbeariaAtual)
                <div class="welcome-header">
                    @if($barbeariaAtual->logo)
                    <img src="{{ $barbeariaAtual->logo_url }}" alt="{{ $barbeariaAtual->nome }}" class="welcome-logo">
                    @else
                    <div class="welcome-logo-placeholder">
                        <i class="bi bi-scissors"></i>
                    </div>
                    @endif
                    <h1 class="welcome-title">{{ $barbeariaAtual->nome }}</h1>
                    @php
                        $enderecoCompleto = $barbeariaAtual->bairro 
                            ? $barbeariaAtual->bairro . ($barbeariaAtual->cidade ? ' - ' . $barbeariaAtual->cidade : '')
                            : $barbeariaAtual->cidade;
                    @endphp
                    @if($enderecoCompleto)
                    <p class="welcome-address">
                        <i class="bi bi-geo-alt"></i> {{ $enderecoCompleto }}
                    </p>
                    @endif
                </div>
            @else
                <div class="welcome-header">
                    <div class="welcome-logo-placeholder">
                        <i class="bi bi-scissors"></i>
                    </div>
                    <h1 class="welcome-title">{{ \App\Models\Configuracao::get('nome_barbearia', 'Santa Barba') }}</h1>
                    <p class="welcome-address">Agende seu horário com praticidade</p>
                </div>
            @endif

            {{-- CTA --}}
            <button class="btn btn-primary btn-agendar w-100" wire:click="iniciarAgendamento">
                <i class="bi bi-calendar-check"></i> Agendar Horário
            </button>
            <div class="welcome-footer-links" style="margin-top:8px;margin-bottom:16px;">
                <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="text-muted small">
                    <i class="bi bi-calendar-check"></i> Meus Agendamentos
                </a>
            </div>

            {{-- Serviços --}}
            <div class="welcome-section">
                <h3 class="welcome-section-title"><i class="bi bi-scissors"></i> Serviços</h3>
                <div class="services-list">
                    @foreach($servicos ?? [] as $s)
                    <div class="service-item">
                        <div class="service-info">
                            <strong>{{ $s->nome }}</strong>
                            @if($s->descricao)
                            <small>{{ $s->descricao }}</small>
                            @endif
                        </div>
                        <div class="service-price">
                            R$ {{ number_format($s->preco, 2, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Horários --}}
            @php
                if ($barbeariaAtual) {
                    $hrAbertura = $barbeariaAtual->horario_abertura ?? '08:00';
                    $hrFechamento = $barbeariaAtual->horario_fechamento ?? '18:00';
                    $hrDias = explode(',', $barbeariaAtual->dias_funcionamento ?? '1,2,3,4,5,6');
                } else {
                    $hrAbertura = \App\Models\Configuracao::get('horario_abertura', '08:00');
                    $hrFechamento = \App\Models\Configuracao::get('horario_fechamento', '18:00');
                    $hrDias = explode(',', \App\Models\Configuracao::get('dias_funcionamento', '1,2,3,4,5,6'));
                }
                $diasSemana = [0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sab'];
                $diasNomes = [];
                foreach ($hrDias as $d) {
                    if (isset($diasSemana[(int)$d])) $diasNomes[] = $diasSemana[(int)$d];
                }
            @endphp
            <div class="welcome-section">
                <h3 class="welcome-section-title"><i class="bi bi-clock"></i> Horários</h3>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;">
                    <span style="color:var(--text-muted);font-size:13px;">{{ implode(', ', $diasNomes) }}</span>
                    <span style="color:var(--text);font-weight:600;">{{ $hrAbertura }} às {{ $hrFechamento }}</span>
                </div>
            </div>

            {{-- Avaliações --}}
            @if(!empty($avaliacoes) && $avaliacoes->count())
            <div class="welcome-section">
                <h3 class="welcome-section-title"><i class="bi bi-star-fill"></i> Avaliações</h3>
                <div class="reviews-list">
                    @foreach($avaliacoes as $av)
                    <div class="review-card">
                        <div class="review-header">
                            <strong>{{ $av->cliente_nome }}</strong>
                            <div class="review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $av->rating)
                                    <i class="bi bi-star-fill"></i>
                                    @else
                                    <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($av->comentario)
                        <p class="review-comment">{{ $av->comentario }}</p>
                        @endif
                        @if($av->resposta)
                        <div class="review-reply">
                            <small><strong>Resposta:</strong> {{ $av->resposta }}</small>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    @else
        <div class="step-indicator">
            @foreach(range(1,5) as $s)
            <div class="step-dot {{ $step >= $s ? ($step > $s ? 'done' : 'active') : '' }}"></div>
            @endforeach
        </div>

        {{-- STEP 1: Telefone (e nome se novo) --}}
        @if($step == 1)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                @if($step1_pedir_nome)
                <button class="btn btn-sm" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border-strong);" wire:click="corrigirTelefone"><i class="bi bi-arrow-left"></i></button>
                @endif
                <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-whatsapp"></i> Seu WhatsApp</h5>
            </div>
            @if(session('error'))<div class="alert alert-danger py-1 small" style="background:var(--danger-bg);color:var(--danger);border:none;border-radius:10px;">{{ session('error') }}</div>@endif
            <div class="mb-3">
                <label class="form-label" style="color:var(--text-muted);">Digite seu WhatsApp</label>
                <input type="tel" class="form-control" wire:model="telefone" placeholder="(87) 99999-8888" required>
                @if($step1_pedir_nome)
                <label class="form-label mt-2" style="color:var(--text-muted);">Seu nome</label>
                <input type="text" class="form-control" wire:model="nome" placeholder="Seu nome" required>
                @endif
            </div>
            <button class="btn btn-primary w-100" wire:click="avancarStep1">
                <i class="bi bi-arrow-right"></i> {{ $step1_pedir_nome ? 'Cadastrar' : 'Continuar' }}
            </button>
        </div>
        @endif

        {{-- STEP 2: Barbearia --}}
        @if($step == 2)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <button class="btn btn-sm" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border-strong);" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-shop"></i> Escolha a Barbearia</h5>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($barbearias as $b)
                <button class="btn text-start d-flex align-items-center gap-2 p-3 rounded-3
                    {{ $barbearia_id == $b->id ? 'btn-dark-selected' : 'btn-outline-custom' }}"
                    wire:click="selectBarbearia({{ $b->id }})">
                    <i class="bi bi-building fs-4"></i>
                    <div>
                        <strong>{{ $b->nome }}</strong>
                        @if($b->bairro || $b->cidade)
                        <br><small>{{ $b->bairro }}{{ $b->bairro && $b->cidade ? ' - ' : '' }}{{ $b->cidade }}</small>
                        @endif
                    </div>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- STEP 3: Barbeiro --}}
        @if($step == 3)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <button class="btn btn-sm" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border-strong);" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-person-badge"></i> Escolha o Barbeiro</h5>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($barbeiros as $b)
                <button class="btn text-start d-flex align-items-center gap-2 p-3 rounded-3
                    {{ $barbeiro_id == $b->id ? 'btn-dark-selected' : 'btn-outline-custom' }}"
                    wire:click="selectBarbeiro({{ $b->id }})">
                    <i class="bi bi-person-circle fs-4"></i>
                    <div>
                        <strong>{{ $b->nome }}</strong>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- STEP 4: Serviço --}}
        @if($step == 4)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <button class="btn btn-sm" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border-strong);" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-scissors"></i> Escolha o Serviço</h5>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($servicos as $s)
                <button class="btn text-start d-flex align-items-center gap-2 p-3 rounded-3
                    {{ $servico_id == $s->id ? 'btn-dark-selected' : 'btn-outline-custom' }}"
                    wire:click="selectServico({{ $s->id }})">
                    <i class="bi bi-cut fs-4"></i>
                    <div style="flex:1;">
                        <strong>{{ $s->nome }}</strong>
                        <br><small style="color:var(--text-muted);">R$ {{ number_format($s->preco, 2, ',', '.') }} · {{ $s->duracao_minutos }}min</small>
                    </div>
                    <span style="color:var(--accent);font-weight:600;">R$ {{ number_format($s->preco, 2, ',', '.') }}</span>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- STEP 5: Data e Hora + Confirmar --}}
        @if($step == 5)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <button class="btn btn-sm" style="background:var(--card-solid);color:var(--text);border:1px solid var(--border-strong);" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-calendar-week"></i> Escolha o Dia</h5>
            </div>

            @if($dias && count($dias))
            <div class="d-flex gap-2 overflow-auto pb-2" style="-webkit-overflow-scrolling:touch">
                @foreach($dias as $d)
                <button class="btn text-center flex-shrink-0 {{ $data == $d['data'] ? 'btn-dark-selected' : 'btn-outline-custom' }}"
                        style="min-width:80px" wire:click="selectDia('{{ $d['data'] }}')">
                    <div class="small">{{ $d['dia'] }}</div>
                    <div class="fw-bold">{{ \Carbon\Carbon::parse($d['data'])->format('d') }}</div>
                    <div class="small">{{ $d['mes'] }}</div>
                </button>
                @endforeach
            </div>
            @else
            <p class="text-muted small" style="color:var(--text-muted);">Nenhum dia disponível para este barbeiro.</p>
            @endif

            @if($data)
            <hr style="border-color:var(--border);">
            <h6 class="mb-2" style="color:var(--text);"><i class="bi bi-clock"></i> Horários Disponíveis</h6>
            @if($horarios && count($horarios))
            <div class="d-flex flex-wrap gap-2">
                @foreach($horarios as $h)
                <button class="btn btn-sm {{ $hora == $h ? 'btn-dark-selected' : 'btn-outline-custom' }}"
                        wire:click="selectHora('{{ $h }}')">
                    {{ $h }}
                </button>
                @endforeach
            </div>
            @else
            <p class="text-muted small" style="color:var(--text-muted);">Nenhum horário disponível nesta data.</p>
            @endif

            @if($hora)
            <hr style="border-color:var(--border);">
            <h6 class="mb-2" style="color:var(--text);">Resumo do Agendamento</h6>
            <table class="table table-borderless small" style="color:var(--text-muted);">
                <tr><th>Cliente:</th><td>{{ $cliente->nome ?? $nome }}</td></tr>
                <tr><th>Barbearia:</th><td>{{ \App\Models\Barbearia::find($barbearia_id)?->nome ?? '-' }}</td></tr>
                <tr><th>Barbeiro:</th><td>{{ \App\Models\Barbeiro::find($barbeiro_id)->nome }}</td></tr>
                <tr><th>Serviço:</th><td>{{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}</td></tr>
                <tr><th>Data:</th><td>{{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</td></tr>
                <tr><th>Horário:</th><td>{{ $hora }}</td></tr>
            </table>
            @if(session('error'))<div class="alert alert-danger py-1 small" style="background:var(--danger-bg);color:var(--danger);border:none;border-radius:10px;">{{ session('error') }}</div>@endif
            <button class="btn btn-primary w-100 mt-2" wire:click="confirmar">
                <i class="bi bi-check-lg"></i> Confirmar Agendamento
            </button>
            @endif
            @endif
        </div>
        @endif

        <div class="text-center mt-2">
            <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="small" style="color:var(--text-muted);">
                <i class="bi bi-calendar-check"></i> Meus Agendamentos
            </a>
        </div>
    @endif
</div>
