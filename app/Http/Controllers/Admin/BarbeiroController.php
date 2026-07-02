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
        if (!$this->isTenantContext()) {
            $user = Auth::guard('web')->user();
            if ($user && !$user->isSuperAdmin()) {
                $ids = $user->ownedBarbearias()->get()->flatMap(fn($b) => $b->tenantTreeIds())->unique()->values()->toArray();
                if (!empty($ids)) {
                    $query->whereIn('barbearia_id', $ids);
                }
            }
        }
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
        $users = User::where(function ($q) {
            if ($this->isTenantContext()) {
                $ids = $this->tenantIds();
                $q->whereHas('ownedBarbearias', function ($q2) use ($ids) {
                    $q2->whereIn('id', $ids);
                });
            }
        })->orderBy('name')->get();
        return view('admin.barbeiros.form', [
            'edit' => false,
            'barbearias' => $barbearias,
            'diasSemana' => $diasSemana,
            'roles' => $roles,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'nullable|exists:users,id',
            'nome' => 'required|string|max:255',
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
        ];

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if (!$user) {
                return back()->withErrors(['user_id' => 'Usuário não encontrado.'])->withInput();
            }
            $existing = Barbeiro::where('email', $user->email)->first();
            if ($existing && $existing->user_id && $existing->user_id != $request->user_id) {
                return back()->withErrors(['email' => 'Este e-mail já está em uso por outro barbeiro.'])->withInput();
            }
            $rules['email'] = 'required|email';
            $data = $request->validate($rules);
            $data['email'] = $user->email;
            $data['nome'] = $user->name;
            $data['password'] = Hash::make(uniqid());
            $barbeiro = $existing;
        } else {
            $rules['email'] = 'required|email|unique:barbeiros,email';
            $rules['password'] = 'required|min:6';
            $data = $request->validate($rules);
            $data['password'] = Hash::make($data['password']);
            $barbeiro = null;
        }

        if ($this->isTenantContext()) {
            $allowedIds = $this->tenantIds();
            if (!empty($data['barbearias'])) {
                $invalid = array_diff($data['barbearias'], $allowedIds);
                if (!empty($invalid)) {
                    return back()->withErrors(['barbearias' => 'Barbearia inválida.'])->withInput();
                }
                if (empty($data['barbearia_id'])) {
                    $data['barbearia_id'] = $data['barbearias'][0];
                }
            } elseif (empty($data['barbearia_id'])) {
                $data['barbearia_id'] = $this->getTenant()->id;
            }
            if (!empty($data['barbearia_id']) && !in_array($data['barbearia_id'], $allowedIds)) {
                return back()->withErrors(['barbearia_id' => 'Barbearia inválida.'])->withInput();
            }
        }

        try {
            if ($barbeiro) {
                $barbeiro->update($data);
            } else {
                $barbeiro = Barbeiro::create($data);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return back()->withErrors(['email' => 'Este e-mail já está cadastrado para outro barbeiro.'])->withInput();
            }
            throw $e;
        }

        if ($request->filled('barbearias')) {
            $barbeiro->barbearias()->sync($request->barbearias);
        }

        $funcionarioRole = Role::where('name', 'funcionario')->where('guard_name', 'barbeiro')->first();
        if ($funcionarioRole) {
            $barbeiro->syncRoles([$funcionarioRole]);
        }

        if ($request->horarios) {
            if ($barbeiro->horarios()->exists()) {
                $barbeiro->horarios()->delete();
            }
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
        $users = User::where(function ($q) {
            if ($this->isTenantContext()) {
                $ids = $this->tenantIds();
                $q->whereHas('ownedBarbearias', function ($q2) use ($ids) {
                    $q2->whereIn('id', $ids);
                });
            }
        })->orderBy('name')->get();
        return view('admin.barbeiros.form', [
            'edit' => true,
            'barbeiro' => $barbeiro,
            'barbearias' => $barbearias,
            'diasSemana' => $diasSemana,
            'roles' => $roles,
            'userExists' => $userExists,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Barbearia $barbearia, int $id)
    {
        $barbeiro = Barbeiro::findOrFail($id);

        $rules = [
            'user_id' => 'nullable|exists:users,id',
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
        ];

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if (!$user) {
                return back()->withErrors(['user_id' => 'Usuário não encontrado.'])->withInput();
            }
            $rules['email'] = 'required|email';
            $data = $request->validate($rules);
            $data['email'] = $user->email;
            $data['nome'] = $user->name;
        } else {
            $rules['nome'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:barbeiros,email,' . $barbeiro->id;
            $rules['password'] = 'nullable|min:6';
            $data = $request->validate($rules);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
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

        $funcionarioRole = Role::where('name', 'funcionario')->where('guard_name', 'barbeiro')->first();
        if ($funcionarioRole) {
            $barbeiro->syncRoles([$funcionarioRole]);
        }

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

    public function destroy(Barbearia $barbearia, int $id)
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
