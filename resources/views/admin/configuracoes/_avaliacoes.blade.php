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
    <div style="margin-top:12px;padding:12px;background:var(--bg);border-radius:var(--r-sm);border-left:3px solid var(--accent);">
        <div style="font-size:12px;font-weight:600;color:var(--accent);margin-bottom:4px;">Sua resposta:</div>
        <p style="font-size:13px;color:var(--text);margin:0;">{{ $avaliacao->resposta }}</p>
    </div>
    @else
    <div style="margin-top:12px;">
        <textarea class="responder-textarea" rows="2" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border-strong);background:var(--bg);color:var(--text);font-size:13px;font-family:inherit;resize:vertical;" placeholder="Digite sua resposta..." data-action="{{ $barbearia ? route('tenant.admin.avaliacoes.responder', [$barbearia->slug, $avaliacao->id]) : route('admin.avaliacoes.responder', $avaliacao->id) }}"></textarea>
        <button type="button" class="btn-responder" style="margin-top:6px;padding:6px 16px;border-radius:8px;background:var(--accent);color:#0d0d12;border:none;font-weight:600;font-size:12px;cursor:pointer;">Responder</button>
    </div>
    @endif
</div>
@empty
<p style="text-align:center;color:var(--text-muted);font-size:14px;padding:20px;">Nenhuma avaliação recebida ainda.</p>
@endforelse
