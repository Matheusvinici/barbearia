@extends('layouts.app')
@section('title', 'Vínculo de Planos')
@section('breadcrumb', 'Clientes Planos')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.clientes-planos.dashboard') }}" class="btn btn-info"><i class="fas fa-chart-bar"></i> Dashboard de Cotas</a>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vincular Cliente a Plano</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.clientes-planos.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Cliente</label>
                    <select name="cliente_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}">{{ $c->nome }} - {{ $c->telefone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Plano</label>
                    <select name="plano_id" class="form-control" required>
                        <option value="">Selecione...</option>
                        @foreach($planos as $p)
                        <option value="{{ $p->id }}">{{ $p->nome }} - R$ {{ number_format($p->valor, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Data Fim</label>
                    <input type="date" name="data_fim" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00">
                </div>
                <div class="col-md-7 mb-3">
                    <label>Observações</label>
                    <textarea name="observacoes" class="form-control" rows="1"></textarea>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-link"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vínculos Atuais</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Cliente</th><th>Telefone</th><th>CPF</th><th>Plano</th><th>Início</th><th>Fim</th><th>Ativo</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($vinculos as $v)
                <tr>
                    <td>{{ $v->cliente->nome }}</td>
                    <td>{{ $v->cliente->telefone }}</td>
                    <td>{{ $v->cpf ?? '-' }}</td>
                    <td>{{ $v->plano->nome }}</td>
                    <td>{{ $v->data_inicio->format('d/m/Y') }}</td>
                    <td>{{ $v->data_fim ? $v->data_fim->format('d/m/Y') : '-' }}</td>
                    <td>{!! $v->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.clientes-planos.edit', $v) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.clientes-planos.destroy', $v) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($vinculos->hasPages())<div class="card-footer">{{ $vinculos->links() }}</div>@endif
</div>
@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({ title: 'Confirmar exclusão?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sim, excluir!' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
</script>
@endpush
@endsection
