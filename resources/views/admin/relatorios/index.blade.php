@extends('layouts.app')
@section('title', 'Relatórios')
@section('breadcrumb', 'Financeiro > Relatórios')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Relatório de Faturamento</h5></div>
            <div class="card-body">
                <p>Veja o faturamento por período, por barbeiro, lucro líquido.</p>
                <a href="{{ route('admin.relatorios.faturamento') }}" class="btn btn-primary"><i class="fas fa-chart-line"></i> Acessar</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h5>Relatório de Serviços</h5></div>
            <div class="card-body">
                <p>Veja quais serviços foram mais realizados em um período.</p>
                <a href="{{ route('admin.relatorios.servicos') }}" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Acessar</a>
            </div>
        </div>
    </div>
</div>
@endsection
