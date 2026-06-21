@extends('layouts.app')
@section('title', 'Barbearias')
@section('breadcrumb', 'Barbearias')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Barbearias</h5>
        <a href="{{ route('admin.barbearias.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nova Barbearia
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Matriz</th>
                    <th>Filial</th>
                    <th>Bairro</th>
                    <th>Cidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barbearias as $b)
                <tr>
                    <td><strong>{{ $b->nome }}</strong></td>
                    <td>{!! $b->isMatriz() ? '<span class="badge bg-primary">Matriz</span>' : '<span class="badge bg-info">Filial</span>' !!}</td>
                    <td>{{ $b->parent?->nome ?? '-' }}</td>
                    <td>{{ $b->children_count ? $b->children_count . ' filial(is)' : '-' }}</td>
                    <td>{{ $b->bairro }}</td>
                    <td>{{ $b->cidade }}</td>
                    <td>
                        <a href="{{ route('admin.barbearias.edit', $b) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.barbearias.destroy', $b) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($barbearias->hasPages())
    <div class="card-footer">{{ $barbearias->links() }}</div>
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
