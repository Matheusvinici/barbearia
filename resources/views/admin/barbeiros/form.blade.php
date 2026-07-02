@extends('layouts.app')

@section('title', $edit ? 'Editar Profissional' : 'Novo Profissional')

@php
    $__tenantSlug = request()->route('barbearia')?->slug;
    function barbeiroRoute($name, $params = []) {
        $slug = request()->route('barbearia')?->slug;
        if (!$slug) return route('admin.' . $name, $params);
        $params = is_array($params) ? $params : [$params];
        return route('tenant.admin.' . $name, array_merge([$slug], $params));
    }
@endphp

@section('breadcrumb')
<svg class="icon icon-sm"><use href="#i-home"/></svg>
<span class="sep">/</span>
<a href="{{ barbeiroRoute('barbeiros.index') }}" style="color: inherit; text-decoration: none;">Profissionais</a>
<span class="sep">/</span>
<span class="current">{{ $edit ? 'Editar' : 'Novo' }} Profissional</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $edit ? 'Editando dados do profissional' : 'Adicionando membro à equipe' }}</span>
<span class="pipe">·</span>
<span>Campos com * são obrigatórios</span>
@endsection

@section('topbar-actions')
<a href="{{ barbeiroRoute('barbeiros.index') }}" class="btn-ghost-c">
    <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
    Voltar
</a>
@endsection

