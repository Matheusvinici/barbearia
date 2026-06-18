<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Relatório de Faturamento</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f4f6f9; }
    .text-right { text-align: right; }
    h2 { text-align: center; color: #333; }
    .total { font-weight: bold; font-size: 14px; }
</style>
</head>
<body>
    <h2>Relatório de Faturamento</h2>
    <p>Período: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr><th>Data</th><th>Cliente</th><th>Barbeiro</th><th>Serviços</th><th>Valor</th></tr>
        </thead>
        <tbody>
            @foreach($agendamentos as $ag)
            <tr>
                <td>{{ $ag->data->format('d/m/Y') }}</td>
                <td>{{ $ag->cliente->nome }}</td>
                <td>{{ $ag->barbeiro->nome }}</td>
                <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                <td class="text-right">R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><td colspan="4" class="text-right total">Total:</td><td class="text-right total">R$ {{ number_format($totalFaturamento, 2, ',', '.') }}</td></tr>
        </tfoot>
    </table>
    <p><small>Relatório gerado em {{ now()->format('d/m/Y H:i') }}</small></p>
</body>
</html>
