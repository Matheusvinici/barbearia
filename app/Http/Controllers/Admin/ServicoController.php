<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbeiro;
use App\Models\Barbearia;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicoController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Servico::query();
        $query = $this->applyTenantScope($query);
        $servicos = $query->paginate(10);
        return view('admin.servicos.index', compact('servicos'));
    }

    public function create()
    {
        $data = ['edit' => false];

        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
            $barbearias = collect([$tenant]);
            $barbeiros = Barbeiro::where('ativo', true)
                ->where('barbearia_id', $tenant->id)
                ->get();
            $data['barbearias'] = $barbearias;
            $data['barbeiros'] = $barbeiros;
        } else {
            $user = Auth::user();
            $barbearias = Barbearia::where('owner_id', $user->id)->get();
            $barbeiros = Barbeiro::where('ativo', true)
                ->whereIn('barbearia_id', $barbearias->pluck('id'))
                ->get();
            $data['barbearias'] = $barbearias;
            $data['barbeiros'] = $barbeiros;
        }

        return view('admin.servicos.form', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:5',
            'ativo' => 'boolean',
        ];

        if ($this->isTenantContext()) {
            $rules['barbearia_id'] = 'nullable|exists:barbearias,id';
        } else {
            $rules['barbearia_id'] = 'required|exists:barbearias,id';
        }

        $data = $request->validate($rules);

        if ($this->isTenantContext()) {
            $data['barbearia_id'] = $this->tenantId();
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('servicos', 'public');
        }

        $servico = Servico::create($data);

        if ($request->has('barbeiros')) {
            $servico->barbeiros()->sync($request->barbeiros);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.servicos.index', $this->getTenant()->slug)
            : route('admin.servicos.index');

        return redirect()->to($route)->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function show(Servico $servico)
    {
        return view('admin.servicos.show', compact('servico'));
    }

    public function edit(Servico $servico)
    {
        $servico->load('barbeiros');
        $data = ['edit' => true, 'servico' => $servico];

        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
            $barbearias = collect([$tenant]);
            $barbeiros = Barbeiro::where('ativo', true)
                ->where('barbearia_id', $tenant->id)
                ->get();
        } else {
            $user = Auth::user();
            $barbearias = Barbearia::where('owner_id', $user->id)->get();
            $barbeiros = Barbeiro::where('ativo', true)
                ->whereIn('barbearia_id', $barbearias->pluck('id'))
                ->get();
        }

        $data['barbearias'] = $barbearias;
        $data['barbeiros'] = $barbeiros;

        return view('admin.servicos.form', $data);
    }

    public function update(Request $request, Servico $servico)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:5',
            'ativo' => 'boolean',
            'remover_foto' => 'boolean',
        ]);

        if ($request->boolean('remover_foto') && $servico->foto) {
            \Storage::disk('public')->delete($servico->foto);
            $data['foto'] = null;
        } elseif ($request->hasFile('foto')) {
            if ($servico->foto) {
                \Storage::disk('public')->delete($servico->foto);
            }
            $data['foto'] = $request->file('foto')->store('servicos', 'public');
        }

        $servico->update($data);

        if ($request->has('barbeiros')) {
            $servico->barbeiros()->sync($request->barbeiros);
        } else {
            $servico->barbeiros()->detach();
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.servicos.index', $this->getTenant()->slug)
            : route('admin.servicos.index');

        return redirect()->to($route)->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Servico $servico)
    {
        if ($servico->foto) {
            \Storage::disk('public')->delete($servico->foto);
        }
        $servico->delete();
        return response()->json(['success' => true, 'message' => 'Serviço excluído com sucesso']);
    }

    public function replicar(Request $request, Servico $servico)
    {
        $tenant = $this->getTenant();
        if (!$tenant || !$tenant->isMatriz()) {
            return redirect()->back()->with('error', 'Apenas a matriz pode replicar serviços.');
        }

        $filiais = $tenant->filiais;
        $count = 0;

        foreach ($filiais as $filial) {
            $exists = Servico::where('barbearia_id', $filial->id)
                ->where('nome', $servico->nome)
                ->exists();

            if (!$exists) {
                Servico::create([
                    'barbearia_id' => $filial->id,
                    'nome' => $servico->nome,
                    'descricao' => $servico->descricao,
                    'preco' => $servico->preco,
                    'duracao_minutos' => $servico->duracao_minutos,
                    'ativo' => $servico->ativo,
                ]);
                $count++;
            }
        }

        $route = route('tenant.admin.servicos.index', $tenant->slug);
        return redirect()->to($route)->with('success', "Serviço replicado para {$count} filial(is) com sucesso!");
    }
}
