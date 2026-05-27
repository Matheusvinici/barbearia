@extends('layouts.app')
@section('title', $edit ? 'Editar Despesa' : 'Nova Despesa')
@section('breadcrumb', 'Financeiro > Despesas')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Despesa' : 'Nova Despesa' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.despesas.update', $despesa) : route('admin.despesas.store') }}" method="POST">
            @csrf @if($edit) @method('PUT') @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Descrição</label>
                    <input type="text" name="descricao" class="form-control" value="{{ old('descricao', $edit ? $despesa->descricao : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Valor (R$)</label>
                    <input type="number" step="0.01" name="valor" class="form-control" value="{{ old('valor', $edit ? $despesa->valor : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control" required>
                        <option value="Aluguel" {{ $edit && $despesa->categoria == 'Aluguel' ? 'selected' : '' }}>Aluguel</option>
                        <option value="Água/Luz" {{ $edit && $despesa->categoria == 'Água/Luz' ? 'selected' : '' }}>Água/Luz</option>
                        <option value="Produtos" {{ $edit && $despesa->categoria == 'Produtos' ? 'selected' : '' }}>Produtos</option>
                        <option value="Equipamentos" {{ $edit && $despesa->categoria == 'Equipamentos' ? 'selected' : '' }}>Equipamentos</option>
                        <option value="Marketing" {{ $edit && $despesa->categoria == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Salários" {{ $edit && $despesa->categoria == 'Salários' ? 'selected' : '' }}>Salários</option>
                        <option value="Impostos" {{ $edit && $despesa->categoria == 'Impostos' ? 'selected' : '' }}>Impostos</option>
                        <option value="Outros" {{ $edit && $despesa->categoria == 'Outros' ? 'selected' : '' }}>Outros</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Data de Vencimento</label>
                    <input type="date" name="data_vencimento" class="form-control" value="{{ old('data_vencimento', $edit ? $despesa->data_vencimento->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Data de Pagamento</label>
                    <input type="date" name="data_pagamento" class="form-control" value="{{ old('data_pagamento', $edit && $despesa->data_pagamento ? $despesa->data_pagamento->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Forma de Pagamento</label>
                    <select name="forma_pagamento" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="Dinheiro" {{ $edit && $despesa->forma_pagamento == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="Pix" {{ $edit && $despesa->forma_pagamento == 'Pix' ? 'selected' : '' }}>Pix</option>
                        <option value="Cartão Débito" {{ $edit && $despesa->forma_pagamento == 'Cartão Débito' ? 'selected' : '' }}>Cartão Débito</option>
                        <option value="Cartão Crédito" {{ $edit && $despesa->forma_pagamento == 'Cartão Crédito' ? 'selected' : '' }}>Cartão Crédito</option>
                        <option value="Boleto" {{ $edit && $despesa->forma_pagamento == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                        <option value="Transferência" {{ $edit && $despesa->forma_pagamento == 'Transferência' ? 'selected' : '' }}>Transferência</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="pago" class="form-check-input" value="1" id="pago" {{ $edit && $despesa->pago ? 'checked' : '' }}>
                        <label class="form-check-label" for="pago">Pago</label>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes', $edit ? $despesa->observacoes : '') }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.despesas.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