@section('content')
<form action="{{ $edit ? barbeiroRoute('barbeiros.update', $barbeiro) : barbeiroRoute('barbeiros.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($edit) @method('PUT') @endif

    <div class="main-grid">
        <div class="col-stack">

            <div class="panel fade-in d1">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-user-id"/></svg></div>
                        <div>
                            <h2 class="panel-title">Dados Pessoais</h2>
                            <div class="panel-subtitle">Informações de contato e identificação</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        @if($users->isNotEmpty())
                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label">Vincular Usuário Existente</label>
                            <select name="user_id" class="form-input" id="selectUser" onchange="onSelectUser(this)">
                                <option value="">— Selecionar usuário —</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}" data-nome="{{ $u->name }}" data-email="{{ $u->email }}" {{ old('user_id', $edit ? $barbeiro->user_id : '') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            <div style="margin-top:6px;font-size:12px;color:var(--text-faint);">Ao vincular, nome e e-mail serão preenchidos automaticamente. Útil para proprietário que também atende como barbeiro.</div>
                        </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" name="nome" class="form-input" value="{{ old('nome', $edit ? $barbeiro->nome : '') }}" placeholder="Ex: Carlos Eduardo Souza" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">E-mail *</label>
                            <input type="email" name="email" class="form-input" value="{{ old('email', $edit ? $barbeiro->email : '') }}" placeholder="email@studiobarber.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telefone / WhatsApp</label>
                            <input type="text" name="telefone" class="form-input" value="{{ old('telefone', $edit ? $barbeiro->telefone : '') }}" placeholder="(00) 00000-0000">
                        </div>
                        @if(!$edit)
                        <div class="form-group">
                            <label class="form-label">Senha *</label>
                            <input type="password" name="password" class="form-input" placeholder="Mínimo 8 caracteres" required>
                        </div>
                        @else
                        <div class="form-group">
                            <label class="form-label">Nova Senha</label>
                            <input type="password" name="password" class="form-input" placeholder="Deixe em branco para manter a atual">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="panel fade-in d2">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-briefcase"/></svg></div>
                        <div>
                            <h2 class="panel-title">Informações Profissionais</h2>
                            <div class="panel-subtitle">Tipo, permissões e comissão</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-input" id="selectTipo" onchange="toggleTipo()">
                                <option value="funcionario" {{ old('tipo', $edit ? $barbeiro->tipo : 'funcionario') == 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                                <option value="proprietario" {{ old('tipo', $edit ? $barbeiro->tipo : '') == 'proprietario' ? 'selected' : '' }}>Proprietário</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Comissão (%) *</label>
                            <input type="number" step="0.01" name="comissao_percentual" class="form-input" value="{{ old('comissao_percentual', $edit ? $barbeiro->comissao_percentual : 50) }}" required>
                        </div>
                        <div class="form-group" id="barbeariasContainer">
                            <label class="form-label">Unidades Vinculadas</label>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;padding:12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--bg);">
                                @php $selectedIds = old('barbearias', $edit ? $barbeiro->barbearias->pluck('id')->toArray() : []); @endphp
                                @foreach($barbearias as $b)
                                <label style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:var(--bg-elevated);border-radius:8px;cursor:pointer;border:1px solid {{ in_array($b->id, $selectedIds) ? 'var(--accent)' : 'var(--border)' }};font-size:13px;font-weight:500;" class="{{ in_array($b->id, $selectedIds) ? 'active' : '' }}">
                                    <input type="checkbox" name="barbearias[]" value="{{ $b->id }}" {{ in_array($b->id, $selectedIds) ? 'checked' : '' }} style="accent-color:var(--accent);">
                                    {{ $b->nome }}
                                </label>
                                @endforeach
                            </div>
                            <div style="margin-top:6px;font-size:12px;color:var(--text-faint);">Selecione uma ou mais unidades onde este profissional atuará.</div>
                        </div>
                        <div class="form-group" id="criarAdminContainer" style="{{ old('tipo', $edit ? $barbeiro->tipo : 'funcionario') == 'proprietario' ? '' : 'display:none' }}">
                            <div class="toggle-row">
                                <div class="toggle-info">
                                    <div class="t">Criar como Admin</div>
                                    <div class="d">Cria uma conta de usuário admin com papel de proprietário para acesso ao painel web.</div>
                                </div>
                                <button type="button" class="switch {{ old('criar_como_admin', $edit ? ($userExists ?? false) : false) ? 'on' : '' }}" data-target="criar_como_admin" onclick="this.classList.toggle('on'); document.getElementById('input-criar-admin').value = this.classList.contains('on') ? '1' : '0';"></button>
                                <input type="hidden" name="criar_como_admin" id="input-criar-admin" value="{{ old('criar_como_admin', $edit ? ($userExists ?? false) : false) ? '1' : '0' }}">
                            </div>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <div class="toggle-row">
                                <div class="toggle-info">
                                    <div class="t">Profissional Ativo</div>
                                    <div class="d">Pode receber agendamentos e acessar o sistema.</div>
                                </div>
                                <button type="button" class="switch {{ old('ativo', $edit ? $barbeiro->ativo : true) ? 'on' : '' }}" data-target="ativo" onclick="this.classList.toggle('on'); document.getElementById('input-ativo').value = this.classList.contains('on') ? '1' : '0';"></button>
                                <input type="hidden" name="ativo" id="input-ativo" value="{{ old('ativo', $edit ? ($barbeiro->ativo ? '1' : '0') : '1') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel fade-in d3">
                <div class="panel-header">
                    <div class="panel-title-wrap">
                        <div class="panel-title-icon"><svg class="icon"><use href="#i-clock"/></svg></div>
                        <div>
                            <h2 class="panel-title">Escala de Trabalho</h2>
                            <div class="panel-subtitle">Horários de atendimento por turno</div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    @php
                        $diasSemanaInt = [0,1,2,3,4,5,6];
                        $diasSemanaLabel = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
                        $periodos = [
                            'manha' => ['label'=>'Manhã', 'default_in'=>'08:00', 'default_out'=>'12:00'],
                            'tarde' => ['label'=>'Tarde', 'default_in'=>'13:00', 'default_out'=>'18:00'],
                            'noite' => ['label'=>'Noite', 'default_in'=>'18:00', 'default_out'=>'22:00'],
                        ];
                        $horariosAgrupados = [];
                        if ($edit && $barbeiro->horarios->count()) {
                            foreach ($barbeiro->horarios as $h) {
                                $horariosAgrupados[$h->dia_semana][$h->periodo] = $h;
                            }
                        }
                        $hi = 0;
                    @endphp
                    <div class="schedule-grid" id="scheduleGrid">
                        @foreach($diasSemanaInt as $diaIdx)
                        @php
                            $diaHorarios = $horariosAgrupados[$diaIdx] ?? [];
                        @endphp
                        <div class="schedule-row" data-day="{{ $diasSemanaLabel[$diaIdx] }}">
                            <div class="schedule-day">
                                <span>{{ $diasSemanaLabel[$diaIdx] }}</span>
                                <div class="toggle {{ count($diaHorarios) ? 'on' : '' }}" onclick="toggleDia(this, {{ $diaIdx }})"></div>
                            </div>
                            <div class="schedule-periods" style="display:contents;">
                                @foreach($periodos as $periKey => $periCfg)
                                @php
                                    $h = $diaHorarios[$periKey] ?? null;
                                    $hasVal = $h && $h->hora_inicio && $h->hora_fim;
                                    $valIn = $hasVal ? $h->hora_inicio : '';
                                    $valOut = $hasVal ? $h->hora_fim : '';
                                @endphp
                                <div class="period-group" style="display:flex;align-items:center;gap:6px;">
                                    <span style="font-size:11px;font-weight:600;color:var(--text-faint);min-width:38px;">{{ $periCfg['label'] }}</span>
                                    <input type="time" name="horarios[{{ $hi }}][hora_inicio]" class="form-input h-in" style="height:36px;width:100px;padding:0 8px;font-size:13px;" value="{{ $valIn }}" {{ !count($diaHorarios) ? 'disabled' : '' }}>
                                    <span style="color:var(--text-faint);font-size:12px;">—</span>
                                    <input type="time" name="horarios[{{ $hi }}][hora_fim]" class="form-input h-out" style="height:36px;width:100px;padding:0 8px;font-size:13px;" value="{{ $valOut }}" {{ !count($diaHorarios) ? 'disabled' : '' }}>
                                    <input type="hidden" name="horarios[{{ $hi }}][dia_semana]" value="{{ $diaIdx }}">
                                    <input type="hidden" name="horarios[{{ $hi }}][periodo]" value="{{ $periKey }}">
                                    <button type="button" class="period-remove" onclick="removerTurno(this)" title="Remover turno">×</button>
                                </div>
                                @php $hi++; @endphp
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <div class="action-card">
            <div class="action-buttons fade-in d2">
                <button type="submit" class="btn-primary-c">
                    <svg class="icon icon-sm"><use href="#i-check"/></svg>
                    {{ $edit ? 'Atualizar Profissional' : 'Salvar Profissional' }}
                </button>
                <a href="{{ barbeiroRoute('barbeiros.index') }}" class="btn-ghost-c">
                    <svg class="icon icon-sm"><use href="#i-x"/></svg>
                    Cancelar
                </a>
            </div>
            <div class="panel fade-in d3" style="border: none;">
                <div class="panel-body" style="padding: 0;">
                    <div class="tips-list">
                        <div class="tip-item">
                            <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-shield"/></svg></div>
                            <div class="tip-info">
                                <div class="t">Senha segura</div>
                                <div class="d">Use senhas com 8+ caracteres, misturando letras e números.</div>
                            </div>
                        </div>
                        <div class="tip-item">
                            <div class="tip-ic"><svg class="icon icon-sm"><use href="#i-trend-up"/></svg></div>
                            <div class="tip-info">
                                <div class="t">Comissão competitiva</div>
                                <div class="d">A média de mercado é 40-50% para barbeiros.</div>
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
function onSelectUser(el) {
    var opt = el.options[el.selectedIndex];
    var nome = opt.getAttribute('data-nome');
    var email = opt.getAttribute('data-email');
    var nomeInput = document.querySelector('input[name="nome"]');
    var emailInput = document.querySelector('input[name="email"]');
    if (opt.value) {
        nomeInput.value = nome || '';
        emailInput.value = email || '';
        nomeInput.setAttribute('readonly', true);
        emailInput.setAttribute('readonly', true);
        nomeInput.style.opacity = '0.7';
        emailInput.style.opacity = '0.7';
    } else {
        nomeInput.removeAttribute('readonly');
        emailInput.removeAttribute('readonly');
        nomeInput.style.opacity = '';
        emailInput.style.opacity = '';
    }
}

function toggleTipo() {
    var val = document.getElementById('selectTipo').value;
    document.getElementById('criarAdminContainer').style.display = val === 'proprietario' ? '' : 'none';
}

function removerTurno(btn) {
    var group = btn.closest('.period-group');
    var hIn = group.querySelector('.h-in');
    var hOut = group.querySelector('.h-out');
    hIn.value = '';
    hOut.value = '';
    hIn.disabled = true;
    hOut.disabled = true;
    group.classList.add('removed');
}

function toggleDia(el, diaIdx) {
    el.classList.toggle('on');
    var row = el.closest('.schedule-row');
    var ativo = el.classList.contains('on');
    var defaults = { manha: ['08:00', '12:00'], tarde: ['13:00', '18:00'], noite: ['18:00', '22:00'] };
    var periodNames = ['manha', 'tarde', 'noite'];
    var inputs = row.querySelectorAll('.period-group');
    inputs.forEach(function(group, idx) {
        var hIn = group.querySelector('.h-in');
        var hOut = group.querySelector('.h-out');
        group.classList.remove('removed');
        hIn.disabled = !ativo;
        hOut.disabled = !ativo;
        if (ativo && !hIn.value && idx < periodNames.length) {
            hIn.value = defaults[periodNames[idx]][0];
            hOut.value = defaults[periodNames[idx]][1];
        }
    });
}
</script>
@endpush