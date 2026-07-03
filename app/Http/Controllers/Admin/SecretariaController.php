<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbearia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SecretariaController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $secretarias = User::role('secretaria')
            ->with('barbearias')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.secretarias.index', compact('secretarias'));
    }

    public function create()
    {
        $barbearias = $this->getTenantBarbearias();
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0]);

        return view('admin.secretarias.form', [
            'edit' => false,
            'barbearias' => $barbearias,
            'permissions' => $permissions,
            'secretariaPermissions' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'barbearias' => 'required|array',
            'barbearias.*' => 'exists:barbearias,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $secretariaRole = Role::where('name', 'secretaria')->where('guard_name', 'web')->first();
        if ($secretariaRole) {
            $user->assignRole($secretariaRole);
        }

        $user->barbearias()->sync($data['barbearias']);

        if (!empty($data['permissions'])) {
            $perms = Permission::whereIn('id', $data['permissions'])->where('guard_name', 'web')->get();
            $user->syncPermissions($perms);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.secretarias.index', $this->getTenant()->slug)
            : route('admin.secretarias.index');

        return redirect()->to($route)->with('success', 'Secretária cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $secretaria = User::with('barbearias', 'permissions')->findOrFail($id);

        if (!$secretaria->hasRole('secretaria')) {
            abort(404);
        }

        $barbearias = $this->getTenantBarbearias();
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => explode('.', $p->name)[0]);

        $secretariaPermissions = $secretaria->permissions->pluck('id')->toArray();

        return view('admin.secretarias.form', [
            'edit' => true,
            'secretaria' => $secretaria,
            'barbearias' => $barbearias,
            'permissions' => $permissions,
            'secretariaPermissions' => $secretariaPermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $secretaria = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $secretaria->id,
            'password' => 'nullable|min:6',
            'barbearias' => 'required|array',
            'barbearias.*' => 'exists:barbearias,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $secretaria->update($updateData);

        $secretaria->barbearias()->sync($data['barbearias']);

        if (!empty($data['permissions'])) {
            $perms = Permission::whereIn('id', $data['permissions'])->where('guard_name', 'web')->get();
            $secretaria->syncPermissions($perms);
        } else {
            $secretaria->syncPermissions([]);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.secretarias.index', $this->getTenant()->slug)
            : route('admin.secretarias.index');

        return redirect()->to($route)->with('success', 'Secretária atualizada com sucesso!');
    }

    public function toggleAtivo($id)
    {
        $secretaria = User::findOrFail($id);

        $pivot = $secretaria->barbearias()->first();
        if ($pivot) {
            $barbeariaId = $pivot->id;
            $currentAtivo = $secretaria->barbearias()->where('barbearia_id', $barbeariaId)->first()->pivot->ativo;
            $secretaria->barbearias()->updateExistingPivot($barbeariaId, ['ativo' => !$currentAtivo]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $secretaria = User::findOrFail($id);

        if ($secretaria->hasRole('secretaria')) {
            $secretaria->removeRole('secretaria');
        }

        $secretaria->barbearias()->detach();
        $secretaria->syncPermissions([]);
        $secretaria->delete();

        return response()->json(['success' => true, 'message' => 'Secretária excluída com sucesso']);
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
