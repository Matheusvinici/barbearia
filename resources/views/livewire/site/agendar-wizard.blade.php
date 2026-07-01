<div>
    @if($success)
        <div class="step-card text-center">
            <div style="font-size:4rem;color:#28a745"><i class="bi bi-check-circle-fill"></i></div>
            <h4 class="mt-2">Agendamento Confirmado!</h4>
            <table class="table table-borderless text-start small">
                <tr><th>Barbearia:</th><td>{{ $agendamento->barbearia?->nome ?? '-' }}</td></tr>
                <tr><th>Barbeiro:</th><td>{{ $agendamento->barbeiro->nome }}</td></tr>
                <tr><th>Serviço:</th><td>{{ $agendamento->servicos->first()->nome ?? '-' }}</td></tr>
                <tr><th>Data:</th><td>{{ \Carbon\Carbon::parse($agendamento->data)->format('d/m/Y') }}</td></tr>
                <tr><th>Horário:</th><td>{{ $agendamento->hora_inicio->format('H:i') }}</td></tr>
                <tr><th>Valor:</th><td>R$ {{ number_format($agendamento->total, 2, ',', '.') }}</td></tr>
            </table>
            <div class="alert alert-info py-2 small">Você receberá um lembrete 1h antes no WhatsApp.</div>
            <button class="btn btn-primary w-100" wire:click="novoAgendamento">
                <i class="bi bi-plus-circle"></i> Novo Agendamento
            </button>
            <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="btn btn-outline-secondary w-100 mt-2">
                <i class="bi bi-calendar-check"></i> Meus Agendamentos
            </a>
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
                <button class="btn btn-sm btn-outline-secondary" wire:click="corrigirTelefone"><i class="bi bi-arrow-left"></i></button>
                @endif
                <h5 class="mb-0"><i class="bi bi-whatsapp"></i> Seu WhatsApp</h5>
            </div>
            @if(session('error'))<div class="alert alert-danger py-1 small">{{ session('error') }}</div>@endif
            <div class="mb-3">
                <label class="form-label">Digite seu WhatsApp</label>
                <input type="tel" class="form-control" wire:model="telefone" placeholder="(87) 99999-8888" required>
                @if($step1_pedir_nome)
                <label class="form-label mt-2">Seu nome</label>
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
                <button class="btn btn-sm btn-outline-secondary" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0"><i class="bi bi-shop"></i> Escolha a Barbearia</h5>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($barbearias as $b)
                <button class="btn btn-outline-dark text-start d-flex align-items-center gap-2 p-3 rounded-3
                    {{ $barbearia_id == $b->id ? 'btn-dark text-white' : '' }}"
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
                <button class="btn btn-sm btn-outline-secondary" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Escolha o Barbeiro</h5>
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($barbeiros as $b)
                <button class="btn btn-outline-dark text-start d-flex align-items-center gap-2 p-3 rounded-3
                    {{ $barbeiro_id == $b->id ? 'btn-dark text-white' : '' }}"
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
                <button class="btn btn-sm btn-outline-secondary" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0"><i class="bi bi-scissors"></i> Escolha o Serviço</h5>
            </div>
            <div class="row g-2">
                @foreach($servicos as $s)
                <div class="col-6">
                    <div class="service-card {{ $servico_id == $s->id ? 'selected' : '' }}"
                         wire:click="selectServico({{ $s->id }})">
                        @if($s->foto)
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nome }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:120px">
                                <i class="bi bi-image text-muted fs-1"></i>
                            </div>
                        @endif
                        <div class="mt-1 small">
                            <strong>{{ $s->nome }}</strong><br>
                            <span class="text-muted">R$ {{ number_format($s->preco, 2, ',', '.') }}</span>
                            <span class="text-muted"> · {{ $s->duracao_minutos }}min</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- STEP 5: Data e Hora + Confirmar --}}
        @if($step == 5)
        <div class="step-card">
            <div class="d-flex align-items-center gap-2 mb-3">
                <button class="btn btn-sm btn-outline-secondary" wire:click="voltar"><i class="bi bi-arrow-left"></i></button>
                <h5 class="mb-0"><i class="bi bi-calendar-week"></i> Escolha o Dia</h5>
            </div>

            @if($dias && count($dias))
            <div class="d-flex gap-2 overflow-auto pb-2" style="-webkit-overflow-scrolling:touch">
                @foreach($dias as $d)
                <button class="btn text-center flex-shrink-0 {{ $data == $d['data'] ? 'btn-dark' : 'btn-outline-dark' }}"
                        style="min-width:80px" wire:click="selectDia('{{ $d['data'] }}')">
                    <div class="small">{{ $d['dia'] }}</div>
                    <div class="fw-bold">{{ \Carbon\Carbon::parse($d['data'])->format('d') }}</div>
                    <div class="small">{{ $d['mes'] }}</div>
                </button>
                @endforeach
            </div>
            @else
            <p class="text-muted small">Nenhum dia disponível para este barbeiro.</p>
            @endif

            @if($data)
            <hr>
            <h6 class="mb-2"><i class="bi bi-clock"></i> Horários Disponíveis</h6>
            @if($horarios && count($horarios))
            <div class="d-flex flex-wrap gap-2">
                @foreach($horarios as $h)
                <button class="btn btn-sm {{ $hora == $h ? 'btn-dark' : 'btn-outline-dark' }}"
                        wire:click="selectHora('{{ $h }}')">
                    {{ $h }}
                </button>
                @endforeach
            </div>
            @else
            <p class="text-muted small">Nenhum horário disponível nesta data.</p>
            @endif

            @if($hora)
            <hr>
            <h6 class="mb-2">Resumo do Agendamento</h6>
            <table class="table table-borderless small">
                <tr><th>Cliente:</th><td>{{ $cliente->nome ?? $nome }}</td></tr>
                <tr><th>Barbearia:</th><td>{{ \App\Models\Barbearia::find($barbearia_id)?->nome ?? '-' }}</td></tr>
                <tr><th>Barbeiro:</th><td>{{ \App\Models\Barbeiro::find($barbeiro_id)->nome }}</td></tr>
                <tr><th>Serviço:</th><td>{{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}</td></tr>
                <tr><th>Data:</th><td>{{ \Carbon\Carbon::parse($data)->format('d/m/Y') }}</td></tr>
                <tr><th>Horário:</th><td>{{ $hora }}</td></tr>
            </table>
            @if(session('error'))<div class="alert alert-danger py-1 small">{{ session('error') }}</div>@endif
            <button class="btn btn-primary w-100 mt-2" wire:click="confirmar">
                <i class="bi bi-check-lg"></i> Confirmar Agendamento
            </button>
            @endif
            @endif
        </div>
        @endif
    @endif

    <div class="text-center mt-2">
        <a href="{{ $slug ? route('tenant.site.meus-agendamentos', $slug) : route('site.meus-agendamentos') }}" class="text-muted small">
            <i class="bi bi-calendar-check"></i> Meus Agendamentos
        </a>
    </div>
</div>
