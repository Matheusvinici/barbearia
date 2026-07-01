@extends('layouts.app')

@section('title', 'Caixa')

@push('styles')
<style>
.action-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 8px;
    border: 1.5px solid;
    background: transparent;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 150ms;
    font-size: 12.5px;
    font-weight: 600;
    font-family: inherit;
    text-decoration: none;
    white-space: nowrap;
}
.action-btn.view { color: var(--info); border-color: var(--info); }
.action-btn.view:hover { background: var(--info-bg); }
.action-btn.edit { color: var(--accent); border-color: var(--accent); }
.action-btn.edit:hover { background: var(--accent-glow); }
.action-btn.warning { color: var(--accent); border-color: var(--accent); }
.action-btn.warning:hover { background: var(--accent-glow); }
.action-btn.danger { color: var(--danger); border-color: var(--danger); }
.action-btn.danger:hover { background: var(--danger-bg); }
.form-caixa { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width:640px) { .form-caixa { grid-template-columns: 1fr; } }
</style>
@endpush

@section('breadcrumb')
<svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.02 2.84L4.04 6.74c-.68.54-1.17 1.71-1.17 2.58v7.04c0 1.83 1.49 3.34 3.32 3.34h11.62c1.83 0 3.32-1.49 3.32-3.33V9.4c0-.93-.53-2.07-1.23-2.6l-5.71-4.04c-1.01-.72-2.55-.69-3.54.06z"/><path d="M12 17.5v-3"/></svg>
<span class="sep">/</span>
<span class="current">Caixa</span>
@endsection

@section('subtitle')
<span class="live-dot"></span>
<span>Gestão de caixa diário</span>
<span class="pipe">·</span>
<span>{{ $caixas->total() }} registros</span>
@endsection

@section('topbar-actions')
<button class="mobile-menu-btn" id="mobileMenuBtn"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 7h18M3 12h18M3 17h18"/></svg></button>
<button class="icon-btn" id="themeToggle" title="Alternar tema"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41" stroke-linecap="round"/></svg></button>
<button id="btnAbrirCaixa" class="btn-primary-c" onclick="document.getElementById('panelAbrir').classList.toggle('hidden')"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 12h12M12 6v12"/></svg>Abrir Caixa</button>
@endsection

