@extends('layouts.app')
@section('title', 'Bloqueio de Agenda')
@section('breadcrumb', 'Bloqueio de Agenda')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Novo Bloqueio</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.bloqueios.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Barbeiro</label>
                        <select name="barbeiro_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            @foreach($barbeiros as $b)
                            <option value="{{ $b->id }}">{{ $b->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Data</label>
                        <input type="date" name="data" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label>De</label>
                            <input type="time" name="hora_inicio" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label>Até</label>
                            <input type="time" name="hora_fim" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Motivo</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ex: Almoço, Folga...">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="recorrente" class="form-check-input" value="1" id="recorrente">
                        <label class="form-check-label" for="recorrente">Repetir semanalmente</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar Bloqueio</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Bloqueios Ativos</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Data</th><th>Barbeiro</th><th>Horário</th><th>Motivo</th><th>Recorrente</th><th>Ações</th></tr></thead>
                    <tbody>
                        @forelse($bloqueios as $bl)
                        <tr>
                            <td>{{ $bl->data->format('d/m/Y') }}</td>
                            <td>{{ $bl->barbeiro->nome }}</td>
                            <td>{{ substr($bl->hora_inicio, 0, 5) }} - {{ substr($bl->hora_fim, 0, 5) }}</td>
                            <td>{{ $bl->motivo ?? '-' }}</td>
                            <td>{!! $bl->recorrente ? '<span class="badge bg-info">Sim</span>' : '<span class="badge bg-secondary">Não</span>' !!}</td>
                            <td><button onclick="confirmarExclusao('{{ route('admin.bloqueios.destroy', $bl) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Nenhum bloqueio cadastrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bloqueios->hasPages())<div class="card-footer">{{ $bloqueios->links() }}</div>@endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({ title: 'Remover bloqueio?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Remover' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
</script>
@endpush
@endsection
