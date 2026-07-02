<div>
    <div class="panel fade-in d1" id="panelAbrir" style="display:{{ $showAbrirPanel ? '' : 'none' }};">
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
            <form wire:submit.prevent="abrir" class="form-caixa">
                <div class="form-group">
                    <label class="form-label">Unidade *</label>
                    <select wire:model="abrirBarbeariaId" class="form-input" {{ $barbearias->count() <= 1 ? '' : 'required' }}>
                        @if($barbearias->count() > 1)
                        <option value="">— Selecione —</option>
                        @endif
                        @foreach($barbearias as $b)
                        <option value="{{ $b->id }}" {{ $barbearias->count() <= 1 ? 'selected' : '' }}>{{ $b->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Data *</label>
                    <div class="input-group">
                        <span class="addon"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/></svg></span>
                        <input type="date" wire:model="abrirData" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Saldo Inicial (R$) *</label>
                    <div class="input-affix">
                        <span class="prefix">R$</span>
                        <input type="number" step="0.01" wire:model="abrirSaldoInicial" class="form-input" required>
                    </div>
                </div>
                <div style="grid-column:1/-1;">
                    <button type="submit" class="btn-primary-c"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Abrir Caixa</button>
                    <button type="button" class="btn-ghost-c" style="margin-left:8px;" wire:click="toggleAbrir">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

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

        <div class="panel-body" style="padding:16px 24px 0;">
            <div class="filter-bar">
                <label for="filterBarbearia">Unidade:</label>
                <select wire:model.live="barbeariaFilter" id="filterBarbearia" class="form-input" style="width:auto;min-width:180px;height:34px;padding:0 8px;font-size:13px;">
                    <option value="">Todas as unidades</option>
                    @foreach($barbearias as $b)
                    <option value="{{ $b->id }}">{{ $b->nome }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="panel-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Unidade</th>
                        <th>Saldo Inicial</th>
                        <th>Entradas</th>
                        <th>Saídas</th>
                        <th>Saldo Final</th>
                        <th>Status</th>
                        <th style="width:140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($caixas as $c)
                    @php $calcSaldo = $c->saldo_inicial + $c->total_entradas - $c->total_saidas; @endphp
                    <tr wire:key="caixa-{{ $c->id }}">
                        <td><strong>{{ $c->data->format('d/m/Y') }}</strong></td>
                        <td>
                            @if($editId === $c->id)
                            <select wire:model="editBarbeariaId" class="form-input" style="width:auto;min-width:120px;height:30px;padding:0 6px;font-size:12px;">
                                <option value="">— Sem unidade —</option>
                                @foreach($barbearias as $b)
                                <option value="{{ $b->id }}">{{ $b->nome }}</option>
                                @endforeach
                            </select>
                            @else
                            <span class="badge-c badge-info" style="font-size:11px;">{{ $c->barbearia?->nome ?? '—' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($editId === $c->id)
                            <div class="input-affix" style="display:inline-flex;height:30px;">
                                <span class="prefix" style="height:30px;line-height:30px;">R$</span>
                                <input type="number" step="0.01" wire:model="editSaldoInicial" class="form-input" style="width:100px;height:30px;padding:0 6px;font-size:12px;" required>
                            </div>
                            @else
                            <span class="badge-c badge-info">R$ {{ number_format($c->saldo_inicial, 2, ',', '.') }}</span>
                            @endif
                        </td>
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
                            @if($editId === $c->id)
                            <div style="display:flex;gap:4px;">
                                <button wire:click="saveEdit({{ $c->id }})" class="action-btn view" title="Salvar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>Salvar</button>
                                <button wire:click="cancelEdit" class="action-btn danger" title="Cancelar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                            </div>
                            @else
                            <div style="display:flex;gap:4px;">
                                <button wire:click="startEdit({{ $c->id }})" class="action-btn edit" title="Editar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4v16h16v-7M18.5 1.5a2.12 2.12 0 0 1 3 3L12 14l-4 1 1-4 9.5-9.5z"/></svg>Editar</button>
                                @if(!$c->fechado)
                                <button wire:click="openFechar({{ $c->id }})" class="action-btn warning" title="Fechar"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Fechar</button>
                                @else
                                <button wire:click="reabrir({{ $c->id }})" class="action-btn danger" title="Reabrir" onclick="return confirm('Reabrir caixa?')"><svg class="icon icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Reabrir</button>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
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

    {{-- Fechar Modal --}}
    @if($showFecharModal && $fecharId)
    <div class="modal-overlay show" style="display:flex;" wire:click.self="showFecharModal = false">
        <div class="modal-box" style="max-width:460px;">
            <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px 0;">
                <h3 style="font-size:16px;font-weight:700;">Fechar Caixa</h3>
                <button type="button" class="icon-btn" wire:click="showFecharModal = false" style="width:32px;height:32px;"><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
            </div>
            <form wire:submit.prevent="fechar">
                <div class="modal-body" style="padding:20px 24px;">
                    <div style="background:var(--bg-card);border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                        <div style="font-size:13px;color:var(--text-muted);margin-bottom:8px;">Saldo Calculado</div>
                        <div style="font-size:22px;font-weight:700;">R$ {{ number_format($fecharSaldoInformado, 2, ',', '.') }}</div>
                        <div style="font-size:12px;color:var(--text-faint);margin-top:4px;">inicial + entradas - saídas</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Saldo Informado (R$) *</label>
                        <div class="input-affix">
                            <span class="prefix">R$</span>
                            <input type="number" step="0.01" wire:model="fecharSaldoInformado" class="form-input" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Observações</label>
                        <textarea wire:model="fecharObservacoes" class="form-textarea" rows="2" placeholder="Opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="display:flex;gap:8px;justify-content:flex-end;padding:0 24px 20px;">
                    <button type="button" class="btn-ghost-c" wire:click="showFecharModal = false">Cancelar</button>
                    <button type="submit" class="btn-primary-c" style="background:var(--accent);">Fechar Caixa</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
