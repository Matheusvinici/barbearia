<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Servico;
use Illuminate\Http\Request;

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
        return view('admin.servicos.form', ['edit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'preco' => 'required|numeric|min:0',
            'duracao_minutos' => 'required|integer|min:5',
            'ativo' => 'boolean',
        ]);

        if ($this->isTenantContext()) {
            $data['barbearia_id'] = $this->tenantId();
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('servicos', 'public');
        }

        Servico::create($data);

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
        return view('admin.servicos.form', ['edit' => true, 'servico' => $servico]);
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
}
