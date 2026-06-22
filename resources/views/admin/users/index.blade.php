@extends('layouts.app')
@section('title', 'Usuários')
@section('breadcrumb', 'Usuários')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Usuários</h5>
        @can('barbeiro.create')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Novo Usuário
        </a>
        @endcan
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Papéis</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles as $r)
                        <span class="badge bg-info">{{ ucfirst($r->name) }}</span>
                        @endforeach
                        @if($u->roles->isEmpty())
                        <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.users.destroy', $u) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}
</script>
@endpush
@endsection
