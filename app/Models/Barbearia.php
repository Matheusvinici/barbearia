<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barbearia extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'nome',
        'descricao',
        'bairro',
        'cidade',
    ];

    public function barbeiros()
    {
        return $this->hasMany(Barbeiro::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }

    public function parent()
    {
        return $this->belongsTo(Barbearia::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Barbearia::class, 'parent_id');
    }

    public function filiais()
    {
        return $this->hasMany(Barbearia::class, 'parent_id');
    }

    public function isMatriz()
    {
        return is_null($this->parent_id);
    }
}
