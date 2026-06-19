@extends('layouts.app')
@section('title', 'Planos')
@section('breadcrumb', 'Planos')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Planos</h5>
        <a href="{{ route('admin.planos.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Plano</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nome</th><th>Valor</th><th>Clientes</th><th>Ativo</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($planos as $p)
                <tr>
                    <td>{{ $p->nome }}</td>
                    <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                    <td>{{ $p->clientes_count }}</td>
                    <td>{!! $p->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.planos.show', $p) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.planos.edit', $p) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.planos.destroy', $p) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($planos->hasPages())<div class="card-footer">{{ $planos->links() }}</div>@endif
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
