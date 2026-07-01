@extends('layouts.app')

@section('title', $edit ? 'Editar Despesa' : 'Nova Despesa')

@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.despesas.index') }}" style="color:inherit; text-decoration:none;">Financeiro</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar' : 'Nova' }} Despesa</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon"><use href="#i-menu"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon"><use href="#i-sun"/></svg></button>
<button class="icon-btn"><svg class="icon"><use href="#i-bell"/></svg><span class="dot-notif"></span></button>
<a href="{{ route('admin.despesas.index') }}" class="btn-ghost-c"><svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>Voltar</a>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">
        <div class="panel fade-in d1">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon"><use href="#i-receipt"/></svg></div>
                    <div>
                        <h2 class="panel-title">{{ $edit ? 'Editar Despesa' : 'Nova Despesa' }}</h2>
                        <div class="panel-subtitle">Campos com * são obrigatórios</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ $edit ? route('admin.despesas.update', $despesa) : route('admin.despesas.store') }}" method="POST">
                    @csrf
                    @if($edit) @method('PUT') @endif

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Descrição *</label>
                            <input type="text" name="descricao" class="form-input" value="{{ old('descricao', $edit ? $despesa->descricao : '') }}" placeholder="Ex: Aluguel do mês" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Categoria *</label>
                            <select name="categoria" class="form-select" required>
                                <option value="Aluguel" {{ $edit && $despesa->categoria == 'Aluguel' ? 'selected' : '' }}>Aluguel</option>
                                <option value="Água/Luz" {{ $edit && $despesa->categoria == 'Água/Luz' ? 'selected' : '' }}>Água/Luz</option>
                                <option value="Internet" {{ $edit && $despesa->categoria == 'Internet' ? 'selected' : '' }}>Internet</option>
                                <option value="Produtos" {{ $edit && $despesa->categoria == 'Produtos' ? 'selected' : '' }}>Produtos</option>
                                <option value="Equipamentos" {{ $edit && $despesa->categoria == 'Equipamentos' ? 'selected' : '' }}>Equipamentos</option>
                                <option value="Marketing" {{ $edit && $despesa->categoria == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Salários" {{ $edit && $despesa->categoria == 'Salários' ? 'selected' : '' }}>Salários</option>
                                <option value="Impostos" {{ $edit && $despesa->categoria == 'Impostos' ? 'selected' : '' }}>Impostos</option>
                                <option value="Comissão" {{ $edit && $despesa->categoria == 'Comissão' ? 'selected' : '' }}>Comissão</option>
                                <option value="Outros" {{ $edit && $despesa->categoria == 'Outros' ? 'selected' : '' }}>Outros</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Valor *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="valor" class="form-input" value="{{ old('valor', $edit ? $despesa->valor : '') }}" placeholder="0,00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Data Vencimento *</label>
                            <input type="date" name="data_vencimento" class="form-input" value="{{ old('data_vencimento', $edit ? $despesa->data_vencimento->format('Y-m-d') : '') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Forma Pagamento</label>
                            <select name="forma_pagamento" class="form-select">
                                <option value="">Selecione...</option>
                                <option value="Dinheiro" {{ $edit && $despesa->forma_pagamento == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                <option value="Pix" {{ $edit && $despesa->forma_pagamento == 'Pix' ? 'selected' : '' }}>Pix</option>
                                <option value="Cartão Débito" {{ $edit && $despesa->forma_pagamento == 'Cartão Débito' ? 'selected' : '' }}>Cartão Débito</option>
                                <option value="Cartão Crédito" {{ $edit && $despesa->forma_pagamento == 'Cartão Crédito' ? 'selected' : '' }}>Cartão Crédito</option>
                                <option value="Boleto" {{ $edit && $despesa->forma_pagamento == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                <option value="Transferência" {{ $edit && $despesa->forma_pagamento == 'Transferência' ? 'selected' : '' }}>Transferência</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="pago" class="form-select">
                                <option value="0" {{ $edit && !$despesa->pago ? 'selected' : '' }}>Pendente</option>
                                <option value="1" {{ $edit && $despesa->pago ? 'selected' : '' }}>Pago</option>
                            </select>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label">Observações <span class="mut">(opcional)</span></label>
                            <textarea name="observacoes" class="form-textarea" placeholder="Adicione uma observação interna...">{{ old('observacoes', $edit ? $despesa->observacoes : '') }}</textarea>
                        </div>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:24px; padding-top:20px; border-top:1px solid var(--border);">
                        <button type="submit" class="btn-primary-c"><svg class="icon icon-sm"><use href="#i-check"/></svg>{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
                        <a href="{{ route('admin.despesas.index') }}" class="btn-ghost-c">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="action-card">
        <div class="panel" style="background:transparent; border:none; backdrop-filter:none; padding:0;">
            <div class="panel-body" style="padding:0;">
                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-info"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Categorização Correta</div>
                            <div class="d">Use a categoria certa para relatórios financeiros precisos.</div>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-alert"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Controle de Vencimentos</div>
                            <div class="d">Mantenha as datas atualizadas para não perder prazos.</div>
                        </div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-receipt"/></svg></div>
                        <div class="tip-info">
                            <div class="t">Despesas Fixas</div>
                            <div class="d">Despesas recorrentes ajudam a prever o fluxo de caixa.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
