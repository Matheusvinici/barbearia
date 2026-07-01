<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbearia;
use App\Models\Barbeiro;
use App\Models\BarbeiroHorario;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BarbeiroController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $query = Barbeiro::with('barbearia', 'roles');
        $query = $this->applyTenantScope($query);
        $barbeiros = $query->paginate(10);
        return view('admin.barbeiros.index', compact('barbeiros'));
    }

    public function create()
    {
        $barbearias = $this->getTenantBarbearias();
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
            'criar_como_admin' => 'nullable|boolean',
            'tipo' => 'required|in:proprietario,funcionario',
            'especialidades' => 'nullable|string|max:500',
            'barbearias' => 'nullable|array',
            'barbearias.*' => 'exists:barbearias,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($this->isTenantContext()) {
            $allowedIds = $this->tenantIds();
            if (!empty($data['barbearias'])) {
                $invalid = array_diff($data['barbearias'], $allowedIds);
                if (!empty($invalid)) {
                    return back()->withErrors(['barbearias' => 'Barbearia inválida.'])->withInput();
                }
            }
            if ($data['barbearia_id'] && !in_array($data['barbearia_id'], $allowedIds)) {
                return back()->withErrors(['barbearia_id' => 'Barbearia inválida.'])->withInput();
            }
        }

        $barbeiro = Barbeiro::create($data);

        if ($request->filled('barbearias')) {
            $barbeiro->barbearias()->sync($request->barbearias);
        }

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

        if ($request->criar_como_admin) {
            $existingUser = User::where('email', $request->email)->first();
            if (!$existingUser) {
                $user = User::create([
                    'name' => $request->nome,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $proprietarioRole = Role::where('name', 'proprietario')->where('guard_name', 'web')->first();
                if ($proprietarioRole) {
                    $user->assignRole($proprietarioRole);
                }
            }
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.barbeiros.index', $this->getTenant()->slug)
            : route('admin.barbeiros.index');

        return redirect()->to($route)->with('success', 'Barbeiro cadastrado com sucesso!');
    }

    public function show(Barbearia $barbearia, int $id)
    {
        $barbeiro = Barbeiro::with('barbearia', 'horarios', 'roles', 'barbearias')->findOrFail($id);
        return view('admin.barbeiros.show', compact('barbeiro'));
    }

    public function edit(Barbearia $barbearia, int $id)
    {
        $barbeiro = Barbeiro::with('horarios', 'roles', 'barbearias')->findOrFail($id);
        $barbearias = $this->getTenantBarbearias();
        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça',
            3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
        ];
        $roles = Role::where('guard_name', 'barbeiro')->orderBy('name')->get();
        $userExists = User::where('email', $barbeiro->email)->exists();
        return view('admin.barbeiros.form', [
            'edit' => true,
            'barbeiro' => $barbeiro,
            'barbearias' => $barbearias,
            'diasSemana' => $diasSemana,
            'roles' => $roles,
            'userExists' => $userExists,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $barbeiro = Barbeiro::findOrFail($id);

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
            'tipo' => 'required|in:proprietario,funcionario',
            'especialidades' => 'nullable|string|max:500',
            'barbearias' => 'nullable|array',
            'barbearias.*' => 'exists:barbearias,id',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $barbeiro->update($data);

        if ($request->filled('barbearias')) {
            $barbeiro->barbearias()->sync($request->barbearias);
        } else {
            $barbeiro->barbearias()->sync([]);
        }

        if ($request->criar_como_admin) {
            $existingUser = User::where('email', $request->email)->first();
            if (!$existingUser) {
                $plainPassword = $request->filled('password') ? $request->password : Str::random(12);
                $user = User::create([
                    'name' => $request->nome,
                    'email' => $request->email,
                    'password' => Hash::make($plainPassword),
                ]);
                $proprietarioRole = Role::where('name', 'proprietario')->where('guard_name', 'web')->first();
                if ($proprietarioRole) {
                    $user->assignRole($proprietarioRole);
                }
                if (!$request->filled('password')) {
                    session()->flash('info', "Conta admin criada com senha gerada: <strong>$plainPassword</strong> — peça ao profissional para alterar.");
                }
            }
        }

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

        $route = $this->isTenantContext()
            ? route('tenant.admin.barbeiros.index', $this->getTenant()->slug)
            : route('admin.barbeiros.index');

        return redirect()->to($route)->with('success', 'Barbeiro atualizado com sucesso!');
    }

    public function destroy(int $id)
    {
        $barbeiro = Barbeiro::findOrFail($id);
        $barbeiro->delete();
        return response()->json(['success' => true, 'message' => 'Barbeiro excluído com sucesso']);
    }

    private function getTenantBarbearias()
    {
        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
            $ids = $tenant->tenantTreeIds();
            return Barbearia::whereIn('id', $ids)->orderBy('nome')->get();
        }

        $user = Auth::guard('web')->user();
        if ($user && !$user->isSuperAdmin()) {
            $ownedIds = Barbearia::where('owner_id', $user->id)->pluck('id');
            return Barbearia::whereIn('id', $ownedIds)
                ->orWhereIn('parent_id', $ownedIds)
                ->orderBy('nome')
                ->get();
        }

        return Barbearia::orderBy('nome')->get();
    }
}
