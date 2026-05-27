@extends('layouts.app')
@section('title', 'Meus Agendamentos')
@section('breadcrumb', 'Agendamentos')

@section('content')
<div class="card">
    <div class="card-header"><h5>Todos os Meus Agendamentos</h5></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Data</th><th>Hora</th><th>Cliente</th><th>Serviços</th><th>Status</th><th>Valor</th><th>Ações</th></tr></thead>
            <tbody>
                @forelse($agendamentos as $ag)
                <tr>
                    <td>{{ $ag->data->format('d/m/Y') }}</td>
                    <td>{{ substr($ag->hora_inicio, 0, 5) }}</td>
                    <td>{{ $ag->cliente->nome }}<br><small>{{ $ag->cliente->telefone }}</small></td>
                    <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                    <td><span class="badge-status status-{{ $ag->status }}">{{ ucfirst($ag->status) }}</span></td>
                    <td>R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
                    <td>
                        @if($ag->status == 'pendente')
                        <form action="{{ route('barbeiro.agendamentos.confirmar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="btn btn-sm btn-success" title="Confirmar"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                        @if(in_array($ag->status, ['pendente', 'confirmado']))
                        <form action="{{ route('barbeiro.agendamentos.realizar', $ag) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button class="btn btn-sm btn-primary" title="Realizar"><i class="fas fa-cut"></i></button>
                        </form>
                        <form action="{{ route('barbeiro.agendamentos.cancelar', $ag) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancelar agendamento?')">
                            @csrf @method('PUT')
                            <button class="btn btn-sm btn-danger" title="Cancelar"><i class="fas fa-times"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Nenhum agendamento encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($agendamentos->hasPages())<div class="card-footer">{{ $agendamentos->links() }}</div>@endif
</div>
@endsection
