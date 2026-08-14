<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\CategoriaProduto;
use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Produto::query();

        if (request('q')) {
            $query->where(function ($q) {
                $q->where('nome', 'like', '%' . request('q') . '%')
                    ->orWhere('categoria', 'like', '%' . request('q') . '%');
            });
        }

        if (request('categoria')) {
            $query->where('categoria', request('categoria'));
        }

        if (request('status') === 'ativos') {
            $query->where('ativo', true);
        } elseif (request('status') === 'inativos') {
            $query->where('ativo', false);
        }

        $query = $this->applyTenantScope($query);
        $produtos = $query->orderBy('nome')->paginate(15)->withQueryString();
        $categorias = collect(CategoriaProduto::where('ativo', true)->orderBy('ordem')->orderBy('nome')->pluck('nome'))
            ->merge(Produto::select('categoria')->whereNotNull('categoria')
                ->where('categoria', '!=', '')->distinct()->pluck('categoria'))
            ->unique()->values();

        return view('admin.produtos.index', compact('produtos', 'categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'custo' => 'nullable|numeric|min:0',
            'estoque' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        if ($this->isTenantContext()) {
            $data['barbearia_id'] = $this->tenantId();
        }

        Produto::create($data);

        return redirect()->back()->with('success', 'Produto cadastrado com sucesso!');
    }

    public function update(Request $request, Produto $produto)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'custo' => 'nullable|numeric|min:0',
            'estoque' => 'nullable|integer|min:0',
            'ativo' => 'boolean',
        ]);

        $produto->update($data);

        return redirect()->back()->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Request $request, Produto $produto)
    {
        $produto->delete();
        return response()->json(['success' => true, 'message' => 'Produto excluído com sucesso']);
    }
}