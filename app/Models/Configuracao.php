<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor'];

    public static function get(string $chave, $default = null): ?string
    {
        try {
            $config = static::where('chave', $chave)->first();
            return $config?->valor ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function set(string $chave, $valor): void
    {
        try {
            static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
        } catch (\Exception $e) {
        }
    }
}
