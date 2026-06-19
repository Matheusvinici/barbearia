<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function quotas()
    {
        return $this->hasMany(PlanoServicoQuota::class);
    }

    public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'plano_servico_quotas')
            ->withPivot('quantidade')
            ->withTimestamps();
    }

    public function clientes()
    {
        return $this->hasMany(ClientePlano::class);
    }
}
