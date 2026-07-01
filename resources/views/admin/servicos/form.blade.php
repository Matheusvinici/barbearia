@extends('layouts.app')

@section('title', $edit ? 'Editar Serviço' : 'Novo Serviço')

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<a href="{{ route('admin.servicos.index') }}" style="color:inherit;text-decoration:none;">Serviços</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar Serviço' : 'Novo Serviço' }}</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $edit ? 'Editando' : 'Cadastrando' }} novo serviço</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-linecap="round"/></svg></button>
<button class="icon-btn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 6.44v6.72M9 16.5h6"/><path d="M8.5 2.5H15.5c.55 0 1 .45 1 1v.74c0 .3.13.59.36.78.83.69 1.27 1.91 1.27 3.45v5.95c0 1.84-1.27 3.43-3.07 3.83-.97.22-1.96.36-2.95.41-.51.03-1.02.04-1.53.04s-1.02-.01-1.53-.04c-.99-.05-1.98-.19-2.95-.41-1.8-.4-3.07-1.99-3.07-3.83V8.47c0-1.54.44-2.76 1.27-3.45.23-.19.36-.48.36-.78V3.5c0-.55.45-1 1-1z" stroke-linejoin="round"/></svg><span class="dot-notif"></span></button>
<a href="{{ route('admin.servicos.index') }}" class="btn-ghost-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>Voltar</a>
@endsection

