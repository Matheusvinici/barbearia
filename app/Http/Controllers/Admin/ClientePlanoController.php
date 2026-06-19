<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ClientePlano;
use App\Models\ClientePlanoUso;
use App\Models\Plano;
use Illuminate\Http\Request;

class ClientePlanoController extends Controller
{
    public function index()
    {
        $vinculos = ClientePlano::with(['cliente', 'plano'])->paginate(15);
        $planos = Plano::where('ativo', true)->get();
        $clientes = Cliente::orderBy('nome')->get();
        return view('admin.clientes-planos.index', compact('vinculos', 'planos', 'clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'plano_id' => 'required|exists:planos,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'observacoes' => 'nullable|string',
        ]);

        ClientePlano::create($data);

        return redirect()->route('admin.clientes-planos.index')->with('success', 'Cliente vinculado ao plano com sucesso!');
    }

    public function edit(ClientePlano $clientesPlano)
    {
        $vinculo = $clientesPlano->load('cliente', 'plano');
        $planos = Plano::where('ativo', true)->get();
        return view('admin.clientes-planos.edit', compact('vinculo', 'planos'));
    }

    public function update(Request $request, ClientePlano $clientesPlano)
    {
        $data = $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativo' => 'boolean',
            'observacoes' => 'nullable|string',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);
        $clientesPlano->update($data);

        return redirect()->route('admin.clientes-planos.index')->with('success', 'Vínculo atualizado com sucesso!');
    }

    public function destroy(ClientePlano $clientesPlano)
    {
        $clientesPlano->delete();
        return response()->json(['success' => true, 'message' => 'Vínculo excluído com sucesso']);
    }

    public function dashboard()
    {
        $vinculos = ClientePlano::with([
            'cliente',
            'plano.quotas.servico',
            'usos.servico',
        ])->where('ativo', true)->get();

        $dados = $vinculos->map(function ($cp) {
            $quotas = $cp->plano->quotas->map(function ($q) use ($cp) {
                $usos = $cp->usos->where('servico_id', $q->servico_id)->count();
                $dentro = $usos < $q->quantidade;
                return [
                    'servico' => $q->servico->nome,
                    'contratada' => $q->quantidade,
                    'utilizada' => $usos,
                    'restante' => max(0, $q->quantidade - $usos),
                    'dentro_da_cota' => $dentro,
                ];
            });

            $totalUsos = $cp->usos->count();
            $totalQuotas = $cp->plano->quotas->sum('quantidade');
            $todasDentro = $quotas->every('dentro_da_cota');

            return [
                'id' => $cp->id,
                'cliente' => $cp->cliente,
                'plano' => $cp->plano,
                'data_inicio' => $cp->data_inicio,
                'data_fim' => $cp->data_fim,
                'quotas' => $quotas,
                'total_utilizada' => $totalUsos,
                'total_contratada' => $totalQuotas,
                'todas_dentro' => $todasDentro,
            ];
        });

        return view('admin.clientes-planos.dashboard', compact('dados'));
    }
}
