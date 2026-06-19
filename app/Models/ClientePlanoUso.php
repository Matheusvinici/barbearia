<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientePlanoUso extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_plano_id',
        'agendamento_id',
        'servico_id',
        'usado_em',
    ];

    protected $table = 'cliente_plano_usos';

    protected function casts(): array
    {
        return [
            'usado_em' => 'datetime',
        ];
    }

    public function clientePlano()
    {
        return $this->belongsTo(ClientePlano::class);
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
