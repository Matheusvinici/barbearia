@extends('layouts.app')
@section('title', 'Editar Caixa')
@section('breadcrumb', 'Financeiro > Caixa > Editar')

@section('content')
<div class="card">
    <div class="card-header"><h5>Editar Caixa - {{ $caixa->data->format('d/m/Y') }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.caixa.update', $caixa) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Saldo Inicial (R$)</label>
                    <input type="number" step="0.01" name="saldo_inicial" class="form-control" value="{{ old('saldo_inicial', $caixa->saldo_inicial) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Total Entradas (R$)</label>
                    <input type="number" step="0.01" name="total_entradas" class="form-control" value="{{ old('total_entradas', $caixa->total_entradas) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Total Saídas (R$)</label>
                    <input type="number" step="0.01" name="total_saidas" class="form-control" value="{{ old('total_saidas', $caixa->total_saidas) }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Saldo Final (R$)</label>
                    <input type="number" step="0.01" name="saldo_final" class="form-control" id="saldoFinal" value="{{ old('saldo_final', $caixa->saldo_final) }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes', $caixa->observacoes) }}</textarea>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Calculado:</strong> Saldo Inicial (R$ <span id="calcInicial">{{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</span>)
                + Entradas (R$ <span id="calcEntradas">{{ number_format($caixa->total_entradas, 2, ',', '.') }}</span>)
                - Saídas (R$ <span id="calcSaidas">{{ number_format($caixa->total_saidas, 2, ',', '.') }}</span>)
                = <strong>R$ <span id="calcResultado">{{ number_format($caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas, 2, ',', '.') }}</span></strong>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            <a href="{{ route('admin.caixa.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calcular() {
    const inicial = parseFloat($('input[name="saldo_inicial"]').val()) || 0;
    const entradas = parseFloat($('input[name="total_entradas"]').val()) || 0;
    const saidas = parseFloat($('input[name="total_saidas"]').val()) || 0;
    const resultado = inicial + entradas - saidas;
    $('#calcInicial').text(inicial.toFixed(2).replace('.', ','));
    $('#calcEntradas').text(entradas.toFixed(2).replace('.', ','));
    $('#calcSaidas').text(saidas.toFixed(2).replace('.', ','));
    $('#calcResultado').text(resultado.toFixed(2).replace('.', ','));
}
$('input[name="saldo_inicial"], input[name="total_entradas"], input[name="total_saidas"]').on('input', calcular);
</script>
@endpush
@endsection
