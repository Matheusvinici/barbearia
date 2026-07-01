<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->foreignId('barbearia_id')->nullable()->constrained('barbearias')->nullOnDelete()->after('id');
            $table->dropUnique('caixas_data_unique');
            $table->unique(['data', 'barbearia_id']);
        });

        Schema::table('caixa_movimentacoes', function (Blueprint $table) {
            $table->foreignId('barbearia_id')->nullable()->constrained('barbearias')->nullOnDelete()->after('caixa_id');
        });
    }

    public function down(): void
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->dropUnique(['data', 'barbearia_id']);
            $table->unique('data');
            $table->dropForeign(['barbearia_id']);
            $table->dropColumn('barbearia_id');
        });

        Schema::table('caixa_movimentacoes', function (Blueprint $table) {
            $table->dropForeign(['barbearia_id']);
            $table->dropColumn('barbearia_id');
        });
    }
};
