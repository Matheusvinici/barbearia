<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaixaMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'caixa_movimentacoes';

    protected $fillable = [
        'barbearia_id',
        'caixa_id',
        'tipo',
        'valor',
        'descricao',
        'origem_type',
        'origem_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
        ];
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }
}
