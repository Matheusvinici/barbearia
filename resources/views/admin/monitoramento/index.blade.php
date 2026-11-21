@extends('layouts.app')

@section('title', 'Monitoramento de Clientes')

@php
use App\Http\Controllers\Admin\MonitoramentoController;
$showAtrasados = in_array($tipo, ['todos', 'atrasados']);
$showUmaVez = in_array($tipo, ['todos', 'uma_vez']);
$showQueda = in_array($tipo, ['todos', 'queda']);
$limitTodos = 15;
$limitFull = 200;
$exportBase = rtrim(url()->current(), '/') . '/export';
$qs = request()->query();
$exportUrl = function ($t) use ($exportBase, $qs) {
    return $exportBase . '?' . http_build_query(array_merge($qs, ['export_tipo' => $t]));
};
@endphp

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
<span class="sep">/</span>
<span class="current">Monitoramento</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>{{ $totalClientes }} clientes no escopo · {{ count($atrasados) }} demorando a retornar · {{ count($umaVez) }} vieram 1x · {{ count($queda) }} com queda</span>
@endsection

@section('topbar-actions')
<a href="{{ $exportUrl($tipo === 'todos' ? 'atrasados' : $tipo) }}" class="btn-ghost-c" style="text-decoration:none;">
    <svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
    Exportar CSV
</a>
@endsection

@section('content')
<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-label">Demorando a retornar (≥ {{ $diasAtraso }}d)</div>
        <div class="stat-value">{{ count($atrasados) }}</div>
        <div class="stat-sub">ordenados: mais ausentes primeiro</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-label">Vieram 1 vez e não voltaram (≥ {{ $diasUmaVez }}d)</div>
        <div class="stat-value">{{ count($umaVez) }}</div>
        <div class="stat-sub">foco em reconquista</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-label">Frequência caiu</div>
        <div class="stat-value">{{ count($queda) }}</div>
        <div class="stat-sub">ritmo recente pior que o antigo</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-label">Sem visita registrada</div>
        <div class="stat-value">{{ $semVisitaCount }}</div>
        <div class="stat-sub">cadastrados sem agendamento concluído</div>
    </div>
</section>

<section class="panel fade-in d2" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11.5" cy="11.5" r="8.5"/><path d="M21 21l-4.35-4.35"/></svg>
            </div>
            <div>
                <h2 class="panel-title">Filtros do relatório</h2>
                <div class="panel-subtitle">Tipos de relatório + cruzamento por nome e telefone</div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <form method="GET" class="form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:end;">
            <div class="form-group">
                <label class="form-label">Tipo de relatório</label>
                <select name="tipo" class="form-input">
                    <option value="todos" {{ $tipo==='todos'?'selected':'' }}>Todos (resumo)</option>
                    <option value="atrasados" {{ $tipo==='atrasados'?'selected':'' }}>Demorando a retornar</option>
                    <option value="uma_vez" {{ $tipo==='uma_vez'?'selected':'' }}>Vieram 1 vez</option>
                    <option value="queda" {{ $tipo==='queda'?'selected':'' }}>Frequência caiu</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Dias sem retornar (≥)</label>
                <input type="number" name="dias_atraso" class="form-input" min="1" value="{{ $diasAtraso }}">
            </div>
            <div class="form-group">
                <label class="form-label">Dias p/ 1 visita (≥)</label>
                <input type="number" name="dias_uma_vez" class="form-input" min="1" value="{{ $diasUmaVez }}">
            </div>
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Buscar nome / telefone</label>
                <input type="text" name="q" class="form-input" placeholder="Ex: Carlos ou 99988..." value="{{ $busca }}">
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Mensagem padrão do WhatsApp (use {nome} e {barbearia})</label>
                <textarea id="msgTemplate" name="mensagem" class="form-textarea" rows="2">{{ $template }}</textarea>
            </div>
            <div style="grid-column:1/-1;display:flex;gap:8px;flex-wrap:wrap;">
                <button type="submit" class="btn-primary-c">Aplicar filtros</button>
                <a href="{{ url()->current() }}" class="btn-ghost-c" style="text-decoration:none;">Limpar</a>
                <span class="cell-muted" style="font-size:12px;align-self:center;">Unidade: <strong>{{ $barbeariaNome }}</strong> · cruzamento por telefone normalizado (DDI 55).</span>
            </div>
        </form>
    </div>
</section>

@if($showAtrasados)
<section class="panel fade-in d3" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon">⏳</div>
            <div>
                <h2 class="panel-title">Demorando a retornar</h2>
                <div class="panel-subtitle">{{ count($atrasados) }} clientes · última visita há ≥ {{ $diasAtraso }} dias · ordem: mais ausentes primeiro</div>
            </div>
        </div>
        <a href="{{ $exportUrl('atrasados') }}" class="btn-ghost-c" style="text-decoration:none;font-size:12.5px;">CSV</a>
    </div>
    @if(count($atrasados))
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Cliente</th><th>Telefone</th><th class="text-center">Visitas</th><th>Última</th><th>Dias fora</th><th>Média</th><th>Zap</th></tr></thead>
            <tbody>
            @foreach(($tipo==='atrasados' ? array_slice($atrasados, 0, $limitFull) : array_slice($atrasados, 0, $limitTodos)) as $r)
                @include('admin.monitoramento._row', ['r' => $r, 'showMotivo' => false])
            @endforeach
            </tbody>
        </table>
    </div>
    @if($tipo==='todos' && count($atrasados) > $limitTodos)
    <div class="panel-footer"><a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['tipo' => 'atrasados'])) }}">Ver todos os {{ count($atrasados) }} →</a></div>
    @endif
    @else
    <div class="panel-body"><div class="empty-state"><h4>Ninguém atrasado 🎉</h4><p>Nenhum cliente com ≥ {{ $diasAtraso }} dias sem voltar.</p></div></div>
    @endif
