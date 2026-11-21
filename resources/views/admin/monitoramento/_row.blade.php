@php
use App\Http\Controllers\Admin\MonitoramentoController;
$cliente = $r['cliente'];
$digits = $r['digits'];
$wa = $digits ? MonitoramentoController::waLink($digits, $r['mensagem']) : null;
@endphp
<tr>
    <td data-label="Cliente">
        <strong>{{ $cliente->nome }}</strong>
        @if($r['duplicado'] ?? false)
            <span class="badge-dup" title="Mesmo telefone em 2+ cadastros — possível duplicado">duplicado?</span>
        @endif
        <div class="cell-muted" style="font-size:11.5px;">{{ $cliente->email ?? 'sem e-mail' }}</div>
    </td>
    <td data-label="Telefone">{{ $cliente->telefone ?? '—' }}</td>
    <td data-label="Visitas" class="text-center"><strong>{{ $r['total'] }}</strong></td>
    <td data-label="Última">{{ $r['ultima'] ? $r['ultima']->format('d/m/Y') : '—' }}</td>
    <td data-label="Dias fora">
        @if($r['dias'] !== null)
            <span class="badge-c {{ $r['dias'] >= 60 ? 'red' : ($r['dias'] >= 30 ? 'amber' : 'gray') }}">{{ $r['dias'] }}d</span>
        @else — @endif
    </td>
    @if(!empty($showBarbeiro))
        <td data-label="Barbeiro">{{ $r['ultimo_barbeiro'] ?? '—' }}</td>
    @elseif(!empty($showMotivo))
        <td data-label="Motivo">{{ $r['queda_motivo'] ?? '—' }}</td>
    @else
        <td data-label="Média">{{ $r['media'] !== null ? $r['media'] . 'd' : '—' }}</td>
    @endif
    <td data-label="Zap">
        @if($wa)
            <a class="zap-btn zap-link" href="{{ $wa }}" target="_blank" rel="noopener" data-digits="{{ $digits }}" data-nome="{{ $cliente->nome }}" title="Abrir WhatsApp Web com mensagem pronta">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.4 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.4-.7-2.9-1.2-4.7-4.1-4.9-4.3-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5s.8 1.9.8 2c.1.1.1.3 0 .5-.3.6-.6.8-.4 1.1.7 1.2 1.6 2 2.8 2.6.3.2.5.1.7-.1l.8-.9c.2-.3.4-.2.7-.1l2 1c.3.1.5.2.6.4 0 .1 0 .7-.4 1.3z"/></svg>
                Chamar
            </a>
        @else
            <span class="zap-btn disabled" title="Sem telefone válido">Sem zap</span>
        @endif
    </td>
</tr>
