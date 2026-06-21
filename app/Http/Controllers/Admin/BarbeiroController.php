<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\BarbeiroHorario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BarbeiroController extends Controller
{
    public function index()
    {
        $barbeiros = Barbeiro::with('barbearia', 'roles')->paginate(10);
        return view('admin.barbeiros.index', compact('barbeiros'));
    }

    public function create()
    {
        $barbearias = Barbearia::orderBy('nome')->get();
        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça',
            3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
        ];
        $roles = Role::where('guard_name', 'barbeiro')->orderBy('name')->get();
        return view('admin.barbeiros.form', [
            'edit' => false,
            'barbearias' => $barbearias,
            'diasSemana' => $diasSemana,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbeiros,email',
            'password' => 'required|min:6',
            'telefone' => 'nullable|string|max:20',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'ativo' => 'boolean',
            'horarios' => 'nullable|array',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $barbeiro = Barbeiro::create($data);

        if ($request->roles) {
            $barbeiro->syncRoles(Role::whereIn('id', $request->roles)->get());
        }

        if ($request->horarios) {
            foreach ($request->horarios as $horario) {
                if (!empty($horario['hora_inicio']) && !empty($horario['hora_fim'])) {
                    BarbeiroHorario::create([
                        'barbeiro_id' => $barbeiro->id,
                        'dia_semana' => $horario['dia_semana'],
                        'periodo' => $horario['periodo'] ?? null,
                        'hora_inicio' => $horario['hora_inicio'],
                        'hora_fim' => $horario['hora_fim'],
                        'ativo' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.barbeiros.index')->with('success', 'Barbeiro cadastrado com sucesso!');
    }

    public function show(Barbeiro $barbeiro)
    {
        $barbeiro->load('barbearia', 'horarios', 'roles');
        return view('admin.barbeiros.show', compact('barbeiro'));
    }

    public function edit(Barbeiro $barbeiro)
    {
        $barbeiro->load('horarios', 'roles');
        $barbearias = Barbearia::orderBy('nome')->get();
        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça',
            3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
        ];
        $roles = Role::where('guard_name', 'barbeiro')->orderBy('name')->get();
        return view('admin.barbeiros.form', [
            'edit' => true,
            'barbeiro' => $barbeiro,
            'barbearias' => $barbearias,
            'diasSemana' => $diasSemana,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, Barbeiro $barbeiro)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbeiros,email,' . $barbeiro->id,
            'password' => 'nullable|min:6',
            'telefone' => 'nullable|string|max:20',
            'comissao_percentual' => 'required|numeric|min:0|max:100',
            'barbearia_id' => 'nullable|exists:barbearias,id',
            'ativo' => 'boolean',
            'horarios' => 'nullable|array',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $barbeiro->update($data);

        $barbeiro->syncRoles($request->roles ? Role::whereIn('id', $request->roles)->get() : []);

        $barbeiro->horarios()->delete();
        if ($request->horarios) {
            foreach ($request->horarios as $horario) {
                if (!empty($horario['hora_inicio']) && !empty($horario['hora_fim'])) {
                    BarbeiroHorario::create([
                        'barbeiro_id' => $barbeiro->id,
                        'dia_semana' => $horario['dia_semana'],
                        'periodo' => $horario['periodo'] ?? null,
                        'hora_inicio' => $horario['hora_inicio'],
                        'hora_fim' => $horario['hora_fim'],
                        'ativo' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.barbeiros.index')->with('success', 'Barbeiro atualizado com sucesso!');
    }

    public function destroy(Barbeiro $barbeiro)
    {
        $barbeiro->delete();
        return response()->json(['success' => true, 'message' => 'Barbeiro excluído com sucesso']);
    }
}
