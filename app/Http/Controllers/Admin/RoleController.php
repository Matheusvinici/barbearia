<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $guards = ['web', 'barbeiro'];
        return view('admin.roles.index', compact('roles', 'guards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,guard_name,' . ($request->guard_name ?? 'web'),
            'guard_name' => 'required|in:web,barbeiro',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => $request->guard_name]);

        if ($request->permissions) {
            $permIds = array_map('intval', $request->permissions);
            $permissions = Permission::whereIn('id', $permIds)
                ->where('guard_name', $request->guard_name)
                ->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Papel criado com sucesso!');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::where('guard_name', $role->guard_name)->orderBy('name')->get();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id . ',id,guard_name,' . $role->guard_name,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $request->name]);

        $permIds = $request->permissions ? array_map('intval', $request->permissions) : [];
        $permissions = Permission::whereIn('id', $permIds)
            ->where('guard_name', $role->guard_name)
            ->get();
        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')->with('success', 'Papel atualizado com sucesso!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return response()->json(['success' => false, 'message' => 'Não é possível excluir o papel admin']);
        }
        $role->delete();
        return response()->json(['success' => true, 'message' => 'Papel excluído com sucesso']);
    }
}
