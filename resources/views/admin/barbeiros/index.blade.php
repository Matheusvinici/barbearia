@extends('layouts.app')
@section('title', 'Barbeiros')
@section('breadcrumb', 'Barbeiros')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Barbeiros</h5>
        <a href="{{ route('admin.barbeiros.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Novo Barbeiro
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Comissão</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barbeiros as $b)
                <tr>
                    <td>{{ $b->nome }}</td>
                    <td>{{ $b->email }}</td>
                    <td>{{ $b->telefone }}</td>
                    <td>{{ $b->comissao_percentual }}%</td>
                    <td>{!! $b->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.barbeiros.show', $b) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.barbeiros.edit', $b) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.barbeiros.destroy', $b) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($barbeiros->hasPages())
    <div class="card-footer">{{ $barbeiros->links() }}</div>
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
