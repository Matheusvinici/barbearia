<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Historia extends Model
{
    protected $fillable = [
        'aluno_id',
        'status',
        'prompt_gerado',
        'resposta_gemini',
        'pdf_path',
        'slug',
        'qr_code_path',
    ];

    protected function casts(): array
    {
        return [
            'resposta_gemini' => 'array',
        ];
    }

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(RespostaAluno::class);
    }
}
