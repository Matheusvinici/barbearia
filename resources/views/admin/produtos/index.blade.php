@extends('layouts.app')

@section('title', 'Produtos')

@section('breadcrumb')
    <svg class="icon icon-sm"><use href="#i-box"/></svg>
    <span class="sep">/</span>
    <span>Barber Control</span>
    <span class="sep">/</span>
    <span class="current">Produtos</span>
@endsection

@section('subtitle')
    <span class="live-dot"></span>
    <span>{{ $produtos->total() }} produtos cadastrados</span>
    <span class="pipe">·</span>
    <span>venda rápida de itens na comanda do cliente</span>
@endsection

@section('topbar-actions')
    <div class="search-box">
        <svg class="icon icon-sm"><use href="#i-search"/></svg>
        <input type="text" placeholder="Buscar produto…" name="q" form="filterForm" value="{{ request('q') }}">
        <span class="kbd">⌘K</span>
    </div>
    <button class="btn-primary-c" data-bs-toggle="modal" data-bs-target="#modalProduto">
        <svg class="icon icon-sm"><use href="#i-plus"/></svg>
        Novo Produto
    </button>
@endsection

@section('content')
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <defs>
        <symbol id="i-box" viewBox="0 0 24 24" fill="none"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M3.29 7L12 12l8.71-5M12 22V12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-search" viewBox="0 0 24 24" fill="none"><circle cx="11.5" cy="11.5" r="8.5" stroke="currentColor" stroke-width="1.6"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-plus" viewBox="0 0 24 24" fill="none"><path d="M6 12h12M12 6v12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-edit" viewBox="0 0 24 24" fill="none"><path d="M13.26 3.6l-8.21 8.69c-.31.33-.61.98-.67 1.43l-.37 3.24c-.13 1.17.71 1.98 1.87 1.8l3.22-.55c.45-.08 1.08-.41 1.39-.75L18.86 8.6c.75-.81.8-2.01-.02-2.79l-1.6-1.54c-.83-.79-2.16-.76-2.98.08z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.47 5.08l3.43 3.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></symbol>
        <symbol id="i-close" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="i-tag" viewBox="0 0 24 24" fill="none"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="7" cy="7" r="1.5" stroke="currentColor" stroke-width="1.6"/></symbol>
        <symbol id="i-activity" viewBox="0 0 24 24" fill="none"><path d="M2 12h4l3-9 6 18 3-9h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
        <symbol id="i-coins" viewBox="0 0 24 24" fill="none"><circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18M7 6h1v4M16.71 13.88l.7.71-2.82 2.82" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></symbol>
    </defs>
</svg>

<section class="stats-grid">
    <div class="stat-card fade-in d1">
        <div class="stat-top"><div class="stat-icon amber"><svg class="icon"><use href="#i-box"/></svg></div></div>
        <div class="stat-label">Produtos cadastrados</div>
        <div class="stat-value">{{ $produtos->total() }}</div>
        <div class="stat-sub">no catálogo</div>
    </div>
    <div class="stat-card fade-in d2">
        <div class="stat-top"><div class="stat-icon green"><svg class="icon"><use href="#i-activity"/></svg></div></div>
        <div class="stat-label">Ativos</div>
        <div class="stat-value">{{ $produtos->where('ativo', true)->count() }}</div>
        <div class="stat-sub">disponíveis para venda</div>
    </div>
    <div class="stat-card fade-in d3">
        <div class="stat-top"><div class="stat-icon blue"><svg class="icon"><use href="#i-tag"/></svg></div></div>
        <div class="stat-label">Categorias</div>
        <div class="stat-value">{{ $categorias->count() }}</div>
        <div class="stat-sub">segmentos distintos</div>
    </div>
    <div class="stat-card fade-in d4">
        <div class="stat-top"><div class="stat-icon purple"><svg class="icon"><use href="#i-coins"/></svg></div></div>
        <div class="stat-label">Estoque total</div>
        <div class="stat-value">{{ number_format($produtos->sum('estoque'), 0, ',', '.') }}</div>
        <div class="stat-sub">unidades em estoque</div>
    </div>
</section>

