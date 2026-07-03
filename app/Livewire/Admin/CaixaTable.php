<?php

namespace App\Livewire\Admin;

use App\Models\Agendamento;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\Caixa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CaixaTable extends Component
{
    public $tenantSlug;
    public $tenantIds = [];
    public $barbeariaFilter;
    public $barbeiroFilter;
    public $dataInicio;
    public $dataFim;
    public $barbeiros = [];

    public $editId;
    public $editSaldoInicial;
    public $editBarbeariaId;

    public $fecharId;
    public $fecharSaldoInformado;
    public $fecharObservacoes = '';
    public $showFecharModal = false;

    public $showAbrirPanel = false;
    public $abrirData;
    public $abrirSaldoInicial = 0;
    public $abrirBarbeariaId;

    protected function getAvailableBarbearias()
    {
        if ($this->tenantSlug) {
            $tenant = Barbearia::where('slug', $this->tenantSlug)->first();
            if (!$tenant) return collect();
            return Barbearia::whereIn('id', $tenant->tenantTreeIds())->get();
        }

        $user = Auth::guard('web')->user();
        if ($user && $user->isSuperAdmin()) {
            return Barbearia::all();
        }

        $ids = $user?->ownedBarbearias()->get()
            ->flatMap(fn($b) => $b->tenantTreeIds())
            ->unique()->values()->toArray() ?? [];

        return Barbearia::whereIn('id', $ids)->get();
    }

    public function mount($tenantSlug = null)
    {
        $this->tenantSlug = $tenantSlug;
        $this->abrirData = now()->format('Y-m-d');
        $this->dataInicio = request('data_inicio', now()->format('Y-m-d'));
        $this->dataFim = request('data_fim', now()->format('Y-m-d'));
        $this->barbeariaFilter = request('barbearia_id');
        $this->barbeiroFilter = request('barbeiro_id');
        if ($tenantSlug) {
            $tenant = Barbearia::where('slug', $tenantSlug)->first();
            if ($tenant) {
                $this->tenantIds = $tenant->tenantTreeIds();
            }
        }
        $this->carregarBarbeiros();
    }

    public function getBarbeariasProperty()
    {
        return $this->getAvailableBarbearias();
    }

    public function updatedBarbeariaFilter($value)
    {
        $this->barbeiroFilter = null;
        $this->carregarBarbeiros();
    }

    public function carregarBarbeiros()
    {
        if ($this->barbeariaFilter) {
            $this->barbeiros = Barbeiro::where('barbearia_id', $this->barbeariaFilter)
                ->orWhereHas('barbearias', fn($q) => $q->where('barbearias.id', $this->barbeariaFilter))
                ->where('ativo', true)
                ->orderBy('nome')
                ->get();
        } else {
            $this->barbeiros = collect();
        }
    }

    public function getCaixasQueryProperty()
    {
        $barbearias = $this->getAvailableBarbearias();
        $ids = $barbearias->pluck('id')->toArray();

        $query = Caixa::with(['usuarioAbertura', 'usuarioFechamento', 'barbearia'])
            ->orderBy('data', 'desc')
            ->orderBy('barbearia_id');

        if ($this->dataInicio) {
            $query->whereDate('data', '>=', $this->dataInicio);
        }
        if ($this->dataFim) {
            $query->whereDate('data', '<=', $this->dataFim);
        }

        if ($this->barbeariaFilter) {
            $query->where('barbearia_id', $this->barbeariaFilter);
        } elseif (!empty($ids)) {
            $query->where(function ($q) use ($ids) {
                $q->whereIn('barbearia_id', $ids)->orWhereNull('barbearia_id');
            });
        }

        return $query;
    }

    public function getCaixasProperty()
    {
        return $this->caixasQuery->paginate(20);
    }

    public function getResumoProperty()
    {
        $caixas = $this->caixasQuery->get();
        $totalEntradas = $caixas->sum('total_entradas');
        $totalSaidas = $caixas->sum('total_saidas');
        $abertos = $caixas->where('fechado', false)->count();
        $fechados = $caixas->where('fechado', true)->count();

        return [
            'total_entradas' => $totalEntradas,
            'total_saidas' => $totalSaidas,
            'saldo' => $caixas->sum('saldo_final'),
            'abertos' => $abertos,
            'fechados' => $fechados,
            'total_caixas' => $caixas->count(),
            'saldo_liquido' => $totalEntradas - $totalSaidas,
        ];
    }

    public function getTotalBarbeiroProperty()
    {
        if (!$this->barbeiroFilter) return null;

        $query = Agendamento::where('barbeiro_id', $this->barbeiroFilter)
            ->where('status', 'realizado');

        if ($this->dataInicio) {
            $query->whereDate('data', '>=', $this->dataInicio);
        }
        if ($this->dataFim) {
            $query->whereDate('data', '<=', $this->dataFim);
        }
        if ($this->barbeariaFilter) {
            $query->where('barbearia_id', $this->barbeariaFilter);
        }

        $total = $query->sum('total');
        $qtd = $query->count();
        $nome = Barbeiro::find($this->barbeiroFilter)?->nome;

        return compact('total', 'qtd', 'nome');
    }

    public function startEdit($id)
    {
        $caixa = Caixa::findOrFail($id);
        $this->editId = $id;
        $this->editSaldoInicial = $caixa->saldo_inicial;
        $this->editBarbeariaId = $caixa->barbearia_id;
    }

    public function saveEdit($id)
    {
        $this->validate([
            'editSaldoInicial' => 'required|numeric|min:0',
        ]);

        $caixa = Caixa::findOrFail($id);

        $barbearias = $this->getAvailableBarbearias();
        $validIds = $barbearias->pluck('id')->toArray();

        $data = ['saldo_inicial' => $this->editSaldoInicial];

        if ($this->editBarbeariaId !== null && $this->editBarbeariaId !== '') {
            $newBarbeariaId = (int)$this->editBarbeariaId;
            if (in_array($newBarbeariaId, $validIds)) {
                $existing = Caixa::where('data', $caixa->data)
                    ->where('barbearia_id', $newBarbeariaId)
                    ->where('id', '!=', $caixa->id)
                    ->exists();
                if ($existing) {
                    $this->dispatch('notify', 'Já existe um caixa para esta unidade nesta data.', 'error');
                    return;
                }
                $data['barbearia_id'] = $newBarbeariaId;
            }
        } else {
            $data['barbearia_id'] = null;
        }

        $caixa->saldo_final = (float)$caixa->saldo_inicial + (float)$caixa->total_entradas - (float)$caixa->total_saidas;
        $caixa->update($data);

        $this->editId = null;
        $this->editSaldoInicial = null;
        $this->editBarbeariaId = null;

        $this->dispatch('notify', 'Caixa atualizado com sucesso!', 'success');
    }

    public function cancelEdit()
    {
        $this->editId = null;
        $this->editSaldoInicial = null;
        $this->editBarbeariaId = null;
    }

    public function openFechar($id)
    {
        $this->fecharId = $id;
        $caixa = Caixa::findOrFail($id);
        $this->fecharSaldoInformado = $caixa->saldo_inicial + $caixa->total_entradas - $caixa->total_saidas;
        $this->fecharObservacoes = '';
        $this->showFecharModal = true;
    }

    public function fechar()
    {
        $this->validate([
            'fecharSaldoInformado' => 'required|numeric|min:0',
            'fecharObservacoes' => 'nullable|string',
        ]);

        $caixa = Caixa::findOrFail($this->fecharId);
        $caixa->update([
            'saldo_final' => $this->fecharSaldoInformado,
            'fechado' => true,
            'observacoes' => $this->fecharObservacoes,
            'user_id_fechamento' => Auth::guard('web')->id(),
        ]);

        $this->showFecharModal = false;
        $this->fecharId = null;

        $this->dispatch('notify', 'Caixa fechado com sucesso!', 'success');
    }

    public function reabrir($id)
    {
        $caixa = Caixa::findOrFail($id);
        if (!$caixa->fechado) {
            $this->dispatch('notify', 'Caixa já está aberto.', 'error');
            return;
        }

        $totalEntradas = $caixa->movimentacoes()->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = $caixa->movimentacoes()->where('tipo', 'saida')->sum('valor');

        $caixa->update([
            'fechado' => false,
            'user_id_fechamento' => null,
            'total_entradas' => $totalEntradas,
            'total_saidas' => $totalSaidas,
            'saldo_final' => $caixa->saldo_inicial + $totalEntradas - $totalSaidas,
        ]);

        $this->dispatch('notify', 'Caixa reaberto com sucesso!', 'success');
    }

    public function destroy($id)
    {
        $caixa = Caixa::findOrFail($id);
        if ($caixa->movimentacoes()->exists()) {
            $this->dispatch('notify', 'Não é possível excluir um caixa que possui movimentações.', 'error');
            return;
        }
        $caixa->delete();
        $this->dispatch('notify', 'Caixa excluído com sucesso!', 'success');
    }

    public function abrir()
    {
        $barbearias = $this->getAvailableBarbearias();
        $barbeariaIds = $barbearias->pluck('id')->toArray();

        $rules = [
            'abrirData' => 'required|date',
            'abrirSaldoInicial' => 'required|numeric|min:0',
        ];

        if ($barbearias->count() > 1) {
            $rules['abrirBarbeariaId'] = 'required|in:' . implode(',', $barbeariaIds);
        } else {
            $rules['abrirBarbeariaId'] = 'in:' . implode(',', $barbeariaIds);
        }

        $this->validate($rules);

        $barbeariaId = $this->abrirBarbeariaId ?? ($barbeariaIds[0] ?? null);

        $existing = Caixa::whereDate('data', $this->abrirData)
            ->where('barbearia_id', $barbeariaId)
            ->first();

        if ($existing) {
            $existing->update([
                'saldo_inicial' => $this->abrirSaldoInicial,
                'saldo_final' => $this->abrirSaldoInicial + $existing->total_entradas - $existing->total_saidas,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]);
            $msg = 'Saldo inicial do caixa atualizado com sucesso!';
        } else {
            Caixa::create([
                'barbearia_id' => $barbeariaId,
                'data' => $this->abrirData,
                'saldo_inicial' => $this->abrirSaldoInicial,
                'saldo_final' => $this->abrirSaldoInicial,
                'user_id_abertura' => Auth::guard('web')->id(),
            ]);
            $msg = 'Caixa aberto com sucesso!';
        }

        $this->abrirSaldoInicial = 0;
        $this->abrirData = now()->format('Y-m-d');
        $this->showAbrirPanel = false;

        $this->dispatch('notify', $msg, 'success');
    }

    public function toggleAbrir()
    {
        $this->showAbrirPanel = !$this->showAbrirPanel;
    }

    protected function getListeners()
    {
        return [
            'toggle-abrir' => 'toggleAbrir',
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'showFecharModal' && !$this->showFecharModal) {
            $this->fecharId = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.caixa-table', [
            'caixas' => $this->caixas,
            'barbearias' => $this->barbearias,
            'resumo' => $this->resumo,
            'totalBarbeiro' => $this->totalBarbeiro,
            'isTenant' => (bool) $this->tenantSlug,
        ]);
    }
}
