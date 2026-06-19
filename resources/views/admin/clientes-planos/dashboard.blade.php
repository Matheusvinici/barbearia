@extends('layouts.app')
@section('title', 'Dashboard de Cotas')
@section('breadcrumb', 'Clientes Planos')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <a href="{{ route('admin.clientes-planos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>
</div>

@forelse($dados as $item)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center
        {{ !$item['todas_dentro'] ? 'bg-warning text-dark' : 'bg-success text-white' }}">
        <h5 class="mb-0">
            <i class="fas {{ $item['todas_dentro'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
            {{ $item['cliente']->nome }}
            <small class="{{ $item['todas_dentro'] ? 'text-white' : 'text-dark' }}">- {{ $item['cliente']->telefone }}</small>
        </h5>
        <div>
            <span class="badge bg-light text-dark">{{ $item['plano']->nome }}</span>
            <span class="badge bg-light text-dark ms-1">R$ {{ number_format($item['plano']->valor, 2, ',', '.') }}</span>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Contratada</th>
                    <th>Utilizada</th>
                    <th>Restante</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item['quotas'] as $quota)
                <tr class="{{ !$quota['dentro_da_cota'] ? 'table-danger' : '' }}">
                    <td>{{ $quota['servico'] }}</td>
                    <td>{{ $quota['contratada'] }}</td>
                    <td>{{ $quota['utilizada'] }}</td>
                    <td>{{ $quota['restante'] }}</td>
                    <td>
                        @if($quota['dentro_da_cota'])
                            <span class="badge bg-success">Dentro da cota</span>
                        @else
                            <span class="badge bg-danger">Cota excedida</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <th>Total</th>
                    <th>{{ $item['total_contratada'] }}</th>
                    <th>{{ $item['total_utilizada'] }}</th>
                    <th>{{ $item['total_contratada'] - $item['total_utilizada'] }}</th>
                    <th>
                        @if($item['todas_dentro'])
                            <span class="badge bg-success">Tudo dentro da cota</span>
                        @else
                            <span class="badge bg-danger">Necessário pagar excedente</span>
                        @endif
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="fas fa-shopping-basket fa-3x mb-3"></i>
        <p>Nenhum cliente com plano ativo no momento.</p>
        <a href="{{ route('admin.clientes-planos.index') }}" class="btn btn-primary">Vincular Clientes</a>
    </div>
</div>
@endforelse
@endsection
