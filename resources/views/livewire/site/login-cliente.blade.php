<div>
    @if($step === 'phone')
    <div class="step-card text-center">
        <div style="font-size:3rem;color:var(--accent)"><i class="bi bi-scissors"></i></div>
        <h4 class="fw-bold">Agende seu Horário</h4>
        <p class="text-muted small">Escolha o barbeiro, serviço e o melhor horário para você.</p>
        <hr>
        <h6><i class="bi bi-phone"></i> Qual seu telefone?</h6>
        <input type="tel" class="form-control form-control-lg phone-input mb-3"
               wire:model="telefone" wire:keydown.enter="buscar"
               placeholder="(88) 99999-9999" maxlength="15"
               inputmode="numeric"
               oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary btn-lg w-100" wire:click="buscar">
            <i class="bi bi-calendar-check"></i> Agendar Agora
        </button>
        <div class="mt-3 text-muted small">
            <i class="bi bi-info-circle"></i> Já tem cadastro? É só digitar o telefone.<br>
            Novo? Precisamos do seu nome na sequência.
        </div>
    </div>

    @elseif($step === 'register')
    <div class="step-card text-center">
        <div style="font-size:3rem;color:var(--accent)"><i class="bi bi-person-plus"></i></div>
        <h5 class="fw-bold">Quase lá!</h5>
        <p class="text-muted small">Telefone: <strong>{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone) }}</strong></p>
        <input type="text" class="form-control form-control-lg mb-3"
               wire:model="nome" wire:keydown.enter="cadastrar"
               placeholder="Seu nome completo">
        @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
        <button class="btn btn-primary btn-lg w-100" wire:click="cadastrar">
            <i class="bi bi-check-lg"></i> Cadastrar e Agendar
        </button>
        <button class="btn btn-link btn-sm mt-2 text-muted" wire:click="$set('step','phone')">
            <i class="bi bi-arrow-left"></i> Voltar
        </button>
    </div>
    @endif
</div>
