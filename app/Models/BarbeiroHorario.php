<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarbeiroHorario extends Model
{
    use HasFactory;

    protected $fillable = [
        'barbeiro_id',
        'dia_semana',
        'periodo',
        'hora_inicio',
        'hora_fim',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function barbeiro()
    {
        return $this->belongsTo(Barbeiro::class);
    }
}
