<div>
    @forelse($avaliacoes as $avaliacao)
    <div style="background:var(--card);border:1px solid var(--border);border-radius:var(--r-md);padding:16px;margin-bottom:12px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
            <div>
                <strong style="color:var(--text);">{{ $avaliacao->cliente_nome }}</strong>
                @if(!$barbearia)
                <span style="font-size:12px;color:var(--text-muted);"> — {{ $avaliacao->barbearia?->nome ?? 'N/A' }}</span>
                @endif
                <div style="color:var(--accent);font-size:16px;margin:4px 0;letter-spacing:2px;">
                    @for($i = 1; $i <= 5; $i++)
                        {!! $i <= $avaliacao->rating ? '★' : '☆' !!}
                    @endfor
                </div>
                @if($avaliacao->comentario)
                <p style="font-size:13px;color:var(--text-muted);margin:6px 0 0;">{{ $avaliacao->comentario }}</p>
                @endif
            </div>
            <span style="font-size:11px;color:var(--text-faint);white-space:nowrap;">{{ $avaliacao->created_at->diffForHumans() }}</span>
        </div>

        @if($avaliacao->resposta)
            @if($editandoId === $avaliacao->id)
            <div style="margin-top:12px;" wire:key="edit-{{ $avaliacao->id }}">
                <textarea wire:model="editandoTexto" rows="2" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border-strong);background:var(--bg);color:var(--text);font-size:13px;font-family:inherit;resize:vertical;"></textarea>
                <div style="display:flex;gap:8px;margin-top:6px;">
                    <button type="button" wire:click="salvarEdicao({{ $avaliacao->id }})" style="padding:6px 16px;border-radius:8px;background:var(--accent);color:#0d0d12;border:none;font-weight:600;font-size:12px;cursor:pointer;">Salvar</button>
                    <button type="button" wire:click="cancelarEdicao" style="padding:6px 16px;border-radius:8px;background:var(--card-solid);color:var(--text-muted);border:1px solid var(--border-strong);font-weight:600;font-size:12px;cursor:pointer;">Cancelar</button>
                </div>
            </div>
            @else
            <div style="margin-top:12px;padding:12px;background:var(--bg);border-radius:var(--r-sm);border-left:3px solid var(--accent);">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <div style="font-size:12px;font-weight:600;color:var(--accent);">Sua resposta:</div>
                    <div style="display:flex;gap:4px;">
                        <button type="button" wire:click="editarResposta({{ $avaliacao->id }})" style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;">✏️</button>
                        <button type="button" wire:click="excluirResposta({{ $avaliacao->id }})" wire:confirm="Excluir resposta?" style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;">🗑️</button>
                    </div>
                </div>
                <p style="font-size:13px;color:var(--text);margin:0;">{{ $avaliacao->resposta }}</p>
            </div>
            @endif
        @else
        <div style="margin-top:12px;" wire:key="resp-{{ $avaliacao->id }}">
            <textarea wire:model="resposta" rows="2" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border-strong);background:var(--bg);color:var(--text);font-size:13px;font-family:inherit;resize:vertical;" placeholder="Digite sua resposta..."></textarea>
            <button type="button" wire:click="responder({{ $avaliacao->id }})" style="margin-top:6px;padding:6px 16px;border-radius:8px;background:var(--accent);color:#0d0d12;border:none;font-weight:600;font-size:12px;cursor:pointer;">
                Responder
            </button>
        </div>
        @endif
    </div>
    @empty
    <p style="text-align:center;color:var(--text-muted);font-size:14px;padding:20px;">Nenhuma avaliação recebida ainda.</p>
    @endforelse
</div>
