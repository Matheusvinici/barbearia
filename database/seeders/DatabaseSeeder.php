<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Mediador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
        ]);

        $this->command->info('Admin criado: admin@admin.com / 123456');
    }
}
