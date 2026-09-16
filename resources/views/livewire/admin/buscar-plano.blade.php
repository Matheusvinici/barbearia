<div>
    <input type="hidden" name="plano_id" value="{{ $plano_id }}">

    @if($plano_id)
        <div class="input-group">
            <input type="text" class="form-control" value="{{ $search }}" disabled>
            <button type="button" class="btn btn-outline-secondary" wire:click="$set('plano_id', null); $set('search', '')">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @else
        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
               placeholder="Digite nome do plano..." autocomplete="off">

        @if(strlen($search) >= 2)
            <div class="list-group mt-1" style="max-height:200px;overflow-y:auto">
                @forelse($resultados as $p)
                    <button type="button" class="list-group-item list-group-item-action py-2"
                            wire:click="select({{ $p->id }})">
                        <strong>{{ $p->nome }}</strong>
                        <small class="text-muted ms-2">R$ {{ number_format($p->valor, 2, ',', '.') }}</small>
                        @if($p->descricao)
                            <br><small class="text-muted" style="font-size:11px">{{ \Illuminate\Support\Str::limit($p->descricao, 60) }}</small>
                        @endif
                    </button>
                @empty
                    <div class="list-group-item text-muted small py-2">
                        Nenhum plano encontrado.
                    </div>
                @endforelse
            </div>
        @endif
    @endif
</div>
