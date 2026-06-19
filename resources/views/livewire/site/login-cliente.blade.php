<div>
    @if($step === 'phone')
    <div class="text-center">
        <div style="font-size:3.5rem;color:var(--accent);line-height:1"><i class="bi bi-scissors"></i></div>
        <h2 class="fw-bold mt-3 mb-1">Barbearia</h2>
        <p class="text-muted mb-4" style="font-size:.95rem">Agende seu horário online</p>

        <button class="btn btn-primary btn-lg w-100 py-3 fw-bold"
                style="font-size:1.2rem;border-radius:14px"
                wire:click="$set('step', 'form')">
            <i class="bi bi-calendar-check"></i> Agendar Horário
        </button>

        <div class="mt-3" style="font-size:.85rem">
            <a href="#" class="text-muted" wire:click.prevent="$set('step', 'form')">
                Já tenho cadastro
            </a>
        </div>
    </div>

    @elseif($step === 'form')
    <div class="step-card">
        <div class="text-center mb-3">
            <div style="font-size:2rem;color:var(--accent)"><i class="bi bi-phone"></i></div>
            <h5 class="fw-bold mt-2 mb-1">Qual seu telefone?</h5>
            <p class="text-muted small">Já tem cadastro? Digite seu telefone.</p>
        </div>
        <input type="tel" class="form-control form-control-lg phone-input mb-3"
               wire:model="telefone" wire:keydown.enter="buscar"
               placeholder="(88) 99999-9999" maxlength="15"
               inputmode="numeric" autofocus
               oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary w-100 py-2" wire:click="buscar">
            Continuar <i class="bi bi-arrow-right"></i>
        </button>
        <button class="btn btn-link btn-sm w-100 mt-2 text-muted" wire:click="$set('step','phone')">
            Voltar
        </button>
    </div>

    @elseif($step === 'register')
    <div class="step-card">
        <div class="text-center mb-3">
            <div style="font-size:2rem;color:var(--accent)"><i class="bi bi-person-plus"></i></div>
            <h5 class="fw-bold mt-2 mb-1">Quase lá!</h5>
            <p class="text-muted small">
                Telefone: <strong>{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone) }}</strong>
            </p>
        </div>
        <input type="text" class="form-control form-control-lg mb-3"
               wire:model="nome" wire:keydown.enter="cadastrar"
               placeholder="Seu nome completo" autofocus>
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary w-100 py-2" wire:click="cadastrar">
            <i class="bi bi-check-lg"></i> Cadastrar e Agendar
        </button>
        <button class="btn btn-link btn-sm w-100 mt-2 text-muted" wire:click="$set('step','form')">
            Voltar
        </button>
    </div>
    @endif
</div>
