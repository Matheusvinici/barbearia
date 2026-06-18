<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Servico;
use App\Models\Configuracao;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Configuracao::set('horario_abertura', '08:00');
        Configuracao::set('horario_fechamento', '18:00');
        Configuracao::set('intervalo_minutos', '30');
        Configuracao::set('dias_funcionamento', '1,2,3,4,5,6');
        Configuracao::set('nome_barbearia', 'Minha Barbearia');
        Configuracao::set('telefone', '(87) 9xxxx-xxxx');
        Configuracao::set('endereco', 'Juazeiro-BA');
        Configuracao::set('whatsapp_bot_token', '');

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
        ]);

        Servico::create(['nome' => 'Corte Social', 'preco' => 35.00, 'duracao_minutos' => 30]);
        Servico::create(['nome' => 'Corte Degradê', 'preco' => 45.00, 'duracao_minutos' => 40]);
        Servico::create(['nome' => 'Barba', 'preco' => 25.00, 'duracao_minutos' => 20]);
        Servico::create(['nome' => 'Corte + Barba', 'preco' => 55.00, 'duracao_minutos' => 50]);
        Servico::create(['nome' => 'Hidratação', 'preco' => 40.00, 'duracao_minutos' => 30]);
        Servico::create(['nome' => 'Sobrancelha', 'preco' => 15.00, 'duracao_minutos' => 10]);
    }
}
