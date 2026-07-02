<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'barbearia_id',
        'cliente_id',
        'agendamento_id',
        'cliente_nome',
        'rating',
        'comentario',
        'resposta',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'responded_at' => 'datetime',
        ];
    }

    public function barbearia()
    {
        return $this->belongsTo(Barbearia::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }
}