@section('content')
<div class="main-grid">
    <div class="col-stack">

        {{-- Open Cash Register Panel --}}
        <div class="panel fade-in d1 hidden" id="panelAbrir" style="display:none;">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16.5v2.6c0 .97-.79 1.78-1.76 1.78-9.07.05-16.55-7.43-16.5-16.5 0-.97.81-1.76 1.78-1.76H7.1c.45 0 .85.3.97.73l.84 3.14c.11.41-.05.85-.39 1.11l-1.49 1.19c1.21 2.47 3.21 4.47 5.68 5.68l1.19-1.49c.26-.34.7-.5 1.11-.39l3.14.84c.43.12.73.52.73.97z"/></svg></div>
                    <div>
                        <h2 class="panel-title">Abrir Caixa</h2>
                        <div class="panel-subtitle">Registrar abertura de caixa para uma data</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form action="{{ route('admin.caixa.abrir') }}" method="POST" class="form-caixa">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Data *</label>
                        <div class="input-group">
                            <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/></svg></span>
                            <input type="date" name="data" class="form-input" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Saldo Inicial (R$) *</label>
                        <div class="input-affix">
                            <span class="prefix">R$</span>
                            <input type="number" step="0.01" name="saldo_inicial" class="form-input" value="0" required>
                        </div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <button type="submit" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Abrir Caixa</button>
                        <button type="button" class="btn-ghost-c" style="margin-left:8px;" onclick="document.getElementById('panelAbrir').classList.add('hidden');document.getElementById('panelAbrir').style.display='none';">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- History --}}
        <div class="panel fade-in d2">
            <div class="panel-header">
                <div class="panel-title-wrap">
                    <div class="panel-title-icon"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 4 4-6"/></svg></div>
                    <div>
                        <h2 class="panel-title">Histórico de Caixa</h2>
                        <div class="panel-subtitle">Registros de abertura e fechamento</div>
                    </div>
                </div>
            </div>

            <div class="panel-body" style="padding:0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Saldo Inicial</th>
                            <th>Entradas</th>
                            <th>Saídas</th>
                            <th>Saldo Final</th>
                            <th>Status</th>
                            <th style="width:180px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caixas as $c)
                        @php $calcSaldo = $c->saldo_inicial + $c->total_entradas - $c->total_saidas; @endphp
                        <tr>
                            <td><strong>{{ $c->data->format('d/m/Y') }}</strong></td>
                            <td><span class="badge-c badge-info">R$ {{ number_format($c->saldo_inicial, 2, ',', '.') }}</span></td>
                            <td style="color:var(--success);font-weight:600;">R$ {{ number_format($c->total_entradas, 2, ',', '.') }}</td>
                            <td style="color:var(--danger);font-weight:600;">R$ {{ number_format($c->total_saidas, 2, ',', '.') }}</td>
                            <td><strong>R$ {{ number_format($c->saldo_final, 2, ',', '.') }}</strong></td>
                            <td>
                                @if($c->fechado)
                                <span class="badge-c badge-danger">Fechado</span>
                                @else
                                <span class="badge-c badge-success">Aberto</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:4px;">
                                    <a href="{{ route('admin.caixa.show', $c) }}" class="action-btn view" title="Visualizar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>Ver</a>
                                    <a href="{{ route('admin.caixa.edit', $c) }}" class="action-btn edit" title="Editar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</a>
                                    @if(!$c->fechado)
                                    <button class="action-btn warning" onclick="document.getElementById('modalFechar{{ $c->id }}').classList.add('show');document.getElementById('modalFechar{{ $c->id }}').style.display='flex';" title="Fechar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Fechar</button>
                                    @else
                                    <form action="{{ route('admin.caixa.reabrir', $c) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="action-btn danger" title="Reabrir" onclick="return confirm('Reabrir caixa?')"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Reabrir</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if(!$c->fechado)
                        {{-- Modal Fechar Caixa --}}
                        <div class="modal-overlay" id="modalFechar{{ $c->id }}" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
                            <div class="modal-box" style="max-width:460px;">
                                <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px 0;">
                                    <h3 style="font-size:16px;font-weight:700;">Fechar Caixa</h3>
                                    <button type="button" class="icon-btn" onclick="document.getElementById('modalFechar{{ $c->id }}').style.display='none'" style="width:32px;height:32px;"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                                </div>
                                <form action="{{ route('admin.caixa.fechar', $c) }}" method="POST">
                                    @csrf
                                    <div class="modal-body" style="padding:20px 24px;">
                                        <div style="background:var(--bg-card);border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                                            <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Saldo Calculado</div>
                                            <div style="font-size:22px;font-weight:700;">R$ {{ number_format($calcSaldo, 2, ',', '.') }}</div>
                                            <div style="font-size:12px;color:var(--text-faint);margin-top:4px;">inicial + entradas - saídas</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Saldo Informado (R$) *</label>
                                            <div class="input-affix">
                                                <span class="prefix">R$</span>
                                                <input type="number" step="0.01" name="saldo_informado" class="form-input" value="{{ $calcSaldo }}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Observações</label>
                                            <textarea name="observacoes" class="form-textarea" rows="2" placeholder="Opcional"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="display:flex;gap:8px;justify-content:flex-end;padding:0 24px 20px;">
                                        <button type="button" class="btn-ghost-c" onclick="document.getElementById('modalFechar{{ $c->id }}').style.display='none'">Cancelar</button>
                                        <button type="submit" class="btn-primary-c" style="background:var(--accent);">Fechar Caixa</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                                <svg class="icon" style="width:40px;height:40px;margin-bottom:12px;opacity:0.3;" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 16l4-8 4 4 4-6"/></svg>
                                <div style="font-size:15px;font-weight:600;margin-bottom:4px;">Nenhum caixa registrado</div>
                                <div style="font-size:13px;">Clique em "Abrir Caixa" para começar.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($caixas->hasPages())
            <div class="panel-footer" style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:center;">
                {{ $caixas->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnAbrirCaixa')?.addEventListener('click', function() {
    var panel = document.getElementById('panelAbrir');
    panel.style.display = panel.style.display === 'none' ? '' : 'none';
});
</script>
@endpush
