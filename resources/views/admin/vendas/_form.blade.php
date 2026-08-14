@php
$slug = request()->route('barbearia')?->slug;
$storeUrl = $slug ? route('tenant.admin.vendas.store', $slug) : route('admin.vendas.store');
$selectedCliente = $clienteId ?? ($venda->cliente_id ?? null);
$selectedAgendamento = $agendamentoId ?? ($venda->agendamento_id ?? null);
$vendaItens = isset($venda) && $venda->produtos->count() ? $venda->produtos->keyBy('id') : collect();
$vendaStatus = isset($venda) ? $venda->status : 'pendente';
$vendaForma = isset($venda) ? $venda->forma_pagamento : '';
$vendaDesconto = isset($venda) ? $venda->desconto : 0;
$vendaObs = isset($venda) ? $venda->observacoes : '';
$formAction = isset($venda)
    ? ($slug
        ? route('tenant.admin.vendas.update', [$slug, $venda])
        : route('admin.vendas.update', $venda))
    : ($slug
        ? route('tenant.admin.vendas.store', $slug)
        : route('admin.vendas.store'));
$editando = isset($venda);
@endphp

<section class="panel fade-in d1">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-receipt"/></svg></div>
            <div>
                <h2 class="panel-title">{{ isset($venda) ? 'Atualizar venda' : 'Nova venda' }}</h2>
                <div class="panel-subtitle">{{ isset($venda) ? 'Edite os itens da comanda e salve as alterações' : 'Monte a comanda do cliente adicionando os produtos comprados' }}</div>
            </div>
        </div>
        <a href="{{ $slug ? route('tenant.admin.vendas.index', $slug) : route('admin.vendas.index') }}" class="btn-ghost-c">
            <svg class="icon icon-sm"><use href="#i-arrow-left"/></svg>
            Voltar
        </a>
    </div>

    <form method="POST" action="{{ $formAction }}" class="panel-body" id="formVenda">
        @csrf
        @if($editando) @method('PUT') @endif

        @if(isset($agendamento) && $agendamento)
        <div class="alert" style="background:var(--info-bg);color:var(--info);border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <svg class="icon icon-sm"><use href="#i-calendar"/></svg>
            Venda vinculada ao agendamento de <strong>{{ $agendamento->cliente?->nome }}</strong> — {{ $agendamento->hora_inicio instanceof \Carbon\Carbon ? $agendamento->hora_inicio->format('d/m/Y H:i') : $agendamento->hora_inicio }}
        </div>
        @endif

        <div class="venda-grid">
            <div class="col-left">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Cliente <span class="mut">(opcional)</span></label>
                        <select name="cliente_id" class="form-select" id="clienteSelect">
                            <option value="">Cliente avulso / sem cadastro</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ (string)$selectedCliente === (string)$cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Forma de pagamento</label>
                        <select name="forma_pagamento" class="form-select">
                            <option value="">Selecione...</option>
                            @foreach(['Dinheiro', 'Pix', 'Cartão de Crédito', 'Cartão de Débito', 'Boleto', 'Outro'] as $fp)
                            <option value="{{ $fp }}" {{ $vendaForma === $fp ? 'selected' : '' }}>{{ $fp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status da venda</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Models\Venda::STATUS as $st)
                            <option value="{{ $st }}" {{ $vendaStatus === $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }} @if($st === 'pendente')(aberta, soma na comanda)@endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Desconto (R$)</label>
                        <input type="number" step="0.01" min="0" name="desconto" id="descontoInput" class="form-input" value="{{ $vendaDesconto }}">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-textarea" rows="3" placeholder="Anotações sobre a venda...">{{ $vendaObs }}</textarea>
                    </div>
                </div>

                <div class="form-actions" style="justify-content:flex-start;border:none;margin-top:16px;padding-top:0;">
                    <button type="submit" class="btn-primary-c" id="btnSalvar">
                        <svg class="icon icon-sm"><use href="#i-check"/></svg>
                        {{ isset($venda) ? 'Salvar alterações' : 'Registrar venda' }}
                    </button>
                    <a href="{{ $slug ? route('tenant.admin.vendas.index', $slug) : route('admin.vendas.index') }}" class="btn-ghost-c">Cancelar</a>
                </div>
            </div>

            <div class="col-right">
                <div class="comanda-panel">
                    <div class="comanda-head">
                        <div>
                            <h3>Produtos</h3>
                            <span>Selecione os itens comprados</span>
                        </div>
                        <span class="badge-c amber" id="itensCount">0 itens</span>
                    </div>

                    @if($produtos->count())
                    <div class="comanda-products">
                        @foreach($produtos as $produto)
                        @php $checked = $vendaItens->has($produto->id); @endphp
                        <label class="produto-card {{ $checked ? 'checked' : '' }}" data-id="{{ $produto->id }}">
                            <div class="produto-card-top">
                                <div class="produto-check">
                                    <input type="checkbox" class="produto-checkbox" name="produto_ids[]" value="{{ $produto->id }}" data-nome="{{ addslashes($produto->nome) }}" data-preco="{{ $produto->preco }}" {{ $checked ? 'checked' : '' }}>
                                    <span class="produto-nome">{{ $produto->nome }}</span>
                                </div>
                                <span class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                            </div>
                            <div class="produto-card-bottom">
                                <span class="produto-cat">{{ $produto->categoria ?: 'Produto' }}</span>
                                <div class="qty-stepper {{ $checked ? 'visible' : '' }}">
                                    <button type="button" class="qty-btn" onclick="mudarQtd({{ $produto->id }}, -1)">−</button>
                                    <input type="number" min="1" value="{{ $checked ? $vendaItens[$produto->id]->pivot->quantidade : 1 }}" name="quantidades[{{ $produto->id }}]" class="qty-input" data-id="{{ $produto->id }}" readonly>
                                    <button type="button" class="qty-btn" onclick="mudarQtd({{ $produto->id }}, 1)">+</button>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="icon-wrap"><svg class="icon"><use href="#i-box"/></svg></div>
                        <h4>Nenhum produto cadastrado</h4>
                        <p>Cadastre produtos na área de Produtos para poder vendê-los aqui.</p>
                    </div>
                    @endif

                    <div class="comanda-summary">
                        <div id="comandaItensList"></div>
                        <div class="summary-row"><span>Subtotal</span><strong id="subtotalValue">R$ 0,00</strong></div>
                        <div class="summary-row muted"><span>Desconto</span><strong id="descontoValue">- R$ 0,00</strong></div>
                        <div class="summary-total"><span>Total da comanda</span><strong id="totalValue">R$ 0,00</strong></div>
                        <p class="summary-hint">O total é somado automaticamente aos produtos selecionados.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@push('styles')
<style>
.venda-grid { display: grid; grid-template-columns: 1fr 420px; gap: 24px; align-items: start; }
.col-left { min-width: 0; }
.col-right { position: sticky; top: 24px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group.full { grid-column: 1 / -1; }
.form-label { font-size: 12.5px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 6px; }
.form-label .mut { color: var(--text-faint); font-weight: 500; }
.form-input, .form-select, .form-textarea { width: 100%; height: 44px; padding: 0 14px; border-radius: 10px; border: 1px solid var(--border-strong); background: var(--bg); color: var(--text); font-family: inherit; font-size: 14px; transition: all 180ms; }
.form-textarea { height: auto; padding: 12px 14px; resize: vertical; min-height: 80px; line-height: 1.5; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px var(--accent-glow); background: var(--bg-elevated); }
.form-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a8a94' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 38px; }
.form-actions { display: flex; align-items: center; gap: 10px; }

.comanda-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; backdrop-filter: blur(20px); }
.comanda-head { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.comanda-head h3 { font-size: 16px; font-weight: 700; margin: 0; }
.comanda-head span { font-size: 12px; color: var(--text-muted); }
.comanda-products { padding: 14px; display: flex; flex-direction: column; gap: 10px; max-height: 420px; overflow-y: auto; }
.produto-card { border: 1px solid var(--border-strong); border-radius: 12px; padding: 12px 14px; cursor: pointer; transition: all 180ms; background: var(--bg); }
.produto-card:hover { border-color: var(--accent); }
.produto-card.checked { border-color: var(--accent); background: var(--accent-glow); }
.produto-card-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
.produto-check { display: flex; align-items: center; gap: 10px; min-width: 0; }
.produto-check input { width: 18px; height: 18px; accent-color: var(--accent); cursor: pointer; flex-shrink: 0; }
.produto-nome { font-size: 13.5px; font-weight: 600; }
.produto-preco { font-size: 13.5px; font-weight: 700; color: var(--accent); white-space: nowrap; }
.produto-card-bottom { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
.produto-cat { font-size: 11px; color: var(--text-faint); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
.qty-stepper { display: none; align-items: center; gap: 4px; }
.qty-stepper.visible { display: inline-flex; }
.qty-btn { width: 26px; height: 26px; border-radius: 8px; border: 1px solid var(--border-strong); background: var(--card-solid); color: var(--text); font-size: 15px; font-weight: 700; cursor: pointer; display: grid; place-items: center; line-height: 1; padding: 0; font-family: inherit; }
.qty-btn:hover { border-color: var(--accent); color: var(--accent); }
.qty-input { width: 40px; height: 26px; text-align: center; border: 1px solid var(--border-strong); border-radius: 8px; background: var(--card-solid); color: var(--text); font-weight: 700; font-size: 13px; font-family: inherit; -moz-appearance: textfield; }
.qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

.comanda-summary { border-top: 1px solid var(--border); padding: 18px 22px; background: rgba(0,0,0,0.02); }
[data-bs-theme="light"] .comanda-summary { background: rgba(0,0,0,0.015); }
.comanda-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px dashed var(--border); font-size: 13px; }
.comanda-item small { color: var(--text-faint); }
.comanda-item strong { font-weight: 700; }
.summary-row { display: flex; align-items: center; justify-content: space-between; padding: 6px 0; font-size: 13.5px; color: var(--text-muted); }
.summary-row.muted strong { color: var(--danger); }
.summary-total { display: flex; align-items: center; justify-content: space-between; padding: 12px 0 6px; border-top: 1px solid var(--border-strong); margin-top: 8px; font-size: 18px; font-weight: 800; }
.summary-total strong { color: var(--accent); }
.summary-hint { font-size: 11.5px; color: var(--text-faint); margin: 8px 0 0; }
@media (max-width: 1200px) { .venda-grid { grid-template-columns: 1fr; } .col-right { position: static; } }
@media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .form-group.full { grid-column: auto; } }
</style>
@endpush

@push('scripts')
<script>
let produtosSelecionados = {};

function collectSelected() {
    const obj = {};
    document.querySelectorAll('.produto-checkbox:checked').forEach(cb => {
        const card = cb.closest('.produto-card');
        obj[cb.value] = {
            nome: cb.dataset.nome,
            preco: parseFloat(cb.dataset.preco),
            qtd: parseInt(card.querySelector('.qty-input').value) || 1,
        };
        card.classList.add('checked');
        card.querySelector('.qty-stepper').classList.add('visible');
    });
    document.querySelectorAll('.produto-checkbox:not(:checked)').forEach(cb => {
        const card = cb.closest('.produto-card');
        card.classList.remove('checked');
        card.querySelector('.qty-stepper').classList.remove('visible');
    });
    produtosSelecionados = obj;
    renderResumo();
}

function renderResumo() {
    const itens = Object.values(produtosSelecionados);
    const totalItens = itens.reduce((s, i) => s + i.qtd, 0);
    const subtotal = itens.reduce((s, i) => s + i.preco * i.qtd, 0);
    const desconto = Math.max(parseFloat(document.getElementById('descontoInput').value) || 0, 0);
    const total = Math.max(subtotal - desconto, 0);

    document.getElementById('itensCount').textContent = totalItens + (totalItens === 1 ? ' item' : ' itens');
    document.getElementById('subtotalValue').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
    document.getElementById('descontoValue').textContent = '- R$ ' + desconto.toFixed(2).replace('.', ',');
    document.getElementById('totalValue').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');

    document.querySelectorAll('.summary-linhas').forEach(el => el.remove());
    const list = document.getElementById('comandaItensList');
    if (list) {
        list.innerHTML = '';
        itens.forEach(i => {
            const div = document.createElement('div');
            div.className = 'comanda-item';
            div.innerHTML = '<span>' + i.nome + ' <small>×' + i.qtd + '</small></span><strong>R$ ' + (i.preco * i.qtd).toFixed(2).replace('.', ',') + '</strong>';
            list.appendChild(div);
        });
    }
}

function mudarQtd(id, delta) {
    const card = document.querySelector('.produto-card[data-id="' + id + '"]');
    const input = card.querySelector('.qty-input');
    const val = Math.max(1, (parseInt(input.value) || 1) + delta);
    input.value = val;
    if (produtosSelecionados[id]) {
        produtosSelecionados[id].qtd = val;
        renderResumo();
    }
}

document.querySelectorAll('.produto-checkbox').forEach(cb => {
    cb.addEventListener('change', collectSelected);
});
document.getElementById('descontoInput')?.addEventListener('input', renderResumo);
document.addEventListener('DOMContentLoaded', collectSelected);
</script>
@endpush