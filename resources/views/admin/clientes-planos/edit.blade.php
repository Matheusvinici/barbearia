@extends('layouts.app')
@section('title', 'Editar Vínculo')
@section('breadcrumb', 'Clientes Planos')

@php
$slug = request()->route('barbearia')?->slug;
$updateRoute = $slug ? route('tenant.admin.clientes-planos.update', [$slug, $vinculo]) : route('admin.clientes-planos.update', $vinculo);
$backRoute = $slug ? route('tenant.admin.clientes-planos.index', $slug) : route('admin.clientes-planos.index');
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Vínculo - {{ $vinculo->cliente->nome }}</h5>
        <small class="text-muted">Validade, cotas e pagamento (caixa)</small>
    </div>
    <div class="card-body">
        <form action="{{ $updateRoute }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Cliente</label>
                    <input type="text" class="form-control" value="{{ $vinculo->cliente->nome }} - {{ $vinculo->cliente->telefone }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Plano *</label>
                    <select name="plano_id" class="form-control" required>
                        @foreach($planos as $p)
                        <option value="{{ $p->id }}" {{ $vinculo->plano_id == $p->id ? 'selected' : '' }}>{{ $p->nome }} - R$ {{ number_format($p->valor, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Data Início *</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ $vinculo->data_inicio->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Validade (Vencimento)</label>
                    <input type="date" name="vencimento" class="form-control" value="{{ ($vinculo->vencimento ?? $vinculo->data_fim) ? ($vinculo->vencimento ?? $vinculo->data_fim)->format('Y-m-d') : '' }}">
                    <small class="text-muted">Se vazio, usa Data Fim</small>
                    <input type="hidden" name="data_fim" id="data_fim_hidden" value="{{ $vinculo->data_fim ? $vinculo->data_fim->format('Y-m-d') : '' }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Data Fim (legado)</label>
                    <input type="date" class="form-control" value="{{ $vinculo->data_fim ? $vinculo->data_fim->format('Y-m-d') : '' }}" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $vinculo->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ !$vinculo->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Valor Pago</label>
                    <input type="number" step="0.01" name="valor_pago" class="form-control" value="{{ $vinculo->valor_pago }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Forma Pagamento</label>
                    <select name="forma_pagamento" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach(['Dinheiro','Pix','Cartão de Crédito','Cartão de Débito','Boleto','Outro'] as $f)
                        <option value="{{ $f }}" {{ $vinculo->forma_pagamento==$f ? 'selected':'' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="pago" value="1" class="form-check-input" id="pagoCheck" {{ $vinculo->pago ? 'checked':'' }}>
                        <label for="pagoCheck" class="form-check-label fw-bold">Pago? (lança no Caixa ao salvar)</label>
                    </div>
                </div>
                @if($vinculo->pago_em)
                <div class="col-md-3 mb-3">
                    <label>Pago em</label>
                    <input type="text" class="form-control" value="{{ $vinculo->pago_em->format('d/m/Y H:i') }}" disabled>
                </div>
                @endif
                <div class="col-md-4 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control" value="{{ $vinculo->cpf }}" placeholder="000.000.000-00">
                </div>
                <div class="col-md-8 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ $vinculo->observacoes }}</textarea>
                </div>
                <div class="col-12">
                    <div class="card bg-light border">
                        <div class="card-body p-3">
                            <h6 class="mb-2">Cotas deste vínculo</h6>
                            @foreach($vinculo->plano->quotas as $q)
                                @php $usada = $vinculo->usos->where('servico_id', $q->servico_id)->count(); $rest = max(0, $q->quantidade - $usada); @endphp
                                <div class="d-flex justify-content-between border-bottom py-1" style="font-size:13px">
                                    <span>{{ $q->servico->nome }} (contratado: {{ $q->quantidade }})</span>
                                    <span>usado <strong>{{ $usada }}</strong> · restante <strong class="{{ $rest>0?'text-success':'text-danger' }}">{{ $rest }}</strong> @if($usada >= $q->quantidade) <span class="badge bg-danger ms-1">excedido</span> @else <span class="badge bg-success ms-1">dentro</span> @endif</span>
                                </div>
                            @endforeach
                            <div class="mt-2 small">
                                Total: <strong>{{ $vinculo->total_usada }}/{{ $vinculo->total_contratada }}</strong> usados · <strong>{{ $vinculo->total_restante }}</strong> restantes · Validade: <strong>{{ ($vinculo->vencimento ?? $vinculo->data_fim)? ($vinculo->vencimento ?? $vinculo->data_fim)->format('d/m/Y') : '—' }}</strong>
                                @if($vinculo->expirado) <span class="badge bg-danger">VENCIDO</span> @else <span class="badge bg-success">VÁLIDO ({{ $vinculo->dias_para_vencer }} dias)</span> @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="{{ $backRoute }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.querySelector('input[name="vencimento"]')?.addEventListener('change', function(){ document.getElementById('data_fim_hidden').value = this.value; });
</script>
@endpush
@endsection
