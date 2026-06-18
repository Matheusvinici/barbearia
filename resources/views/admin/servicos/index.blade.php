@extends('layouts.app')
@section('title', 'Serviços')
@section('breadcrumb', 'Serviços')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Serviços</h5>
        <a href="{{ route('admin.servicos.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Novo Serviço</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Foto</th><th>Nome</th><th>Preço</th><th>Duração</th><th>Ativo</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($servicos as $s)
                <tr>
                    <td>
                        @if($s->foto)
                            <img src="{{ $s->foto_url }}" alt="{{ $s->nome }}" style="width:50px;height:50px;object-fit:cover;border-radius:6px">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $s->nome }}</td>
                    <td>R$ {{ number_format($s->preco, 2, ',', '.') }}</td>
                    <td>{{ $s->duracao_minutos }} min</td>
                    <td>{!! $s->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.servicos.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.servicos.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.servicos.destroy', $s) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($servicos->hasPages())<div class="card-footer">{{ $servicos->links() }}</div>@endif
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