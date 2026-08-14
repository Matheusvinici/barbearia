<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProduto extends Model
{
    protected $table = 'categorias_produtos';

    protected $fillable = ['nome', 'ordem', 'ativo'];

    protected $casts = [
        'ativo' => 'boolean',
    ];
}