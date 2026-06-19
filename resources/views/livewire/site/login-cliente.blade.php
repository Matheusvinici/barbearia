<div>
    @if($step === 'phone')
    <div class="text-center" style="padding-top:2rem">
        <div style="font-size:4rem;color:var(--accent);line-height:1"><i class="bi bi-scissors"></i></div>
        <h1 class="fw-bold mt-3" style="font-size:2rem">Barbearia</h1>
        <p class="text-muted mb-4">Agende seu horário online</p>

        <button class="btn btn-primary btn-lg w-100 py-3 mb-3" style="font-size:1.2rem;border-radius:14px"
                wire:click="$set('step', 'form')">
            <i class="bi bi-calendar-check"></i> Agendar Horário
        </button>

        <div class="text-muted">
            <small>ou <a href="#" class="text-muted" wire:click.prevent="$set('step', 'form')">faça login</a> para ver seus agendamentos</small>
        </div>
    </div>

    @elseif($step === 'form')
    <div class="step-card text-center mt-3">
        <button class="btn btn-sm btn-outline-secondary position-absolute" style="top:1rem;left:1rem"
                wire:click="$set('step','phone')"><i class="bi bi-arrow-left"></i></button>
        <h5 class="fw-bold mb-1"><i class="bi bi-phone"></i> Qual seu telefone?</h5>
        <p class="text-muted small mb-3">Já tem cadastro? É só digitar. Novo? Vamos te cadastrar.</p>
        <input type="tel" class="form-control form-control-lg phone-input mb-3"
               wire:model="telefone" wire:keydown.enter="buscar"
               placeholder="(88) 99999-9999" maxlength="15"
               inputmode="numeric" autofocus
               oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary w-100 py-2" wire:click="buscar">
            <i class="bi bi-arrow-right"></i> Continuar
        </button>
    </div>

    @elseif($step === 'register')
    <div class="step-card text-center mt-3">
        <h5 class="fw-bold"><i class="bi bi-person-plus"></i> Quase lá!</h5>
        <p class="text-muted small">Telefone: <strong>{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone) }}</strong></p>
        <input type="text" class="form-control form-control-lg mb-3"
               wire:model="nome" wire:keydown.enter="cadastrar"
               placeholder="Seu nome completo" autofocus>
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary w-100 py-2" wire:click="cadastrar">
            <i class="bi bi-check-lg"></i> Cadastrar e Agendar
        </button>
        <button class="btn btn-link btn-sm mt-2 text-muted" wire:click="$set('step','form')">
            Voltar
        </button>
    </div>
    @endif
</div>
