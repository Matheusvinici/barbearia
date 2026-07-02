<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbearia;
use App\Models\Caixa;
use App\Models\CaixaMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaixaController extends Controller
{
    use TenantScoped;

    private function getAvailableBarbearias()
    {
        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
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

    private function authorizeCaixa(Caixa $caixa): void
    {
        $barbearias = $this->getAvailableBarbearias();
        $ids = $barbearias->pluck('id')->toArray();

        if (!in_array($caixa->barbearia_id, $ids) && $caixa->barbearia_id !== null) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $barbearias = $this->getAvailableBarbearias();

        $query = Caixa::with(['usuarioAbertura', 'usuarioFechamento', 'barbearia'])
            ->orderBy('data', 'desc');

        $ids = $barbearias->pluck('id')->toArray();

        $barbeariaFilter = $request->query('barbearia_id');
        if ($barbeariaFilter) {
            $query->where('barbearia_id', $barbeariaFilter);
        } elseif (!empty($ids)) {
            $query->where(function ($q) use ($ids) {
                $q->whereIn('barbearia_id', $ids)
                  ->orWhereNull('barbearia_id');
            });
        }

        $caixas = $query->paginate(20);

        return view('admin.caixa.index', compact('caixas', 'barbearias', 'barbeariaFilter'));
    }

    public function show($caixa)
    {
        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route);
    }

    public function edit($caixa)
    {
        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route);
    }

    public function update(Request $request, $caixa)
    {
        $caixa = Caixa::findOrFail($caixa);
        $this->authorizeCaixa($caixa);

        $request->validate([
            'saldo_inicial' => 'required|numeric|min:0',
            'total_entradas' => 'required|numeric|min:0',
            'total_saidas' => 'required|numeric|min:0',
            'saldo_final' => 'required|numeric',
            'observacoes' => 'nullable|string',
        ]);

        $caixa->update([
            'saldo_inicial' => $request->saldo_inicial,
            'total_entradas' => $request->total_entradas,
            'total_saidas' => $request->total_saidas,
            'saldo_final' => $request->saldo_final,
            'observacoes' => $request->observacoes,
        ]);

        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route)->with('success', 'Caixa atualizado com sucesso!');
    }

    public function abrir(Request $request)
    {
        $barbearias = $this->getAvailableBarbearias();
        $barbeariaIds = $barbearias->pluck('id')->toArray();

        $rules = [
            'data' => 'required|date',
            'saldo_inicial' => 'required|numeric|min:0',
        ];

        if ($barbearias->count() > 1) {
            $rules['barbearia_id'] = 'required|in:' . implode(',', $barbeariaIds);
        } else {
            $rules['barbearia_id'] = 'in:' . implode(',', $barbeariaIds);
        }

        $request->validate($rules);

        $barbeariaId = $request->barbearia_id ?? ($barbeariaIds[0] ?? null);

        $existing = Caixa::whereDate('data', $request->data)
            ->where('barbearia_id', $barbeariaId)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Caixa já aberto para esta data para esta unidade.');
        }

        Caixa::create([
            'barbearia_id' => $barbeariaId,
            'data' => $request->data,
            'saldo_inicial' => $request->saldo_inicial,
            'saldo_final' => $request->saldo_inicial,
            'user_id_abertura' => Auth::guard('web')->id(),
        ]);

        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route)->with('success', 'Caixa aberto com sucesso!');
    }

    public function fechar(Request $request, $caixa)
    {
        $caixa = Caixa::findOrFail($caixa);
        $this->authorizeCaixa($caixa);

        $request->validate([
            'saldo_informado' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $caixa->update([
            'saldo_final' => $request->saldo_informado,
            'fechado' => true,
            'observacoes' => $request->observacoes,
            'user_id_fechamento' => Auth::guard('web')->id(),
        ]);

        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route)->with('success', 'Caixa fechado com sucesso!');
    }

    public function reabrir(Request $request, $caixa)
    {
        $caixa = Caixa::findOrFail($caixa);
        $this->authorizeCaixa($caixa);

        if (!$caixa->fechado) {
            return redirect()->back()->with('error', 'Caixa já está aberto.');
        }

        $caixa->update([
            'fechado' => false,
            'user_id_fechamento' => null,
        ]);

        $route = $this->isTenantContext()
            ? route('tenant.admin.caixa.index', $this->getTenant()->slug)
            : route('admin.caixa.index');

        return redirect()->to($route)->with('success', 'Caixa reaberto com sucesso!');
    }
}
