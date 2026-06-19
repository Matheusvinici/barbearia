@extends('layouts.app')
@section('title', 'Agendamentos')
@section('breadcrumb', 'Agendamentos')

@section('content')

@livewire('agendamentos-table')

<div class="modal fade" id="modalNovoAgendamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.agendamentos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="data" value="{{ request('data', now()->format('Y-m-d')) }}">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Cliente</label>
                            @livewire('admin.buscar-cliente')
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Barbeiro</label>
                            <select name="barbeiro_id" class="form-control" id="barbeiroSelect" required>
                                <option value="">Selecione...</option>
                                @foreach(App\Models\Barbeiro::where('ativo', true)->get() as $b)
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
                            <div class="border rounded p-2" style="max-height:150px;overflow-y:auto">
                                @foreach(App\Models\Servico::where('ativo', true)->get() as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servico_ids[]"
                                           value="{{ $s->id }}" id="servico{{ $s->id }}">
                                    <label class="form-check-label small" for="servico{{ $s->id }}">
                                        {{ $s->nome }} - R$ {{ number_format($s->preco, 2, ',', '.') }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
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