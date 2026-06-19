@extends('layouts.app')
@section('title', $edit ? 'Editar Plano' : 'Novo Plano')
@section('breadcrumb', 'Planos')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Plano' : 'Novo Plano' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.planos.update', $plano) : route('admin.planos.store') }}" method="POST">
            @csrf @if($edit) @method('PUT') @endif
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome do Plano</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $plano->nome : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Valor (R$)</label>
                    <input type="number" step="0.01" name="valor" class="form-control" value="{{ old('valor', $edit ? $plano->valor : '') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $edit && $plano->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $edit && !$plano->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" rows="2">{{ old('descricao', $edit ? $plano->descricao : '') }}</textarea>
                </div>
            </div>

            <h6 class="mt-4 mb-3">Cotas de Serviços</h6>
            <p class="text-muted small">Defina quantas vezes cada serviço está incluso neste plano.</p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>Serviço</th><th>Quantidade</th></tr></thead>
                    <tbody>
                        @foreach($servicos as $s)
                        @php
                            $qtd = 0;
                            if ($edit && $plano->quotas->contains('servico_id', $s->id)) {
                                $qtd = $plano->quotas->where('servico_id', $s->id)->first()->quantidade;
                            }
                        @endphp
                        <tr>
                            <td>{{ $s->nome }} <small class="text-muted">R$ {{ number_format($s->preco, 2, ',', '.') }}</small></td>
                            <td style="width:150px">
                                <input type="hidden" name="quotas[{{ $loop->index }}][servico_id]" value="{{ $s->id }}">
                                <input type="number" name="quotas[{{ $loop->index }}][quantidade]" class="form-control form-control-sm" value="{{ old('quotas.'.$loop->index.'.quantidade', $qtd) }}" min="0">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.planos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
