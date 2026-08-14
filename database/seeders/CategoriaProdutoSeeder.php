<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CategoriaProduto;

class CategoriaProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Cabelo', 'ordem' => 1],
            ['nome' => 'Barba', 'ordem' => 2],
            ['nome' => 'Moda', 'ordem' => 3],
            ['nome' => 'Perfumaria', 'ordem' => 4],
            ['nome' => 'Acessórios', 'ordem' => 5],
            ['nome' => 'Higiene', 'ordem' => 6],
            ['nome' => 'Kits', 'ordem' => 7],
            ['nome' => 'Outros', 'ordem' => 8],
        ];

        foreach ($categorias as $categoria) {
            CategoriaProduto::firstOrCreate(['nome' => $categoria['nome']], $categoria);
        }
    }
}