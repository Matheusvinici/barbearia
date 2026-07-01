@extends('layouts.app')

@section('title', 'Editar Caixa')

@push('styles')
<style>
.action-btn { height: 32px; padding: 0 12px; border-radius: 8px; border: 1.5px solid; background: transparent; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 150ms; font-size: 12.5px; font-weight: 600; font-family: inherit; text-decoration: none; white-space: nowrap; }
.action-btn.danger { color: var(--danger); border-color: var(--danger); }
.action-btn.danger:hover { background: var(--danger-bg); }
.form-grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; }
@media (max-width:768px) { .form-grid-4 { grid-template-columns: 1fr 1fr; } }
@media (max-width:480px) { .form-grid-4 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.caixa.index') }}" style="color:inherit;text-decoration:none;">Caixa</a>
<span class="sep">/</span>
<span class="current">Editar</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Editando caixa de {{ $caixa->data->format('d/m/Y') }}</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.caixa.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">

        <div class="panel fade-in d1">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z"/></svg></div>
                    <div>
                        <h2 class="panel-title">Editar Caixa</h2>
                        <div class="panel-subtitle">{{ $caixa->data->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.caixa.update', $caixa) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="form-grid-4">
                        <div class="form-group">
                            <label class="form-label">Saldo Inicial (R$) *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="saldo_inicial" class="form-input" value="{{ old('saldo_inicial', $caixa->saldo_inicial) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Entradas (R$) *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="total_entradas" class="form-input" value="{{ old('total_entradas', $caixa->total_entradas) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Total Saídas (R$) *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="total_saidas" class="form-input" value="{{ old('total_saidas', $caixa->total_saidas) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Saldo Final (R$) *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="saldo_final" class="form-input" id="saldoFinal" value="{{ old('saldo_final', $caixa->saldo_final) }}" required>
                            </div>
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-textarea" rows="2">{{ old('observacoes', $caixa->observacoes) }}</textarea>
                        </div>
                    </div>

                    {{-- Calculated --}}
                    <div class="panel" style="background:var(--accent-glow);border:1px solid var(--accent);margin-top:16px;">
                        <div class="panel-body" style="padding:12px 16px;font-size:13px;">
                            <strong>Calculado:</strong>
                            Saldo Inicial (R$ <span id="calcInicial">{{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</span>)
                            + Entradas (R$ <span id="calcEntradas">{{ number_format($caixa->total_entradas, 2, ',', '.') }}</span>)
                            - Saídas (R$ <span id="calcSaidas">{{ number_format($caixa->total_saidas, 2, ',', '.') }}</span>)
                            = <strong>R$ <span id="calcResultado">{{ number_format($caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas, 2, ',', '.') }}</span></strong>
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;margin-top:20px;">
                        <button type="submit" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Salvar Alterações</button>
                        <a href="{{ route('admin.caixa.index') }}" class="btn-ghost-c" style="padding:0 20px;height:44px;">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>

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
