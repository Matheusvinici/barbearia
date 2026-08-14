<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Financeiro - {{ $nomeMes }}</title>
    <style>
        * { font-family: 'Helvetica', 'Arial', sans-serif; box-sizing: border-box; }
        body { margin: 24px; color: #111; font-size: 12px; }
        .head { border-bottom: 3px solid #0d0d12; padding-bottom: 12px; margin-bottom: 18px; }
        .head h1 { font-size: 19px; margin: 0 0 4px; }
        .head .sub { color: #666; font-size: 12px; }
        .totals { display: flex; gap: 10px; margin-bottom: 18px; }
        .total-box { flex: 1; border: 1px solid #ddd; border-radius: 10px; padding: 10px 14px; }
        .total-box .l { font-size: 10px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; }
        .total-box .v { font-size: 17px; font-weight: 700; margin-top: 3px; }
        .in { color: #15803d; }
        .out { color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #0d0d12; color: #fff; padding: 8px 10px; text-align: left; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.04em; }
        td { padding: 7px 10px; border-bottom: 1px solid #e5e5e5; font-size: 11.5px; }
        tr:nth-child(even) td { background: #f7f7f7; }
        .val { font-weight: 700; text-align: right; white-space: nowrap; }
        .chip { font-size: 9.5px; font-weight: 700; padding: 2px 7px; border-radius: 6px; }
        .chip.in { background: #dcfce7; color: #15803d; }
        .chip.out { background: #fee2e2; color: #b91c1c; }
        .foot { margin-top: 22px; font-size: 10px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="head">
        <h1>Relatório Financeiro</h1>
        <div class="sub">{{ $nomeMes }} · Gerado em {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="totals">
        <div class="total-box">
            <div class="l">Entradas</div>
            <div class="v in">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
        </div>
        <div class="total-box">
            <div class="l">Saídas</div>
            <div class="v out">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</div>
        </div>
        <div class="total-box">
            <div class="l">Saldo</div>
            <div class="v" style="color: {{ $saldo >= 0 ? '#15803d' : '#b91c1c' }};">R$ {{ number_format($saldo, 2, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:70px">Data</th>
                <th style="width:80px">Tipo</th>
                <th>Descrição</th>
                <th style="width:90px">Origem</th>
                <th style="width:110px" class="val">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimentacoes as $m)
            @php
                $origem = $m->origem;
                $origemNome = match ($m->origem_type) {
                    App\Models\Agendamento::class => 'Serviço',
                    App\Models\Venda::class => 'Venda',
                    App\Models\Despesa::class => 'Despesa',
                    default => '—',
                };
            @endphp
            <tr>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td><span class="chip {{ $m->tipo }}">{{ $m->tipo === 'entrada' ? 'Entrada' : 'Saída' }}</span></td>
                <td>{{ $m->descricao }}</td>
                <td>{{ $origemNome }}</td>
                <td class="val {{ $m->tipo }}">{{ $m->tipo === 'entrada' ? '+' : '−' }} R$ {{ number_format($m->valor, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:20px;color:#888;">Nenhuma movimentação no período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="foot">Barber Control · Relatório gerado automaticamente</div>
</body>
</html>