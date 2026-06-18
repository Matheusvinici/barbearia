@extends('layouts.app')
@section('title', 'Agendamentos')
@section('breadcrumb', 'Agendamentos')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <label class="mb-0">Data:</label>
                <input type="date" name="data" class="form-control form-control-sm" value="{{ $data }}" style="width:auto">
                <label class="mb-0">Barbeiro:</label>
                <select name="barbeiro_id" class="form-control form-control-sm" style="width:auto">
                    <option value="">Todos</option>
                    @foreach($barbeiros as $b)
                    <option value="{{ $b->id }}" {{ $barbeiroId == $b->id ? 'selected' : '' }}>{{ $b->nome }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-info"><i class="fas fa-search"></i></button>
            </form>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoAgendamento">
                <i class="fas fa-plus"></i> Novo Agendamento
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Hora</th><th>Cliente</th><th>Barbeiro</th><th>Serviços</th><th>Status</th><th>Valor</th><th>Ações</th></tr>
            </thead>
            <tbody>
                @forelse($agendamentos as $ag)
                <tr>
                    <td>{{ $ag->hora_inicio->format('H:i') }}</td>
                    <td>{{ $ag->cliente->nome }}<br><small class="text-muted">{{ $ag->cliente->telefone }}</small></td>
                    <td>{{ $ag->barbeiro->nome }}</td>
                    <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                    <td><span class="badge-status status-{{ $ag->status }}">{{ ucfirst($ag->status) }}</span></td>
                    <td>R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('admin.agendamentos.show', $ag) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.agendamentos.edit', $ag) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.agendamentos.destroy', $ag) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum agendamento para esta data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalNovoAgendamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.agendamentos.store') }}" method="POST" id="formAgendamento">
                @csrf
                <input type="hidden" name="data" value="{{ $data }}">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Cliente</label>
                            <select name="cliente_id" class="form-control select2" required>
                                <option value="">Selecione ou busque...</option>
                                @foreach(App\Models\Cliente::all() as $c)
                                <option value="{{ $c->id }}">{{ $c->nome }} - {{ $c->telefone }}</option>
                                @endforeach
                            </select>
                            <small><a href="{{ route('admin.clientes.create') }}" target="_blank">+ Novo Cliente</a></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Barbeiro</label>
                            <select name="barbeiro_id" class="form-control" id="barbeiroSelect" required>
                                <option value="">Selecione...</option>
                                @foreach($barbeiros as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Horário</label>
                            <select name="hora_inicio" class="form-control" id="horarioSelect" required>
                                <option value="">Selecione barbeiro e data primeiro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Serviços</label>
                            <select name="servico_ids[]" class="form-control" multiple required size="4">
                                @foreach($servicos as $s)
                                <option value="{{ $s->id }}" data-minutos="{{ $s->duracao_minutos }}">{{ $s->nome }} - R$ {{ number_format($s->preco, 2, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Observações</label>
                            <textarea name="observacoes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({ title: 'Confirmar exclusão?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sim, excluir!' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}

$('#barbeiroSelect').change(function() {
    const barbeiroId = $(this).val();
    const data = $('input[name="data"]').val();
    if (barbeiroId && data) {
        $.get('{{ route("admin.agendamentos.horarios") }}', { barbeiro_id: barbeiroId, data: data }, function(res) {
            const select = $('#horarioSelect');
            select.html('<option value="">Selecione...</option>');
            res.forEach(function(h) { select.append(`<option value="${h}">${h}</option>`); });
        });
    }
});
</script>
@endpush
@endsection
