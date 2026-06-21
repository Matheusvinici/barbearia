@extends('layouts.app')
@section('title', $edit ? 'Editar Barbeiro' : 'Novo Barbeiro')
@section('breadcrumb', $edit ? 'Editar Barbeiro' : 'Novo Barbeiro')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $edit ? 'Editar Barbeiro' : 'Novo Barbeiro' }}</h5></div>
    <div class="card-body">
        <form action="{{ $edit ? route('admin.barbeiros.update', $barbeiro) : route('admin.barbeiros.store') }}" method="POST">
            @csrf
            @if($edit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control" value="{{ old('nome', $edit ? $barbeiro->nome : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $edit ? $barbeiro->email : '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Senha{{ $edit ? ' (deixe em branco para manter)' : '' }}</label>
                    <input type="password" name="password" class="form-control" {{ $edit ? '' : 'required' }}>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $edit ? $barbeiro->telefone : '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Barbearia</label>
                    <select name="barbearia_id" class="form-control">
                        <option value="">Selecione...</option>
                        @foreach($barbearias as $b)
                        <option value="{{ $b->id }}" {{ old('barbearia_id', $edit ? $barbeiro->barbearia_id : '') == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Comissão (%)</label>
                    <input type="number" step="0.01" name="comissao_percentual" class="form-control" value="{{ old('comissao_percentual', $edit ? $barbeiro->comissao_percentual : 50) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Ativo</label>
                    <select name="ativo" class="form-control">
                        <option value="1" {{ $edit && $barbeiro->ativo ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $edit && !$barbeiro->ativo ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Papéis</label>
                    <div class="border rounded p-2">
                        @forelse($roles as $r)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}"
                                   id="role{{ $r->id }}"
                                   {{ $edit && $barbeiro->hasRole($r->name) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role{{ $r->id }}">{{ ucfirst($r->name) }}</label>
                        </div>
                        @empty
                        <small class="text-muted">Nenhum papel disponível. Execute: php artisan db:seed --class=PermissionSeeder</small>
                        @endforelse
                    </div>
                </div>
            </div>

            <hr>
            <h5>Horários de Atendimento por Período</h5>
            <p class="text-muted small">Preencha os horários para cada período do dia. Deixe vazio quando não trabalhar no período.</p>

            @php
                $periodos = [
                    'manha' => 'Manhã',
                    'tarde' => 'Tarde',
                    'noite' => 'Noite',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            @foreach($periodos as $pk => $pl)
                            <th colspan="2" class="text-center">{{ $pl }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th></th>
                            @foreach($periodos as $pk => $pl)
                            <th class="text-center small">Início</th>
                            <th class="text-center small">Fim</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diasSemana as $key => $label)
                        <tr>
                            <td><strong>{{ $label }}</strong></td>
                            @foreach($periodos as $pk => $pl)
                                @php
                                    $horario = $edit ? $barbeiro->horarios->where('dia_semana', $key)->where('periodo', $pk)->first() : null;
                                    $hi = old("horarios.{$key}_{$pk}.hora_inicio", $horario ? $horario->hora_inicio : '');
                                    $hf = old("horarios.{$key}_{$pk}.hora_fim", $horario ? $horario->hora_fim : '');
                                @endphp
                            <td>
                                <input type="time" name="horarios[{{ $key }}_{{ $pk }}][hora_inicio]" class="form-control form-control-sm" value="{{ $hi }}">
                                <input type="hidden" name="horarios[{{ $key }}_{{ $pk }}][dia_semana]" value="{{ $key }}">
                                <input type="hidden" name="horarios[{{ $key }}_{{ $pk }}][periodo]" value="{{ $pk }}">
                            </td>
                            <td>
                                <input type="time" name="horarios[{{ $key }}_{{ $pk }}][hora_fim]" class="form-control form-control-sm" value="{{ $hf }}">
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">{{ $edit ? 'Atualizar' : 'Salvar' }}</button>
            <a href="{{ route('admin.barbeiros.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
