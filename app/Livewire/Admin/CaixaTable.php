<?php

namespace App\Livewire\Admin;

use App\Models\Barbearia;
use App\Models\Caixa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CaixaTable extends Component
{
    public $tenantSlug;
    public $tenantIds = [];
    public $barbeariaFilter;

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
        $this->barbeariaFilter = request('barbearia_id');
        if ($tenantSlug) {
            $tenant = Barbearia::where('slug', $tenantSlug)->first();
            if ($tenant) {
                $this->tenantIds = $tenant->tenantTreeIds();
            }
        }
    }

    public function getBarbeariasProperty()
    {
        return $this->getAvailableBarbearias();
    }

    public function getCaixasProperty()
    {
        $barbearias = $this->getAvailableBarbearias();
        $ids = $barbearias->pluck('id')->toArray();

        $query = Caixa::with(['usuarioAbertura', 'usuarioFechamento', 'barbearia'])
            ->orderBy('data', 'desc');

        if ($this->barbeariaFilter) {
            $query->where('barbearia_id', $this->barbeariaFilter);
        } elseif (!empty($ids)) {
            $query->where(function ($q) use ($ids) {
                $q->whereIn('barbearia_id', $ids)->orWhereNull('barbearia_id');
            });
        }

        return $query->paginate(20);
    }

    public function updatedBarbeariaFilter()
    {
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
            if (in_array((int)$this->editBarbeariaId, $validIds)) {
                $data['barbearia_id'] = (int)$this->editBarbeariaId;
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

        $caixa->update([
            'fechado' => false,
            'user_id_fechamento' => null,
        ]);

        $this->dispatch('notify', 'Caixa reaberto com sucesso!', 'success');
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
            $this->dispatch('notify', 'Caixa já aberto para esta data para esta unidade.', 'error');
            return;
        }

        Caixa::create([
            'barbearia_id' => $barbeariaId,
            'data' => $this->abrirData,
            'saldo_inicial' => $this->abrirSaldoInicial,
            'saldo_final' => $this->abrirSaldoInicial,
            'user_id_abertura' => Auth::guard('web')->id(),
        ]);

        $this->abrirSaldoInicial = 0;
        $this->abrirData = now()->format('Y-m-d');
        $this->showAbrirPanel = false;

        $this->dispatch('notify', 'Caixa aberto com sucesso!', 'success');
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
            'isTenant' => (bool) $this->tenantSlug,
        ]);
    }
}
