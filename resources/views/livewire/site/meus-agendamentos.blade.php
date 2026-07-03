<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
<div wire:poll.15s>
            <h1 style="font-size:28px;font-weight:800;letter-spacing:-0.03em;margin:0;">Meus Agendamentos</h1>
            <p style="color:var(--text-muted);font-size:14px;margin-top:4px;">{{ $cliente->nome }}</p>
        </div>
        <a href="{{ $slug ? route('tenant.site.agendar', $slug) : route('site.agendar') }}" class="btn-primary-c" style="text-decoration:none;font-size:14px;height:42px;padding:0 20px;">
            <svg class="icon icon-sm"><use href="#i-check"/></svg>
            Novo
        </a>
    </div>

    @if(session('success'))
    <div style="padding:10px 14px;background:var(--success-bg);color:var(--success);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:12px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="padding:10px 14px;background:var(--danger-bg);color:var(--danger);border-radius:10px;font-size:13px;font-weight:500;margin-bottom:12px;">{{ session('error') }}</div>
    @endif

    <div style="display:flex;align-items:center;gap:12px;background:var(--card);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--r-md);padding:16px;margin-bottom:20px;">
        <div style="width:44px;height:44px;border-radius:11px;background:var(--accent-glow);color:var(--accent);display:grid;place-items:center;flex-shrink:0;">
            <svg class="icon"><use href="#i-user"/></svg>
        </div>
        <div style="flex:1;">
            <strong style="color:var(--text);">{{ $cliente->nome }}</strong><br>
            <small style="color:var(--text-muted);">{{ preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $cliente->telefone) }}</small>
        </div>
        <a href="{{ $slug ? route('tenant.site.login', $slug) : route('site.login') }}" style="width:38px;height:38px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text-muted);display:grid;place-items:center;text-decoration:none;">
            <svg class="icon icon-sm"><use href="#i-moon"/></svg>
        </a>
    </div>

    @forelse($agendamentos as $ag)
    @php
        $agData = \Carbon\Carbon::parse($ag->data->format('Y-m-d') . ' ' . $ag->hora_inicio->format('H:i'));
        $agTimestamp = $agData->timestamp;
        $agPassed = $agData->isPast();
        $agAvaliado = in_array($ag->id, $avaliados);
    @endphp
    <div class="card-base" style="margin-bottom:12px;padding:16px 20px;" data-ag-id="{{ $ag->id }}" data-ag-timestamp="{{ $agTimestamp }}" data-ag-nome="{{ $ag->barbeiro->nome }}" data-ag-data="{{ \Carbon\Carbon::parse($ag->data)->format('d/m') }}" data-ag-hora="{{ $ag->hora_inicio->format('H:i') }}">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <strong style="color:var(--text);font-size:15px;">{{ $ag->barbeiro->nome }}</strong>
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
                    @if($agAvaliado)
                    <span style="font-size:11px;padding:3px 8px;border-radius:6px;font-weight:600;background:var(--success-bg);color:var(--success);">
                        <svg class="icon icon-xs"><use href="#i-check"/></svg> Avaliado
                    </span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">
                    <svg class="icon icon-xs" style="margin-right:2px;"><use href="#i-calendar"/></svg>
                    {{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}
                    <svg class="icon icon-xs" style="margin:0 2px 0 8px;"><use href="#i-clock"/></svg>
                    {{ $ag->hora_inicio->format('H:i') }}
                </div>
                @foreach($ag->servicos as $s)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:var(--card-solid);border:1px solid var(--border);border-radius:var(--r-sm);margin-bottom:6px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg class="icon icon-xs" style="color:var(--accent);flex-shrink:0;"><use href="#i-scissors-2"/></svg>
                        <span style="font-size:13px;font-weight:600;color:var(--text);">{{ $s->nome }}</span>
                    </div>
                    @if($ag->status == 'realizado' && !$agAvaliado)
                    <button style="height:30px;padding:0 12px;border-radius:6px;background:var(--accent-glow);color:var(--accent);border:1px solid var(--accent);font-weight:600;font-size:12px;font-family:inherit;cursor:pointer;white-space:nowrap;" wire:click="abrirAvaliacao({{ $ag->id }})">
                        <svg class="icon icon-xs"><use href="#i-star"/></svg> Avaliar
                    </button>
                    @elseif($ag->status == 'realizado' && $agAvaliado && $loop->first)
                    <span style="font-size:12px;color:var(--success);font-weight:600;">
                        <svg class="icon icon-xs"><use href="#i-check"/></svg> Avaliado
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
            <div style="text-align:right;flex-shrink:0;margin-left:12px;">
                <strong style="color:var(--accent);font-size:16px;white-space:nowrap;">R$ {{ number_format($ag->total, 2, ',', '.') }}</strong>
            </div>
        </div>
    </div>
    @empty
    <div class="card-base" style="text-align:center;padding:40px 20px;">
        <svg class="icon" style="width:40px;height:40px;color:var(--text-faint);margin-bottom:12px;"><use href="#i-calendar"/></svg>
        <p style="color:var(--text-muted);margin-bottom:16px;">Nenhum agendamento encontrado.</p>
        <a href="{{ $slug ? route('tenant.site.agendar', $slug) : route('site.agendar') }}" class="btn-primary-c" style="text-decoration:none;display:inline-flex;">
            <svg class="icon icon-sm"><use href="#i-check"/></svg>
            Agendar Agora
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
                <svg class="icon" style="color:var(--accent);margin-right:4px;"><use href="#i-star"/></svg> Avaliar Serviço
            </h5>
            <p style="font-size:13px;color:var(--text-muted);text-align:center;margin-bottom:16px;">
                {{ $ag->barbeiro->nome }} · {{ $ag->servicos->first()?->nome ?? 'Serviço' }}
            </p>
            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:28px;letter-spacing:6px;cursor:pointer;">
                    @for($i = 1; $i <= 5; $i++)
                    <svg class="icon" style="width:28px;height:28px;color:{{ $i <= $avaliacao_rating ? 'var(--accent)' : 'var(--text-faint)' }};cursor:pointer;" wire:click="setRating({{ $i }})"><use href="#i-star"/></svg>
                    @endfor
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Clique nas estrelas para avaliar</div>
            </div>
            <textarea wire:model.blur="avaliacao_comentario" rows="3" style="width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--border-strong);background:var(--bg);color:var(--text);font-size:13px;font-family:inherit;resize:vertical;" placeholder="Deixe seu comentário (opcional)..."></textarea>
            <div style="display:flex;gap:10px;margin-top:16px;">
                <button class="btn-ghost-c" style="flex:1;justify-content:center;height:42px;font-size:13px;" wire:click="fecharAvaliacao">Cancelar</button>
                <button class="btn-primary-c" style="flex:1;justify-content:center;height:42px;font-size:13px;" wire:click="salvarAvaliacao">Enviar</button>
            </div>
        </div>
    </div>
    @endif
    @endif
{{-- Reminder Modal --}}
<div wire:ignore id="reminderModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);align-items:center;justify-content:center;padding:20px;">
    <div style="background:var(--card-solid);border:1px solid var(--border-strong);border-radius:var(--r-lg);padding:32px;max-width:400px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.4);">
        <div style="width:64px;height:64px;border-radius:50%;background:var(--accent-glow);display:grid;place-items:center;margin:0 auto 16px;">
            <svg class="icon" style="width:32px;height:32px;color:var(--accent);"><use href="#i-bell"/></svg>
        </div>
        <h3 style="font-size:20px;font-weight:800;margin-bottom:4px;color:var(--text);">Lembrete de Agendamento</h3>
        <p id="reminderMsg" style="font-size:15px;color:var(--text-muted);margin:12px 0 24px;line-height:1.5;"></p>
        <button onclick="fecharReminder()" class="btn-primary-c" style="width:100%;justify-content:center;height:48px;font-size:15px;">OK, entendi</button>
    </div>
</div>
</div>

@push('scripts')
<script>
(function(){
    if (!('Notification' in window)) return;
    if (Notification.permission === 'default') Notification.requestPermission();

    var notifiedIds = {};
    var reminderTimeout = null;
    var audioCtx = null;

    function playBeep() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            var n = audioCtx.currentTime;
            [
                { f: 880, t: 0 },
                { f: 1100, t: 0.25 },
                { f: 880, t: 0.5 },
            ].forEach(function(t) {
                var o = audioCtx.createOscillator();
                var g = audioCtx.createGain();
                o.type = 'triangle';
                o.frequency.value = t.f;
                g.gain.setValueAtTime(0, n + t.t);
                g.gain.linearRampToValueAtTime(0.25, n + t.t + 0.02);
                g.gain.exponentialRampToValueAtTime(0.001, n + t.t + 1.5);
                o.connect(g);
                g.connect(audioCtx.destination);
                o.start(n + t.t);
                o.stop(n + t.t + 1.6);
            });
        } catch(e) {}
    }

    window.fecharReminder = function() {
        var modal = document.getElementById('reminderModal');
        if (modal) {
            modal.style.display = 'none';
            modal.style.opacity = '';
        }
        if (reminderTimeout) {
            clearTimeout(reminderTimeout);
            reminderTimeout = null;
        }
    };

    function showReminderModal(msg) {
        document.getElementById('reminderMsg').textContent = msg;
        var modal = document.getElementById('reminderModal');
        modal.style.display = 'flex';
        playBeep();
        if (reminderTimeout) clearTimeout(reminderTimeout);
        reminderTimeout = setTimeout(fecharReminder, 30000);
    }

    function checkReminders() {
        var cards = document.querySelectorAll('[data-ag-timestamp]');
        var now = Math.floor(Date.now() / 1000);

        cards.forEach(function(card) {
            var ts = parseInt(card.dataset.agTimestamp);
            var id = card.dataset.agId;
            var nome = card.dataset.agNome || 'Barbeiro';
            var data = card.dataset.agData || '';
            var hora = card.dataset.agHora || '';

            if (notifiedIds[id]) return;

            var diffMin = Math.floor((ts - now) / 60);
            var tipo = null;

            if (diffMin >= 55 && diffMin <= 65) tipo = '1 hora';
            else if (diffMin >= 25 && diffMin <= 35) tipo = '30 minutos';
            else if (diffMin >= 10 && diffMin <= 20) tipo = '15 minutos';
            else if (diffMin >= 3 && diffMin <= 7) tipo = '5 minutos';

            // dispara se estiver na janela OU se acabou de passar (phone ficou em background)
            if (!tipo && diffMin <= 0 && diffMin > -10) tipo = 'agora';

            if (tipo) {
                notifiedIds[id] = true;
                var msg = tipo === 'agora'
                    ? 'Seu agendamento com ' + nome + ' era agora! (' + data + ' às ' + hora + ')'
                    : 'Seu agendamento com ' + nome + ' é em ' + tipo + '! (' + data + ' às ' + hora + ')';
                showReminderModal(msg);
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        var n = new Notification('Lembrete de Agendamento', {
                            body: msg,
                            icon: '/images/logo.jpg',
                            tag: 'lembrete-' + id,
                        });
                        setTimeout(function() { n.close(); }, 15000);
                        n.onclick = function() { window.focus(); fecharReminder(); };
                    } catch(e) {}
                }
            }
        });
    }

    checkReminders();
    setInterval(checkReminders, 10000);

    // dispara check ao voltar do background (celular / aba inativa)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkReminders();
        }
    });
})();
</script>
@endpush