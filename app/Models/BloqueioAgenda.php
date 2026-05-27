<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueioAgenda extends Model
{
    use HasFactory;

    protected $table = 'bloqueio_agendas';

    protected $fillable = [
        'barbeiro_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'motivo',
        'recorrente',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'hora_inicio' => 'datetime:H:i',
            'hora_fim' => 'datetime:H:i',
            'recorrente' => 'boolean',
        ];
    }

    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class);
    }
}