</section>
@endif

@if($showUmaVez)
<section class="panel fade-in d3" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon">👋</div>
            <div>
                <h2 class="panel-title">Vieram 1 vez e não voltaram</h2>
                <div class="panel-subtitle">{{ count($umaVez) }} clientes · 1 visita há ≥ {{ $diasUmaVez }} dias</div>
            </div>
        </div>
        <a href="{{ $exportUrl('uma_vez') }}" class="btn-ghost-c" style="text-decoration:none;font-size:12.5px;">CSV</a>
    </div>
    @if(count($umaVez))
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Cliente</th><th>Telefone</th><th class="text-center">Visitas</th><th>Única visita</th><th>Dias fora</th><th>Barbeiro</th><th>Zap</th></tr></thead>
            <tbody>
            @foreach(($tipo==='uma_vez' ? array_slice($umaVez, 0, $limitFull) : array_slice($umaVez, 0, $limitTodos)) as $r)
                @include('admin.monitoramento._row', ['r' => $r, 'showMotivo' => false, 'showBarbeiro' => true])
            @endforeach
            </tbody>
        </table>
    </div>
    @if($tipo==='todos' && count($umaVez) > $limitTodos)
    <div class="panel-footer"><a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['tipo' => 'uma_vez'])) }}">Ver todos os {{ count($umaVez) }} →</a></div>
    @endif
    @else
    <div class="panel-body"><div class="empty-state"><h4>Tudo fidelizado 🎉</h4><p>Nenhum cliente de 1ª visita pendente de retorno.</p></div></div>
    @endif
</section>
@endif

@if($showQueda)
<section class="panel fade-in d3" style="margin-bottom:22px;">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon">📉</div>
            <div>
                <h2 class="panel-title">Frequência caiu</h2>
                <div class="panel-subtitle">{{ count($queda) }} clientes · vinham com frequência e espaçaram</div>
            </div>
        </div>
        <a href="{{ $exportUrl('queda') }}" class="btn-ghost-c" style="text-decoration:none;font-size:12.5px;">CSV</a>
    </div>
    @if(count($queda))
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Cliente</th><th>Telefone</th><th class="text-center">Visitas</th><th>Última</th><th>Dias fora</th><th>Motivo da queda</th><th>Zap</th></tr></thead>
            <tbody>
            @foreach(($tipo==='queda' ? array_slice($queda, 0, $limitFull) : array_slice($queda, 0, $limitTodos)) as $r)
                @include('admin.monitoramento._row', ['r' => $r, 'showMotivo' => true])
            @endforeach
            </tbody>
        </table>
    </div>
    @if($tipo==='todos' && count($queda) > $limitTodos)
    <div class="panel-footer"><a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['tipo' => 'queda'])) }}">Ver todos os {{ count($queda) }} →</a></div>
    @endif
    @else
    <div class="panel-body"><div class="empty-state"><h4>Ritmo estável 🎉</h4><p>Nenhuma queda de frequência detectada.</p></div></div>
    @endif
</section>
@endif
@endsection

@push('styles')
<style>
.zap-btn{display:inline-flex;align-items:center;gap:6px;background:#25D366;color:#fff!important;font-weight:700;font-size:12.5px;padding:7px 12px;border-radius:999px;text-decoration:none;white-space:nowrap;}
.zap-btn:hover{filter:brightness(.93);}
.zap-btn.disabled{background:#cbd5e1;pointer-events:none;}
.badge-dup{background:#fef3c7;color:#92400e;font-size:10.5px;font-weight:800;padding:2px 8px;border-radius:999px;margin-left:6px;}
</style>
@endpush

@push('scripts')
<script>
(function(){
  var tpl = document.getElementById('msgTemplate');
  var barb = @json($barbeariaNome);
  function firstName(n){ return (n||'').trim().split(/\s+/)[0] || n; }
  function refresh(){
    if(!tpl) return;
    var t = tpl.value;
    document.querySelectorAll('.zap-link').forEach(function(a){
      var digits = a.getAttribute('data-digits')||'';
      var nome = a.getAttribute('data-nome')||'';
      if(!digits){ return; }
      var msg = t.split('{nome}').join(firstName(nome)).split('{barbearia}').join(barb);
      a.href = 'https://wa.me/' + digits + '?text=' + encodeURIComponent(msg);
    });
  }
  if(tpl){ tpl.addEventListener('input', refresh); refresh(); }
})();
</script>
@endpush
