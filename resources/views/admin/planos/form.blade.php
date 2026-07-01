@extends('layouts.app')

@section('title', $edit ? 'Editar Plano' : 'Novo Plano')

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.planos.index') }}" style="color:inherit;text-decoration:none;">Planos</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar Plano' : 'Novo Plano' }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $edit ? 'Editando' : 'Cadastrando' }} plano</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<a href="{{ route('admin.planos.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<form action="{{ $edit ? route('admin.planos.update', $plano) : route('admin.planos.store') }}" method="POST">
    @csrf
    @if($edit) @method('PUT') @endif

    <div class="main-grid">
        <div class="col-stack">

            {{-- 1. Basic Info --}}
            <div class="panel fade-in d1">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></div>
                        <div>
                            <h2 class="panel-title">Informações do Plano</h2>
                            <div class="panel-subtitle">Dados básicos e valores</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Nome do Plano *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></span>
                                <input type="text" name="nome" class="form-input @error('nome') form-error @enderror" placeholder="Ex: Plano Mensal" value="{{ old('nome', $edit ? $plano->nome : '') }}">
                            </div>
                            @error('nome')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Valor *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="valor" class="form-input @error('valor') form-error @enderror" placeholder="0,00" value="{{ old('valor', $edit ? $plano->valor : '') }}">
                            </div>
                            @error('valor')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ativo</label>
                            <div class="toggle-row" style="margin-top:4px;">
                                <button type="button" class="switch {{ old('ativo', $edit ? $plano->ativo : true) ? 'on' : '' }}" data-switch="ativo"></button>
                                <input type="hidden" name="ativo" value="{{ old('ativo', $edit ? ($plano->ativo ? 1 : 0) : 1) }}">
                            </div>
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Descrição <span class="mut">(opcional)</span></label>
                            <textarea name="descricao" class="form-textarea @error('descricao') form-error @enderror" placeholder="Descrição do plano...">{{ old('descricao', $edit ? $plano->descricao : '') }}</textarea>
                            @error('descricao')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Service Quotas --}}
            <div class="panel fade-in d2">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"/></svg></div>
                        <div>
                            <h2 class="panel-title">Cotas de Serviços</h2>
                            <div class="panel-subtitle">Defina quantas vezes cada serviço está incluso</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body" style="padding:0;">
                    <table class="data-table" style="border:0;">
                        <thead>
                            <tr><th>Serviço</th><th style="width:120px;">Quantidade</th></tr>
                        </thead>
                        <tbody>
                            @foreach($servicos as $s)
                            @php
                                $qtd = 0;
                                if ($edit && $plano->quotas->contains('servico_id', $s->id)) {
                                    $qtd = $plano->quotas->where('servico_id', $s->id)->first()->quantidade;
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="avatar-row">
                                        <div class="av amber">{{ mb_substr($s->nome, 0, 2) }}</div>
                                        <div class="info">
                                            <strong>{{ $s->nome }}</strong>
                                            <span>R$ {{ number_format($s->preco, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" name="quotas[{{ $loop->index }}][servico_id]" value="{{ $s->id }}">
                                    <input type="number" name="quotas[{{ $loop->index }}][quantidade]" class="form-input" style="width:80px;" value="{{ old('quotas.'.$loop->index.'.quantidade', $qtd) }}" min="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Right: Action Card --}}
        <div class="action-card">
            <div class="action-buttons fade-in d3">
                <button type="submit" class="btn-primary-c">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    {{ $edit ? 'Atualizar Plano' : 'Salvar Plano' }}
                </button>
                <a href="{{ route('admin.planos.index') }}" class="btn-ghost-c" style="width:100%;justify-content:center;height:48px;">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                    Cancelar
                </a>
            </div>

            <div class="panel" style="background:transparent;border:none;backdrop-filter:none;padding:0;">
                <div class="panel-body" style="padding:0;">
                    <div class="tips-list">
                        <div class="tip-item">
                            <div class="tip-ic"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8M14 7h7v7"/></svg></div>
                            <div class="tip-info">
                                <div class="t">Preço Atrativo</div>
                                <div class="d">Planos com desconto de 10-20% em relação à soma dos serviços avulsos costumam converter melhor.</div>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-ic"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg></div>
                            <div class="tip-info">
                                <div class="t">Cotas Inteligentes</div>
                                <div class="d">Defina 0 para serviços que não fazem parte do plano.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.switch[data-switch="ativo"]').on('click', function() {
        $(this).toggleClass('on');
        $(this).next('input[type="hidden"]').val($(this).hasClass('on') ? 1 : 0);
    });
});
</script>
@endpush
