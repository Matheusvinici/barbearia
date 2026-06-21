@extends('layouts.app')
@section('title', 'Caixa')
@section('breadcrumb', 'Financeiro > Caixa')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Abrir Caixa</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.caixa.abrir') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Data</label>
                        <input type="date" name="data" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Saldo Inicial (R$)</label>
                        <input type="number" step="0.01" name="saldo_inicial" class="form-control" value="0" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-cash-register"></i> Abrir Caixa</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Histórico de Caixa</h5></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Data</th><th>Saldo Inicial</th><th>Entradas</th><th>Saídas</th><th>Saldo Final</th><th>Status</th><th>Ações</th></tr></thead>
                    <tbody>
                        @forelse($caixas as $c)
                        <tr>
                            <td>{{ $c->data->format('d/m/Y') }}</td>
                            <td>R$ {{ number_format($c->saldo_inicial, 2, ',', '.') }}</td>
                            <td class="text-success">R$ {{ number_format($c->total_entradas, 2, ',', '.') }}</td>
                            <td class="text-danger">R$ {{ number_format($c->total_saidas, 2, ',', '.') }}</td>
                            <td><strong>R$ {{ number_format($c->saldo_final, 2, ',', '.') }}</strong></td>
                            <td>{!! $c->fechado ? '<span class="badge bg-secondary">Fechado</span>' : '<span class="badge bg-success">Aberto</span>' !!}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.caixa.show', $c) }}" class="btn btn-sm btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.caixa.edit', $c) }}" class="btn btn-sm btn-primary" title="Editar valores"><i class="fas fa-edit"></i></a>
                                    @if(!$c->fechado)
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalFechar{{ $c->id }}" title="Fechar"><i class="fas fa-lock"></i></button>
                                    @else
                                    <form action="{{ route('admin.caixa.reabrir', $c) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-info" title="Reabrir" onclick="return confirm('Reabrir caixa?')"><i class="fas fa-unlock"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if(!$c->fechado)
                        <div class="modal fade" id="modalFechar{{ $c->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.caixa.fechar', $c) }}" method="POST">
                                        @csrf
                                        <div class="modal-header"><h5>Fechar Caixa - {{ $c->data->format('d/m/Y') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Saldo Calculado (inicial + entradas - saídas)</label>
                                                <input type="text" class="form-control" value="R$ {{ number_format($c->saldo_inicial + $c->total_entradas - $c->total_saidas, 2, ',', '.') }}" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label>Saldo Informado (R$)</label>
                                                <input type="number" step="0.01" name="saldo_informado" class="form-control" value="{{ $c->saldo_inicial + $c->total_entradas - $c->total_saidas }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Observações</label>
                                                <textarea name="observacoes" class="form-control" rows="2"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-warning">Fechar Caixa</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Nenhum caixa registrado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($caixas->hasPages())<div class="card-footer">{{ $caixas->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection
