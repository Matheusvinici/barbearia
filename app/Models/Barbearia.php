<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Barbearia extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barbearia) {
            if (empty($barbearia->slug) && $barbearia->nome) {
                $barbearia->slug = Str::slug($barbearia->nome);
                $base = $barbearia->slug;
                $counter = 1;
                while (static::where('slug', $barbearia->slug)->exists()) {
                    $barbearia->slug = $base . '-' . $counter++;
                }
            }
        });
    }

    protected $fillable = [
        'parent_id',
        'nome',
        'slug',
        'descricao',
        'bairro',
        'cidade',
        'logo',
        'background_image',
        'owner_id',
        'horario_abertura',
        'horario_fechamento',
        'intervalo_minutos',
        'dias_funcionamento',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function despesas()
    {
        return $this->hasMany(Despesa::class);
    }

    public function bloqueios()
    {
        return $this->hasMany(BloqueioAgenda::class);
    }

    public function caixas()
    {
        return $this->hasMany(Caixa::class);
    }

    public function isMatriz()
    {
        return is_null($this->parent_id);
    }

    public function tenantTreeIds(): array
    {
        if ($this->isMatriz()) {
            return $this->children()->pluck('id')->push($this->id)->all();
        }
        return $this->parent?->tenantTreeIds() ?? [$this->id];
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/logo.jpg');
    }

    public function getBackgroundUrlAttribute()
    {
        return $this->background_image ? asset('storage/' . $this->background_image) : asset('images/frenteBarbearia.jpg');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }

    public function avaliacoesAprovadas()
    {
        return $this->hasMany(Avaliacao::class)->whereNotNull('resposta');
    }
}
