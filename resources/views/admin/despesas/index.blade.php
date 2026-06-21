@extends('layouts.app')
@section('title', 'Despesas')
@section('breadcrumb', 'Financeiro > Despesas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Despesas</h5>
        <a href="{{ route('admin.despesas.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova Despesa</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Descrição</th><th>Barbearia</th><th>Valor</th><th>Vencimento</th><th>Pagamento</th><th>Categoria</th><th>Pago</th><th>Ações</th></tr></thead>
            <tbody>
                @foreach($despesas as $d)
                <tr class="{{ !$d->pago && $d->data_vencimento < now() ? 'table-danger' : '' }}">
                    <td>{{ $d->descricao }}</td>
                    <td>{{ $d->barbearia?->nome ?? 'Geral' }}</td>
                    <td>R$ {{ number_format($d->valor, 2, ',', '.') }}</td>
                    <td>{{ $d->data_vencimento->format('d/m/Y') }}</td>
                    <td>{{ $d->data_pagamento ? $d->data_pagamento->format('d/m/Y') : '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $d->categoria }}</span></td>
                    <td>
                        <button onclick="togglePago({{ $d->id }})" class="btn btn-sm btn-{{ $d->pago ? 'success' : 'warning' }}">
                            {{ $d->pago ? 'Pago' : 'Pendente' }}
                        </button>
                    </td>
                    <td>
                        <a href="{{ route('admin.despesas.edit', $d) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ route('admin.despesas.destroy', $d) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($despesas->hasPages())<div class="card-footer">{{ $despesas->links() }}</div>@endif
</div>

@push('scripts')
<script>
function togglePago(id) {
    $.ajax({
        url: '{{ route("admin.despesas.toggle-pago", "") }}/' + id,
        method: 'PATCH',
        data: { _token: '{{ csrf_token() }}' },
        success: () => location.reload()
    });
}
function confirmarExclusao(url) {
    Swal.fire({ title: 'Excluir despesa?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Excluir' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
</script>
@endpush
@endsection
