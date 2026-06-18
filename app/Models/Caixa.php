<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'saldo_inicial',
        'total_entradas',
        'total_saidas',
        'saldo_final',
        'fechado',
        'observacoes',
        'user_id_abertura',
        'user_id_fechamento',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date:Y-m-d',
            'saldo_inicial' => 'decimal:2',
            'total_entradas' => 'decimal:2',
            'total_saidas' => 'decimal:2',
            'saldo_final' => 'decimal:2',
            'fechado' => 'boolean',
        ];
    }

    public function movimentacoes()
    {
        return $this->hasMany(CaixaMovimentacao::class);
    }

    public function usuarioAbertura()
    {
        return $this->belongsTo(User::class, 'user_id_abertura');
    }

    public function usuarioFechamento()
    {
        return $this->belongsTo(User::class, 'user_id_fechamento');
    }
}
