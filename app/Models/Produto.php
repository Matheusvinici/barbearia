<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbearia_id',
        'nome',
        'categoria',
        'descricao',
        'preco',
        'custo',
        'estoque',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'custo' => 'decimal:2',
            'estoque' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }

    public function vendas()
    {
        return $this->belongsToMany(Venda::class, 'venda_produto')
            ->withPivot('quantidade', 'preco_unitario', 'subtotal')
            ->withTimestamps();
    }
}