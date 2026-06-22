<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = [
            'barbearia.view', 'barbearia.create', 'barbearia.edit', 'barbearia.delete',
            'barbeiro.view', 'barbeiro.create', 'barbeiro.edit', 'barbeiro.delete',
            'servico.view', 'servico.create', 'servico.edit', 'servico.delete',
            'cliente.view', 'cliente.create', 'cliente.edit', 'cliente.delete',
            'agendamento.view', 'agendamento.create', 'agendamento.edit', 'agendamento.delete',
            'agendamento.confirmar', 'agendamento.realizar', 'agendamento.cancelar',
            'plano.view', 'plano.create', 'plano.edit', 'plano.delete',
            'relatorio.view', 'relatorio.faturamento',
            'role.view', 'role.create', 'role.edit', 'role.delete',
            'configuracao.edit',
            'despesa.view', 'despesa.create', 'despesa.edit', 'despesa.delete',
            'caixa.view', 'caixa.abrir', 'caixa.fechar',
        ];

        // Admin guard permissions
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::where('guard_name', 'web')->get());

        $user = User::first();
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        // Proprietario - same full access as admin, on web guard
        $proprietario = Role::firstOrCreate(['name' => 'proprietario', 'guard_name' => 'web']);
        $proprietario->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Remove old proprietario from barbeiro guard if it exists
        $oldProprietario = Role::where('name', 'proprietario')->where('guard_name', 'barbeiro')->first();
        if ($oldProprietario) {
            $oldProprietario->delete();
        }

        // Barbeiro guard permissions (subset for employees)
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'barbeiro']);
        }

        // Funcionario - limited to own appointments
        $funcionario = Role::firstOrCreate(['name' => 'funcionario', 'guard_name' => 'barbeiro']);
        $funcionario->syncPermissions([
            Permission::where('guard_name', 'barbeiro')->where('name', 'agendamento.view')->first(),
            Permission::where('guard_name', 'barbeiro')->where('name', 'agendamento.confirmar')->first(),
            Permission::where('guard_name', 'barbeiro')->where('name', 'agendamento.realizar')->first(),
            Permission::where('guard_name', 'barbeiro')->where('name', 'agendamento.cancelar')->first(),
        ]);

        $this->command->info('Permissões criadas com sucesso!');
    }
}
