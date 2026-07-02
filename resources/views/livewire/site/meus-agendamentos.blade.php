<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0" style="color:var(--text);"><i class="bi bi-calendar-check"></i> Meus Agendamentos</h5>
        <a href="{{ $slug ? route('tenant.site.agendar', $slug) : route('site.agendar') }}" class="btn btn-primary btn-sm" wire:navigate>
            <i class="bi bi-plus-circle"></i> Novo
        </a>
    </div>

    @if(session('success'))
    <div style="padding:10px 14px;background:var(--success-bg);color:var(--success);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:12px;">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="padding:10px 14px;background:var(--danger-bg);color:var(--danger);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:12px;">
        {{ session('error') }}
    </div>
    @endif

    <div class="step-card d-flex align-items-center gap-2 mb-3 p-3">
        <i class="bi bi-person-circle fs-3" style="color:var(--text-muted);"></i>
        <div>
            <strong style="color:var(--text);">{{ $cliente->nome }}</strong><br>
            <small style="color:var(--text-muted);">{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $cliente->telefone) }}</small>
        </div>
        <a href="{{ $slug ? route('tenant.site.login', $slug) : route('site.login') }}" class="ms-auto btn btn-sm" style="background:var(--card-solid);color:var(--text-muted);border:1px solid var(--border-strong);" wire:navigate>
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>

    @forelse($agendamentos as $ag)
    <div class="step-card">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong style="color:var(--text);">{{ $ag->barbeiro->nome }}</strong>
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;font-weight:600;background:{{ $ag->status == 'realizado' ? 'var(--success-bg)' : ($ag->status == 'cancelado' ? 'var(--danger-bg)' : 'var(--info-bg)') }};color:{{ $ag->status == 'realizado' ? 'var(--success)' : ($ag->status == 'cancelado' ? 'var(--danger)' : 'var(--info)') }};">
                    @switch($ag->status)
                        @case('pendente') Pendente @break
                        @case('confirmado') Confirmado @break
                        @case('realizado') Realizado @break
                        @case('cancelado') Cancelado @break
                        @case('ausente') Ausente @break
                        @default {{ $ag->status }}
                    @endswitch
                </span>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">
                    <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}
                    <i class="bi bi-clock ms-2"></i> {{ substr($ag->hora_inicio, 0, 5) }}
                </div>
                <div style="font-size:12px;margin-top:4px;">
                    @foreach($ag->servicos as $s)
                    <span style="background:var(--card-solid);color:var(--text-muted);padding:2px 8px;border-radius:4px;margin-right:4px;font-size:11px;">{{ $s->nome }}</span>
                    @endforeach
                </div>
            </div>
            <div class="text-end">
                <strong style="color:var(--accent);">R$ {{ number_format($ag->total, 2, ',', '.') }}</strong>
            </div>
        </div>

        @if($ag->status == 'realizado' && !in_array($ag->id, $avaliados))
        <hr style="border-color:var(--border);margin:10px 0;">
        <button class="btn btn-sm" style="background:var(--accent-glow);color:var(--accent);border:1px solid var(--accent);border-radius:8px;font-weight:600;width:100%;" wire:click="abrirAvaliacao({{ $ag->id }})">
            <i class="bi bi-star-fill"></i> Avaliar
        </button>
        @elseif($ag->status == 'realizado' && in_array($ag->id, $avaliados))
        <hr style="border-color:var(--border);margin:10px 0;">
        <div style="font-size:12px;color:var(--text-muted);text-align:center;">
            <i class="bi bi-check-circle" style="color:var(--success);"></i> Você já avaliou
        </div>
        @endif
    </div>
    @empty
    <div class="step-card text-center py-4" style="color:var(--text-muted);">
        <i class="bi bi-calendar-x fs-1"></i>
        <p class="mt-2">Nenhum agendamento encontrado.</p>
        <a href="{{ $slug ? route('tenant.site.agendar', $slug) : route('site.agendar') }}" class="btn btn-primary" wire:navigate>
            <i class="bi bi-plus-circle"></i> Agendar Agora
        </a>
    </div>
    @endforelse

    {{-- Modal de Avaliação --}}
    @if($avaliacao_agendamento_id)
    @php $ag = $agendamentos->firstWhere('id', $avaliacao_agendamento_id); @endphp
    @if($ag)
    <div style="position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;padding:20px;">
        <div style="background:var(--card-solid);border:1px solid var(--border-strong);border-radius:var(--r-lg);padding:24px;max-width:380px;width:100%;">
            <h5 style="color:var(--text);margin-bottom:16px;text-align:center;">
                <i class="bi bi-star-fill" style="color:var(--accent);"></i> Avaliar Serviço
            </h5>
            <p style="font-size:13px;color:var(--text-muted);text-align:center;margin-bottom:16px;">
                {{ $ag->barbeiro->nome }} · {{ $ag->servicos->first()?->nome ?? 'Serviço' }}
            </p>

            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:28px;letter-spacing:6px;cursor:pointer;">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $avaliacao_rating ? 'bi-star-fill' : 'bi-star' }}" style="color:{{ $i <= $avaliacao_rating ? 'var(--accent)' : 'var(--text-faint)' }};" wire:click="setRating({{ $i }})"></i>
                    @endfor
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Clique nas estrelas para avaliar</div>
            </div>

            <textarea wire:model.blur="avaliacao_comentario" rows="3" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border-strong);background:var(--bg);color:var(--text);font-size:13px;font-family:inherit;resize:vertical;" placeholder="Deixe seu comentário (opcional)..."></textarea>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button class="btn btn-sm" style="flex:1;background:var(--card-solid);color:var(--text-muted);border:1px solid var(--border-strong);border-radius:8px;" wire:click="fecharAvaliacao">Cancelar</button>
                <button class="btn btn-sm" style="flex:1;background:var(--accent);color:#0d0d12;border:none;border-radius:8px;font-weight:700;" wire:click="salvarAvaliacao">Enviar</button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
