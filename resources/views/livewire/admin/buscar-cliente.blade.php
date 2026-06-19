<div>
    <input type="hidden" name="cliente_id" value="{{ $cliente_id }}">

    @if($cliente_id)
        <div class="input-group">
            <input type="text" class="form-control" value="{{ $search }}" disabled>
            <button type="button" class="btn btn-outline-secondary" wire:click="$set('cliente_id', null); $set('search', '')">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @elseif($creating)
        <div class="border rounded p-3 bg-light">
            <h6 class="mb-2"><i class="fas fa-plus-circle"></i> Novo Cliente</h6>
            <div class="mb-2">
                <input type="text" class="form-control" wire:model="nome" placeholder="Nome completo" autofocus>
                @error('nome') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="mb-2">
                <input type="tel" class="form-control" wire:model="telefone" placeholder="Telefone com DDD"
                       oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
                @error('telefone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-primary" wire:click="create">
                    <i class="fas fa-check"></i> Criar
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="cancelCreate">
                    Cancelar
                </button>
            </div>
        </div>
    @else
        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
               placeholder="Digite nome ou telefone do cliente..." autocomplete="off">

        @if(strlen($search) >= 2 && !$cliente_id)
            <div class="list-group mt-1" style="max-height:200px;overflow-y:auto">
                @forelse($resultados as $c)
                    <button type="button" class="list-group-item list-group-item-action py-2"
                            wire:click="select({{ $c->id }})">
                        <strong>{{ $c->nome }}</strong>
                        <small class="text-muted ms-2">{{ $c->telefone }}</small>
                    </button>
                @empty
                    <div class="list-group-item text-muted small py-2">
                        Nenhum cliente encontrado.
                    </div>
                    <div class="border rounded p-3 bg-light mt-1">
                        <h6 class="mb-2"><i class="fas fa-plus-circle"></i> Cadastrar Novo Cliente</h6>
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" wire:model="nome" placeholder="Nome completo" autofocus>
                            @error('nome') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-2">
                            <input type="tel" class="form-control form-control-sm" wire:model="telefone" placeholder="Telefone com DDD"
                                   oninput="this.value=this.value.replace(/\D/g,'').replace(/^(\d{2})(\d{5})(\d{4}).*/,'($1) $2-$3')">
                            @error('telefone') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <button type="button" class="btn btn-sm btn-primary btn-block w-100" wire:click="create">
                            <i class="fas fa-check"></i> Cadastrar e Selecionar
                        </button>
                    </div>
                @endforelse
            </div>
        @endif
    @endif
</div>
