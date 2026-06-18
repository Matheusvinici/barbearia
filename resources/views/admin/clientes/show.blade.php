@extends('layouts.app')
@section('title', $cliente->nome)
@section('breadcrumb', 'Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>{{ $cliente->nome }}</h5>
        <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th>Nome:</th><td>{{ $cliente->nome }}</td></tr>
            <tr><th>Telefone:</th><td>{{ $cliente->telefone }}</td></tr>
            <tr><th>Email:</th><td>{{ $cliente->email ?? '-' }}</td></tr>
            <tr><th>Observações:</th><td>{{ $cliente->observacoes ?? '-' }}</td></tr>
        </table>
        <hr>
        <h6>Histórico de Agendamentos</h6>
        <table class="table table-sm">
            <thead><tr><th>Data</th><th>Hora</th><th>Barbeiro</th><th>Serviços</th><th>Status</th><th>Valor</th></tr></thead>
            <tbody>
                @foreach($cliente->agendamentos as $ag)
                <tr>
                    <td>{{ $ag->data->format('d/m/Y') }}</td>
                    <td>{{ $ag->hora_inicio->format('H:i') }}</td>
                    <td>{{ $ag->barbeiro->nome }}</td>
                    <td>{{ $ag->servicos->pluck('nome')->implode(', ') }}</td>
                    <td><span class="badge-status status-{{ $ag->status }}">{{ ucfirst($ag->status) }}</span></td>
                    <td>R$ {{ number_format($ag->total ?? 0, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
