<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoServicoQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'plano_id',
        'servico_id',
        'quantidade',
    ];

    protected $table = 'plano_servico_quotas';

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
