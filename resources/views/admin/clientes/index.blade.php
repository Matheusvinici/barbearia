@extends('layouts.app')
@section('title', 'Clientes')
@section('breadcrumb', 'Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Clientes</h5>
        <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Cliente</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nome</th><th>Telefone</th><th>Email</th><th>Agendamentos</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($clientes as $c)
                <tr>
                    <td>{{ $c->nome }}</td>
                    <td>{{ $c->telefone }}</td>
                    <td>{{ $c->email ?? '-' }}</td>
                    <td>{{ $c->agendamentos_count }}</td>
                    <td>
                        <a href="{{ route('admin.clientes.show', $c) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.clientes.edit', $c) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.clientes.destroy', $c) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($clientes->hasPages())<div class="card-footer">{{ $clientes->links() }}</div>@endif
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