<section class="panel fade-in d5">
    <div class="panel-header">
        <div class="panel-title-wrap">
            <div class="panel-title-icon"><svg class="icon"><use href="#i-box"/></svg></div>
            <div>
                <h2 class="panel-title">Catálogo de produtos</h2>
                <div class="panel-subtitle">Cadastre os produtos vendidos na barbearia</div>
            </div>
        </div>
    </div>

    <form id="filterForm" method="GET" class="toolbar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar produto…" style="height:36px;padding:0 12px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;min-width:180px;">
        <select name="categoria" onchange="this.form.submit()" style="height:36px;padding:0 10px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
            <option value="">Todas as categorias</option>
            @foreach($categorias as $c)
            <option value="{{ $c }}" {{ request('categoria') == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()" style="height:36px;padding:0 10px;border-radius:10px;border:1px solid var(--border-strong);background:var(--card-solid);color:var(--text);font-family:inherit;font-size:13px;">
            <option value="">Todos os status</option>
            <option value="ativos" {{ request('status') == 'ativos' ? 'selected' : '' }}>Ativos</option>
            <option value="inativos" {{ request('status') == 'inativos' ? 'selected' : '' }}>Inativos</option>
        </select>
        <div class="toolbar-spacer"></div>
        <div class="result-count"><strong>{{ $produtos->total() }}</strong> produto(s)</div>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Categoria</th>
                    <th style="text-align:right">Preço</th>
                    <th style="text-align:right">Estoque</th>
                    <th>Status</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $produto)
                <tr>
                    <td>
                        <div class="avatar-row">
                            <div class="av {{ $produto->ativo ? 'amber' : 'gray' }}"><svg class="icon icon-sm"><use href="#i-box"/></svg></div>
                            <div class="info">
                                <strong>{{ $produto->nome }}</strong>
                                @if($produto->descricao)<span>{{ \Illuminate\Support\Str::limit($produto->descricao, 45) }}</span>@endif
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-c outlined">{{ $produto->categoria ?: '—' }}</span></td>
                    <td style="text-align:right;font-weight:700;">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td style="text-align:right">
                        @if($produto->estoque !== null && $produto->estoque <= 0)
                        <span class="badge-c red">Esgotado</span>
                        @else
                        <span class="cell-muted">{{ $produto->estoque ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($produto->ativo)
                        <span class="badge-c green">Ativo</span>
                        @else
                        <span class="badge-c gray">Inativo</span>
                        @endif
                    </td>
                    <td style="text-align:right">
                        <div class="actions-cell">
                            <button class="action-btn" onclick="editarProduto({{ $produto->id }}, '{{ addslashes($produto->nome) }}', '{{ addslashes($produto->categoria ?? '') }}', '{{ addslashes($produto->descricao ?? '') }}', '{{ $produto->preco }}', '{{ $produto->custo ?? '' }}', '{{ $produto->estoque ?? '' }}', {{ $produto->ativo ? '1' : '0' }})">
                                <svg class="icon"><use href="#i-edit"/></svg>
                                <span class="action-label">Editar</span>
                            </button>
                            <button class="action-btn danger" onclick="confirmarExclusao('{{ route((request()->route('barbearia') ? 'tenant.admin.produtos.destroy' : 'admin.produtos.destroy'), request()->route('barbearia') ? [request()->route('barbearia')->slug, $produto] : [$produto]) }}')">
                                <svg class="icon"><use href="#i-close"/></svg>
                                <span class="action-label">Excluir</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="icon-wrap"><svg class="icon"><use href="#i-box"/></svg></div>
                            <h4>Nenhum produto cadastrado</h4>
                            <p>Cadastre produtos para vendê-los na comanda do cliente.</p>
                            <button class="btn-primary-c" data-bs-toggle="modal" data-bs-target="#modalProduto">
                                <svg class="icon icon-sm"><use href="#i-plus"/></svg> Novo Produto
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($produtos->hasPages())
    <div class="panel-footer" style="justify-content:center;border-top:1px solid var(--border);">
        {{ $produtos->links() }}
    </div>
    @endif
</section>

<div class="modal fade" id="modalProduto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="formProduto">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProdutoTitle">Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="_method" id="produtoMethod" value="POST">
                    <div class="mb-3">
                        <label class="form-label">Nome do produto *</label>
                        <input type="text" name="nome" class="form-control" required placeholder="Ex.: Pomada modeladora 100g">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select">
                            <option value="">Selecione a categoria…</option>
                            @foreach($categorias as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Preço (R$) *</label>
                            <input type="number" step="0.01" min="0" name="preco" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Custo (R$)</label>
                            <input type="number" step="0.01" min="0" name="custo" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estoque</label>
                        <input type="number" min="0" name="estoque" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="ativo" value="1" id="produtoAtivo" checked>
                        <label class="form-check-label" for="produtoAtivo">Produto ativo (disponível para venda)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
.stat-card { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); padding: 22px; position: relative; overflow: hidden; transition: all 220ms; }
.stat-card:hover { border-color: var(--border-strong); transform: translateY(-2px); }
.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; }
.stat-icon.amber { background: var(--accent-glow); color: var(--accent); }
.stat-icon.green { background: var(--success-bg); color: var(--success); }
.stat-icon.blue { background: var(--info-bg); color: var(--info); }
.stat-icon.purple { background: var(--purple-bg); color: var(--purple); }
.stat-label { font-size: 12.5px; color: var(--text-muted); font-weight: 500; margin-bottom: 6px; }
.stat-value { font-size: 28px; font-weight: 800; letter-spacing: -0.025em; line-height: 1; }
.stat-sub { font-size: 11.5px; color: var(--text-faint); margin-top: 10px; font-weight: 500; }
.panel { background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: var(--r-lg); overflow: hidden; }
.panel-header { padding: 22px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.panel-title-wrap { display: flex; align-items: center; gap: 14px; }
.panel-title-icon { width: 40px; height: 40px; border-radius: 11px; background: var(--accent-glow); color: var(--accent); display: grid; place-items: center; }
.panel-title { font-size: 17px; font-weight: 700; margin: 0; letter-spacing: -0.015em; }
.panel-subtitle { font-size: 12.5px; color: var(--text-muted); margin-top: 2px; }
.toolbar { padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; background: rgba(0,0,0,0.02); }
.toolbar-spacer { flex: 1; }
.result-count { font-size: 13px; color: var(--text-muted); }
.result-count strong { color: var(--text); font-weight: 700; }
.actions-cell { display: flex; gap: 4px; justify-content: flex-end; flex-wrap: wrap; }
.action-btn { height: 34px; padding: 0 10px; border-radius: 9px; border: 1px solid var(--border); background: transparent; color: var(--text-muted); display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 150ms; font-size: 12px; font-weight: 600; font-family: inherit; white-space: nowrap; }
.action-btn:hover { color: var(--accent); border-color: var(--accent); background: var(--accent-glow); }
.action-btn.danger:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-bg); }
.action-btn .icon { width: 16px; height: 16px; }
.action-label { font-size: 12px; }
.av.gray { background: var(--border); color: var(--text-muted); }
.av.amber { background: linear-gradient(135deg, #f5b544, #e89538); }
.badge-c.outlined { background: transparent; border: 1px solid var(--border-strong); color: var(--text-muted); }
.fade-in { animation: fadeInUp 400ms ease both; }
.d1 { animation-delay: 50ms; } .d2 { animation-delay: 100ms; } .d3 { animation-delay: 150ms; } .d4 { animation-delay: 200ms; } .d5 { animation-delay: 250ms; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script>
@php $slug = request()->route('barbearia')?->slug; @endphp
const baseUrl = '{{ $slug ? route('tenant.admin.produtos.index', $slug) : route('admin.produtos.index') }}';

function editarProduto(id, nome, categoria, descricao, preco, custo, estoque, ativo) {
    document.getElementById('modalProdutoTitle').textContent = 'Editar Produto';
    document.getElementById('formProduto').action = baseUrl + '/' + id;
    document.getElementById('produtoMethod').value = 'PUT';
    const f = document.getElementById('formProduto');
    f.nome.value = nome;
    f.categoria.value = categoria;
    f.descricao.value = descricao;
    f.preco.value = preco;
    f.custo.value = custo;
    f.estoque.value = estoque;
    f.querySelector('#produtoAtivo').checked = !!ativo;
    new bootstrap.Modal(document.getElementById('modalProduto')).show();
}

document.getElementById('modalProduto').addEventListener('show.bs.modal', function() {
    if (this.querySelector('.modal-title').textContent !== 'Editar Produto') {
        document.getElementById('formProduto').action = baseUrl;
        document.getElementById('produtoMethod').value = 'POST';
        document.getElementById('formProduto').reset();
        document.getElementById('produtoAtivo').checked = true;
    }
});

function confirmarExclusao(url) {
    Swal.fire({
        title: 'Confirmar exclusão?',
        text: 'Este produto será removido do catálogo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sim, excluir!'
    }).then((r) => {
        if (r.isConfirmed) {
            $.ajax({ url, method: 'DELETE', data: { _token: '{{ csrf_token() }}' }, success: () => location.reload() });
        }
    });
}

document.addEventListener('keydown', function(e) {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-box input').focus();
    }
});
</script>
@endpush