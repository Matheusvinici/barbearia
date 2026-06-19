<div>
    <div class="step-card text-center">
        @if($step === 'phone')
            <h5><i class="bi bi-phone"></i> Entrar</h5>
            <p class="text-muted small">Digite seu telefone para agendar ou ver seus horários.</p>
            <input type="tel" class="form-control form-control-lg phone-input mb-3"
                   wire:model="telefone" wire:keydown.enter="buscar"
                   placeholder="(88) 99999-9999" maxlength="15"
                   inputmode="numeric"
                   oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
            @error('telefone') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
            @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
            <button class="btn btn-primary w-100" wire:click="buscar">
                <i class="bi bi-arrow-right"></i> Continuar
            </button>

        @elseif($step === 'register')
            <h5><i class="bi bi-person-plus"></i> Novo Cadastro</h5>
            <p class="text-muted small">Telefone: <strong>{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone) }}</strong></p>
            <input type="text" class="form-control form-control-lg mb-3"
                   wire:model="nome" wire:keydown.enter="cadastrar"
                   placeholder="Seu nome completo">
            @if($error) <div class="alert alert-danger py-2 small">{{ $error }}</div> @endif
            <button class="btn btn-primary w-100" wire:click="cadastrar">
                <i class="bi bi-check-lg"></i> Cadastrar e Entrar
            </button>
            <button class="btn btn-link btn-sm mt-2 text-muted" wire:click="$set('step','phone')">
                Voltar
            </button>
        @endif
    </div>
</div>
