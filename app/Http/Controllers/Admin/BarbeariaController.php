<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TenantScoped;
use App\Models\Barbearia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BarbeariaController extends Controller
{
    use TenantScoped;

    public function index()
    {
        $user = Auth::guard('web')->user();

        $query = Barbearia::with('parent', 'owner')->withCount('children');

        if ($this->isTenantContext()) {
            $tenant = $this->getTenant();
            $ids = $tenant->tenantTreeIds();
            $query->whereIn('id', $ids);
        } elseif (!$user->isSuperAdmin()) {
            $ownedIds = Barbearia::where('owner_id', $user->id)->pluck('id');
            $query->whereIn('id', $ownedIds)
                  ->orWhereIn('parent_id', $ownedIds);
        }

        $barbearias = $query->paginate(10);
        return view('admin.barbearias.index', compact('barbearias'));
    }

    public function create()
    {
        $matrizesQuery = Barbearia::whereNull('parent_id')->orderBy('nome');

        if ($this->isTenantContext()) {
            $matrizesQuery->where('id', $this->tenantId());
        }

        $matrizes = $matrizesQuery->get();
        return view('admin.barbearias.form', ['edit' => false, 'matrizes' => $matrizes]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:barbearias,id',
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:barbearias,slug',
            'descricao' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'owner_name' => 'required_without:parent_id|string|max:255',
            'owner_email' => 'required_without:parent_id|email|unique:users,email',
            'owner_password' => 'required_without:parent_id|min:6',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('barbearias/logos', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('barbearias/backgrounds', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nome']);
            $base = $data['slug'];
            $counter = 1;
            while (Barbearia::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $base . '-' . $counter++;
            }
        }

        $barbearia = Barbearia::create($data);

        if (!$request->parent_id) {
            $user = User::create([
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => Hash::make($request->owner_password),
            ]);

            $proprietarioRole = Role::where('name', 'proprietario')->where('guard_name', 'web')->first();
            if ($proprietarioRole) {
                $user->assignRole($proprietarioRole);
            }

            $barbearia->update(['owner_id' => $user->id]);
        }

        $route = $this->isTenantContext()
            ? route('tenant.admin.barbearias.index', $this->getTenant()->slug)
            : route('admin.barbearias.index');

        return redirect()->to($route)->with('success', 'Barbearia cadastrada com sucesso!');
    }

    public function edit(Barbearia $barbearia)
    {
        $matrizesQuery = Barbearia::whereNull('parent_id')->where('id', '!=', $barbearia->id)->orderBy('nome');

        if ($this->isTenantContext()) {
            $matrizesQuery->where('id', $this->tenantId());
        }

        $matrizes = $matrizesQuery->get();
        $proprietarios = User::role('proprietario')->orderBy('name')->get();
        $barbearia->load('owner');
        return view('admin.barbearias.form', [
            'edit' => true,
            'barbearia' => $barbearia,
            'matrizes' => $matrizes,
            'proprietarios' => $proprietarios,
        ]);
    }

    public function update(Request $request, Barbearia $barbearia)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:barbearias,id|different:id',
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:barbearias,slug,' . $barbearia->id,
            'descricao' => 'nullable|string',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remover_logo' => 'boolean',
            'remover_background' => 'boolean',
            'owner_id' => 'nullable|exists:users,id',
            'owner_email' => 'nullable|email',
            'owner_password' => 'nullable|min:6',
        ]);

        if ($request->filled('owner_id')) {
            $data['owner_id'] = $request->owner_id;
        }

        if ($request->filled('owner_email') && $request->owner_email !== $barbearia->owner?->email) {
            $owner = $barbearia->owner;
            if ($owner) {
                $owner->update(['email' => $request->owner_email]);
                if ($request->filled('owner_password')) {
                    $owner->update(['password' => Hash::make($request->owner_password)]);
                }
            }
        } elseif ($request->filled('owner_password') && $barbearia->owner) {
            $barbearia->owner->update(['password' => Hash::make($request->owner_password)]);
        }

        if ($request->boolean('remover_logo') && $barbearia->logo) {
            \Storage::disk('public')->delete($barbearia->logo);
            $data['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($barbearia->logo) \Storage::disk('public')->delete($barbearia->logo);
            $data['logo'] = $request->file('logo')->store('barbearias/logos', 'public');
        }

        if ($request->boolean('remover_background') && $barbearia->background_image) {
            \Storage::disk('public')->delete($barbearia->background_image);
            $data['background_image'] = null;
        } elseif ($request->hasFile('background_image')) {
            if ($barbearia->background_image) \Storage::disk('public')->delete($barbearia->background_image);
            $data['background_image'] = $request->file('background_image')->store('barbearias/backgrounds', 'public');
        }

        $barbearia->update($data);

        $route = $this->isTenantContext()
            ? route('tenant.admin.barbearias.index', $this->getTenant()->slug)
            : route('admin.barbearias.index');

        return redirect()->to($route)->with('success', 'Barbearia atualizada com sucesso!');
    }

    public function destroy(Barbearia $barbearia)
    {
        if ($barbearia->logo) \Storage::disk('public')->delete($barbearia->logo);
        if ($barbearia->background_image) \Storage::disk('public')->delete($barbearia->background_image);
        $barbearia->delete();
        return response()->json(['success' => true, 'message' => 'Barbearia excluída com sucesso']);
    }
}