@section('content')
<form action="{{ $edit ? route('admin.servicos.update', $servico) : route('admin.servicos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($edit) @method('PUT') @endif

    <div class="main-grid">
        <div class="col-stack">

            {{-- 1. Basic Info --}}
            <div class="panel fade-in d1">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12V5a2 2 0 0 1 2-2h7l9 9-9 9-9-9z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg></div>
                        <div>
                            <h2 class="panel-title">Informações Básicas</h2>
                            <div class="panel-subtitle">Como o serviço será exibido</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Nome do Serviço *</label>
                            <div class="input-group">
                                <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"/></svg></span>
                                <input type="text" name="nome" class="form-input @error('nome') form-error @enderror" placeholder="Ex: Corte Degradê Navalhado" value="{{ old('nome', $edit ? $servico->nome : '') }}">
                            </div>
                            @error('nome')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Descrição <span class="mut">(opcional)</span></label>
                            <textarea name="descricao" class="form-textarea @error('descricao') form-error @enderror" placeholder="Descreva o serviço, o que está incluso e seus diferenciais...">{{ old('descricao', $edit ? $servico->descricao : '') }}</textarea>
                            @error('descricao')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Pricing & Duration --}}
            <div class="panel fade-in d2">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12v3.5c0 1.5-.5 2.5-2 2.5h-2v-3.25c0-.41-.34-.75-.75-.75h-3.5c-.41 0-.75.34-.75.75V18H4c-1.5 0-2-1-2-2.5V8.5C2 7 2.5 6 4 6h16c1.5 0 2 1 2 2.5V12z"/><path d="M2 9h4M2 13h4M14 9.5V7c0-1.5 1-2.5 2.5-2.5h2.05c.31 0 .57.25.62.55.06.4.07.81.07 1.2 0 1.55-.43 2.95-1.13 4.13-.21.35-.59.55-.99.55H14z"/></svg></div>
                        <div>
                            <h2 class="panel-title">Preço e Duração</h2>
                            <div class="panel-subtitle">Defina o valor, comissão e o tempo estimado</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Preço Cobrado *</label>
                            <div class="input-affix">
                                <span class="prefix">R$</span>
                                <input type="number" step="0.01" name="preco" class="form-input @error('preco') form-error @enderror" placeholder="0,00" value="{{ old('preco', $edit ? $servico->preco : '') }}">
                            </div>
                            @error('preco')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Comissão Barbeiro *</label>
                            <div class="input-affix">
                                <input type="number" name="comissao_percentual" class="form-input @error('comissao_percentual') form-error @enderror" placeholder="50" value="{{ old('comissao_percentual', $edit ? ($servico->comissao_percentual ?? 50) : 50) }}">
                                <span class="suffix"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5L5 19M8 8h.01M16 16h.01"/><circle cx="8" cy="8" r="4"/><circle cx="16" cy="16" r="4"/></svg></span>
                            </div>
                            @error('comissao_percentual')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duração Estimada *</label>
                            <div class="input-affix">
                                <input type="number" name="duracao_minutos" class="form-input @error('duracao_minutos') form-error @enderror" placeholder="30" value="{{ old('duracao_minutos', $edit ? $servico->duracao_minutos : 30) }}">
                                <span class="suffix">min</span>
                            </div>
                            @error('duracao_minutos')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Foto do Serviço <span class="mut">(opcional)</span></label>
                            <input type="file" name="foto" class="form-input @error('foto') form-error @enderror" accept="image/jpeg,image/png,image/webp">
                            <small style="font-size:11px;color:var(--text-faint);margin-top:4px;">Formatos: JPEG, PNG, WebP — Máx: 2MB</small>
                            @error('foto')<span style="font-size:12px;color:var(--danger);margin-top:4px;display:block;">{{ $message }}</span>@enderror
                            @if($edit && $servico->foto)
                            <div style="display:flex;align-items:center;gap:12px;margin-top:10px;">
                                <img src="{{ $servico->foto_url }}" alt="Foto" style="height:50px;border-radius:8px;object-fit:cover;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--danger);cursor:pointer;">
                                    <input type="checkbox" name="remover_foto" value="1" style="accent-color:var(--danger);"> Remover foto
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Availability --}}
            <div class="panel fade-in d3">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 1.5"/></svg></div>
                        <div>
                            <h2 class="panel-title">Disponibilidade</h2>
                            <div class="panel-subtitle">Dias e status do serviço</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid full">
                        <div class="form-group">
                            <label class="form-label">Dias Disponíveis</label>
                            <div class="days-grid">
                                @php $dias = old('dias', $edit ? ($servico->dias ?? ['seg','ter','qua','qui','sex','sab']) : ['seg','ter','qua','qui','sex','sab']); @endphp
                                @foreach(['seg'=>'Seg','ter'=>'Ter','qua'=>'Qua','qui'=>'Qui','sex'=>'Sex','sab'=>'Sáb','dom'=>'Dom'] as $k => $v)
                                <button type="button" class="day-box {{ in_array($k, $dias) ? 'active' : '' }}" data-day="{{ $k }}"><span class="d">{{ $v }}</span></button>
                                <input type="hidden" name="dias[]" value="{{ $k }}" {{ in_array($k, $dias) ? '' : 'disabled' }}>
                                @endforeach
                            </div>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-info">
                                <div class="t">Ativo</div>
                                <div class="d">Serviço disponível para agendamento.</div>
                            </div>
                            <button type="button" class="switch {{ old('ativo', $edit ? $servico->ativo : true) ? 'on' : '' }}" data-switch="ativo"></button>
                            <input type="hidden" name="ativo" value="{{ old('ativo', $edit ? ($servico->ativo ? 1 : 0) : 1) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Team Assignment --}}
            <div class="panel fade-in d4">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M13 20.5H6.5c-1.5 0-2.5-1-2.5-2.5 0-3.5 3-5.5 6-5.5.83 0 1.63.13 2.36.37"/><circle cx="10" cy="6.5" r="3.5"/><path d="M17.13 17.92l2.32 2.32c.21.21.55.21.76 0l1.55-1.55c.21-.21.21-.55 0-.76l-2.32-2.32a.54.54 0 0 1-.16-.38v-2.18c0-.29-.24-.53-.53-.53h-2.18a.54.54 0 0 1-.38-.16L14 9.95c-.18-.18-.49-.18-.67 0l-1.55 1.55c-.18.18-.18.49 0 .67l1.65 1.65c.1.1.16.24.16.38v2.18c0 .29.24.53.53.53h2.18c.14 0 .28.06.38.16z" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                        <div>
                            <h2 class="panel-title">Equipe Habilitada</h2>
                            <div class="panel-subtitle">Quais profissionais podem realizar este serviço?</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="team-grid">
                        @php $barbeirosSelecionados = old('barbeiros', $edit ? ($servico->barbeiros ?? []) : []); @endphp
                        @forelse(\App\Models\Barbeiro::where('ativo', true)->get() as $barbeiro)
                        @php $selected = in_array($barbeiro->id, $barbeirosSelecionados); @endphp
                        <div class="team-check {{ $selected ? 'active' : '' }}" data-barbeiro="{{ $barbeiro->id }}">
                            <div class="team-avatar av-amber">{{ mb_substr($barbeiro->nome, 0, 1) }}{{ mb_substr($barbeiro->nome, -1, 1) }}</div>
                            <div class="team-info"><div class="n">{{ $barbeiro->nome }}</div><div class="r">{{ $barbeiro->comissao_percentual ?? 50 }}% comissão</div></div>
                            <div class="check-circle"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg></div>
                            <input type="checkbox" name="barbeiros[]" value="{{ $barbeiro->id }}" {{ $selected ? 'checked' : '' }} style="display:none;">
                        </div>
                        @empty
                        <div style="grid-column:1/-1;padding:20px;text-align:center;color:var(--text-muted);font-size:13px;">Nenhum barbeiro ativo cadastrado.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Action Card --}}
        <div class="action-card">
            <div class="action-buttons fade-in d3">
                <button type="submit" class="btn-primary-c">
                    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                    {{ $edit ? 'Atualizar Serviço' : 'Salvar Serviço' }}
                </button>
                <a href="{{ route('admin.servicos.index') }}" class="btn-ghost-c" style="width:100%;justify-content:center;height:48px;">
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
                                <div class="t">Preço Competitivo</div>
                                <div class="d">Pesquise médias da região. Margem ideal acima de 60%.</div>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-ic"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg></div>
                            <div class="tip-info">
                                <div class="t">Descrição Venda</div>
                                <div class="d">Detalhar o que está incluso aumenta a conversão em 30%.</div>
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
    $('.day-box').on('click', function() {
        $(this).toggleClass('active');
        var hidden = $(this).next('input[type="hidden"]');
        if (hidden.length) {
            hidden.prop('disabled', !$(this).hasClass('active'));
        }
    });

    $('.switch[data-switch="ativo"]').on('click', function() {
        $(this).toggleClass('on');
        $(this).next('input[type="hidden"]').val($(this).hasClass('on') ? 1 : 0);
    });

    $('.team-check').on('click', function(e) {
        if ($(e.target).is('input')) return;
        $(this).toggleClass('active');
        $(this).find('input[type="checkbox"]').prop('checked', $(this).hasClass('active'));
    });
});
</script>
@endpush
