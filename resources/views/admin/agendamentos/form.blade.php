@extends('layouts.app')
@section('title', 'Editar Agendamento')
@section('breadcrumb', 'Agendamentos')

@section('content')
<div class="card">
    <div class="card-header"><h5>Editar Agendamento #{{ $agendamento->id }}</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.agendamentos.update', $agendamento) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Cliente</label>
                    <input type="text" class="form-control" value="{{ $agendamento->cliente->nome }} - {{ $agendamento->cliente->telefone }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Barbeiro</label>
                    <select name="barbeiro_id" class="form-control" required>
                        @foreach($barbeiros as $b)
                        <option value="{{ $b->id }}" {{ $agendamento->barbeiro_id == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Data</label>
                    <input type="date" name="data" class="form-control" value="{{ $agendamento->data->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Horário Início</label>
                    <input type="time" name="hora_inicio" class="form-control" value="{{ \Carbon\Carbon::parse($agendamento->hora_inicio)->format('H:i') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control" required>
                        <option value="pendente" {{ $agendamento->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="confirmado" {{ $agendamento->status == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                        <option value="realizado" {{ $agendamento->status == 'realizado' ? 'selected' : '' }}>Realizado</option>
                        <option value="cancelado" {{ $agendamento->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        <option value="ausente" {{ $agendamento->status == 'ausente' ? 'selected' : '' }}>Ausente</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Serviços</label>
                    <div class="border rounded p-2" style="max-height:150px;overflow-y:auto">
                        @foreach($servicos as $s)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="servico_ids[]"
                                   value="{{ $s->id }}" id="servico{{ $s->id }}"
                                   {{ $agendamento->servicos->contains($s->id) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="servico{{ $s->id }}">
                                {{ $s->nome }} - R$ {{ number_format($s->preco, 2, ',', '.') }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ $agendamento->observacoes }}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection
