@extends('layouts.app')
@section('title', 'Vínculo de Planos')
@section('breadcrumb', 'Clientes Planos')

@php
$__tenantLive = request()->route('barbearia');
$slug = $__tenantLive?->slug;
$storeRoute = $slug ? route('tenant.admin.clientes-planos.store', $slug) : route('admin.clientes-planos.store');
$dashRoute = $slug ? route('tenant.admin.clientes-planos.dashboard', $slug) : route('admin.clientes-planos.dashboard');
$livewireBarbeariaId = $__tenantLive?->id;
$livewireTenantIds = $__tenantLive?->tenantTreeIds() ?? [];
@endphp

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex gap-2">
        <a href="{{ $dashRoute }}" class="btn btn-info"><i class="fas fa-chart-bar"></i> Dashboard de Cotas</a>
        @if($slug)
        <a href="{{ route('admin.clientes-planos.index') }}" class="btn btn-outline-secondary">Ver Global</a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vincular Cliente a Plano</h5>
        <small class="text-muted">Defina cota, validade e pagamento — pagamento entra no Caixa automaticamente</small>
    </div>
    <div class="card-body">
        <form action="{{ $storeRoute }}" method="POST" id="formVincular">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Cliente * <span class="text-muted" style="font-weight:normal">(busque por nome ou telefone)</span></label>
                    @livewire('admin.buscar-cliente', ['barbearia_id' => $livewireBarbeariaId, 'tenantIds' => $livewireTenantIds])
                    @error('cliente_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label>Plano * <span class="text-muted" style="font-weight:normal">(busque por nome)</span></label>
                    @livewire('admin.buscar-plano')
                    @error('plano_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
                    <small id="planoQuotasHelp" class="text-muted"></small>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Data Início *</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Validade (Vencimento)</label>
                    <input type="date" name="vencimento" class="form-control" placeholder="Expira em...">
                    <small class="text-muted">Campo Fim = Vencimento</small>
                    <input type="hidden" name="data_fim" id="data_fim_hidden">
                </div>
            </div>
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label>Valor Pago</label>
                    <input type="number" step="0.01" name="valor_pago" id="valorPago" class="form-control" placeholder="0,00">
                </div>
                <div class="col-md-2 mb-3">
                    <label>Forma Pgto</label>
                    <select name="forma_pagamento" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Pix">Pix</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="Boleto">Boleto</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input type="checkbox" name="pago" value="1" id="pagoCheck" class="form-check-input">
                        <label for="pagoCheck" class="form-check-label fw-bold">Pago? (lança no Caixa)</label>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Observações</label>
                    <input type="text" name="observacoes" class="form-control" placeholder="Obs...">
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-2">
                    <div id="cotasPreview" class="alert alert-light border small" style="display:none"></div>
                </div>
                <div class="col-md-2 ms-auto mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-link"></i> Vincular</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vínculos Atuais</h5>
        <small class="text-muted">Cotas / pagamento / validade</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead><tr><th>Cliente</th><th>Plano</th><th>Cotas</th><th>Validade</th><th>Pago</th><th>Ativo</th><th style="width:130px">Ações</th></tr></thead>
            <tbody>
                @forelse($vinculos as $v)
                @php
                    $validade = $v->vencimento ?? $v->data_fim;
                    $dias = $validade ? $v->dias_para_vencer : null;
                    $cotasTxt = $v->total_restante . '/' . $v->total_contratada . ' restantes';
                    $badgeVal = '';
                    if ($validade) {
                        if ($v->expirado) $badgeVal = '<span class="badge bg-danger">Vencido '. $validade->format('d/m/Y') .'</span>';
                        elseif ($dias !== null && $dias <= 7) $badgeVal = '<span class="badge bg-warning text-dark">'.$dias.'d p/ vencer ('.$validade->format('d/m/Y').')</span>';
                        else $badgeVal = '<span class="badge bg-success">Até '.$validade->format('d/m/Y').'</span>';
                    } else $badgeVal = '<span class="badge bg-secondary">Sem validade</span>';
                    $editRoute = $slug ? route('tenant.admin.clientes-planos.edit', [$slug, $v]) : route('admin.clientes-planos.edit', $v);
                    $delRoute = $slug ? route('tenant.admin.clientes-planos.destroy', [$slug, $v]) : route('admin.clientes-planos.destroy', $v);
                @endphp
                <tr>
                    <td>
                        <strong>{{ $v->cliente->nome }}</strong><br>
                        <small class="text-muted">{{ $v->cliente->telefone }} @if($v->cpf) · {{ $v->cpf }} @endif</small>
                    </td>
                    <td>
                        <strong>{{ $v->plano->nome }}</strong><br>
                        <small class="text-muted">R$ {{ number_format($v->plano->valor, 2, ',', '.') }} @if($v->valor_pago) · pago R$ {{ number_format($v->valor_pago, 2, ',', '.') }} @endif</small>
                        @if($v->forma_pagamento) <br><small class="badge bg-light text-dark border">{{ $v->forma_pagamento }}</small> @endif
                    </td>
                    <td>
                        <span class="badge {{ $v->total_restante>0 ? 'bg-success' : 'bg-danger' }}">{{ $cotasTxt }}</span>
                        <button class="btn btn-xs btn-outline-secondary ms-1" style="font-size:10px;padding:1px 5px" onclick="toggleCotas({{ $v->id }})">detalhe</button>
                        <div id="cotas-{{ $v->id }}" style="display:none;margin-top:6px">
                            @foreach($v->plano->quotas as $q)
                                @php $usada = $v->usos->where('servico_id', $q->servico_id)->count(); $rest = max(0, $q->quantidade - $usada); @endphp
                                <div style="font-size:11px">{{ $q->servico->nome ?? 'Serv#'.$q->servico_id }}: <strong>{{ $usada }}/{{ $q->quantidade }}</strong> usados · <span class="{{ $rest>0?'text-success':'text-danger' }}">{{ $rest }} restantes</span></div>
                            @endforeach
                            <small class="text-muted">Total usado: {{ $v->total_usada }}/{{ $v->total_contratada }}</small>
                        </div>
                    </td>
                    <td>{!! $badgeVal !!} @if($dias!==null && !$v->expirado)<br><small class="text-muted">{{ $dias }} dia(s) restantes</small>@endif</td>
                    <td>
                        @if($v->pago)
                            <span class="badge bg-success">Pago @if($v->pago_em) {{ $v->pago_em->format('d/m/Y H:i') }} @endif</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendente</span>
                        @endif
                    </td>
                    <td>{!! $v->ativo ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>' !!}</td>
                    <td>
                        <a href="{{ $editRoute }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmarExclusao('{{ $delRoute }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum vínculo</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($vinculos->hasPages())<div class="card-footer">{{ $vinculos->links() }}</div>@endif
</div>
@push('scripts')
<script>
function confirmarExclusao(url) {
    Swal.fire({ title: 'Confirmar exclusão?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonText: 'Cancelar', confirmButtonText: 'Sim, excluir!' })
    .then((r) => { if(r.isConfirmed) $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() }); });
}
function toggleCotas(id){ var el=document.getElementById('cotas-'+id); if(el) el.style.display = el.style.display==='none'?'block':'none'; }
var planosQuotas = @json($planos->load('quotas.servico')->mapWithKeys(fn($p)=>[$p->id => $p->quotas->map(fn($q)=>['servico'=>$q->servico->nome,'qtd'=>$q->quantidade])->values()]));
function atualizarPlanoPreview(planoId, valor){
    if(valor){ document.getElementById('valorPago').value = parseFloat(valor).toFixed(2); }
    var quotas = planosQuotas[planoId] || [];
    var help = document.getElementById('planoQuotasHelp');
    var preview = document.getElementById('cotasPreview');
    if(quotas.length){
        help.textContent = quotas.map(q=> q.servico+': '+q.qtd).join(' | ');
        preview.style.display='block';
        preview.innerHTML = '<strong>Cotas inclusas:</strong><br>' + quotas.map(q=> q.servico+': <b>'+q.qtd+'x</b>').join('<br>');
    } else {
        if(planoId){ help.textContent='Sem cotas cadastradas'; preview.style.display='none'; }
        else { help.textContent=''; preview.style.display='none'; }
    }
}
// Suporte legacy select (se existir)
document.getElementById('planoSelect')?.addEventListener('change', function(){
    var opt = this.options[this.selectedIndex];
    atualizarPlanoPreview(this.value, opt?.dataset?.valor);
});
// Livewire: escuta seleção do plano
document.addEventListener('livewire:init', () => {
    if(window.Livewire){
        Livewire.on('plano-selecionado', (data) => {
            const planoId = data.planoId ?? data[0]?.planoId;
            const valor = data.valor ?? data[0]?.valor;
            atualizarPlanoPreview(planoId, valor);
        });
    }
});
// Polling fallback para hidden input plano_id (Livewire atualiza via AJAX)
let lastPlanoId = document.querySelector('input[name="plano_id"]')?.value || '';
setInterval(()=>{
    const el = document.querySelector('input[name="plano_id"]');
    if(!el) return;
    if(el.value !== lastPlanoId){
        lastPlanoId = el.value;
        if(lastPlanoId){
            // tenta achar valor via planosQuotas ou via dataset
            let valor = null;
            // procura no json de planos (precisa valor também)
            const planosValores = @json($planos->pluck('valor','id'));
            valor = planosValores[lastPlanoId] ?? null;
            atualizarPlanoPreview(lastPlanoId, valor);
        } else {
            atualizarPlanoPreview('', null);
        }
    }
}, 400);
document.querySelector('input[name="vencimento"]')?.addEventListener('change', function(){ document.getElementById('data_fim_hidden').value = this.value; });
</script>
@endpush
@endsection
